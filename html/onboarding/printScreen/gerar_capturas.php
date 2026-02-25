<?php
// --- CONFIGURACOES ---
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(0);

$base_dir = __DIR__ . "/Files";
$container_html_path = "/var/www/html";
$host_html_path = getenv('HOST_HTML_PATH') ?: '';
$host_html_path = rtrim($host_html_path, '/');
$queue_dir = $base_dir . "/queue";
$status_dir = $base_dir . "/status";
$jobs_dir = $base_dir . "/jobs";
function is_ajax_request() {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        return true;
    }
    if (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        return true;
    }
    return false;
}

function ensure_dir($dir) {
    if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=UTF-8');
        echo "Erro: Nao foi possivel criar a pasta: " . $dir;
        exit;
    }
}

function remove_path($path) {
    if (!file_exists($path)) {
        return;
    }
    if (is_file($path) || is_link($path)) {
        @unlink($path);
        return;
    }
    $items = scandir($path);
    if ($items === false) {
        return;
    }
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        remove_path($path . DIRECTORY_SEPARATOR . $item);
    }
    @rmdir($path);
}

function cleanup_dir_except($dir, array $keep_names) {
    $items = scandir($dir);
    if ($items === false) {
        return;
    }
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        if (in_array($item, $keep_names, true)) {
            continue;
        }
        remove_path($dir . DIRECTORY_SEPARATOR . $item);
    }
}

function sanitize_job_id($id) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$id);
}

ensure_dir($base_dir);
ensure_dir($queue_dir);
ensure_dir($status_dir);
ensure_dir($jobs_dir);

// --- ENDPOINTS GET ---
if (isset($_GET['cancel'])) {
    $job_id = sanitize_job_id($_GET['cancel']);
    if ($job_id === '') {
        http_response_code(400);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'Job ID invalido.']);
        exit;
    }
    $job_dir = $jobs_dir . "/" . $job_id;
    if (!is_dir($job_dir)) {
        http_response_code(404);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'Job nao encontrado.']);
        exit;
    }
    @file_put_contents($job_dir . "/cancel", "1");
    $status_path = $status_dir . "/" . $job_id . ".json";
    if (file_exists($status_path)) {
        $status = json_decode(file_get_contents($status_path), true);
        if (!is_array($status)) {
            $status = [];
        }
        $status['status'] = 'canceled';
        $status['message'] = 'Execucao cancelada pelo usuario.';
        $status['finished_at'] = date('c');
        file_put_contents($status_path, json_encode($status, JSON_UNESCAPED_SLASHES));
    }
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['ok' => true]);
    exit;
}

if (isset($_GET['download_log'])) {
    $job_id = sanitize_job_id($_GET['download_log']);
    if ($job_id === '') {
        http_response_code(400);
        header('Content-Type: text/plain; charset=UTF-8');
        echo "Job ID invalido.";
        exit;
    }
    $log_path = $jobs_dir . "/" . $job_id . "/docker.log";
    if (!file_exists($log_path)) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo "Log nao encontrado.";
        exit;
    }
    header('Content-Type: text/plain; charset=UTF-8');
    header('Content-Disposition: attachment; filename=\"docker.log\"');
    header('Content-Length: ' . filesize($log_path));
    readfile($log_path);
    exit;
}

if (isset($_GET['download_zip'])) {
    $job_id = sanitize_job_id($_GET['download_zip']);
    if ($job_id === '') {
        http_response_code(400);
        header('Content-Type: text/plain; charset=UTF-8');
        echo "Job ID invalido.";
        exit;
    }
    $zip_path = $jobs_dir . "/" . $job_id . "/capturas.zip";
    if (!file_exists($zip_path)) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo "ZIP nao encontrado.";
        exit;
    }
    $filename = 'capturas.zip';
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($zip_path));
    readfile($zip_path);
    exit;
}

if (isset($_GET['status'])) {
    $job_id = sanitize_job_id($_GET['status']);
    if ($job_id === '') {
        http_response_code(400);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'Job ID invalido.']);
        exit;
    }
    $status_path = $status_dir . "/" . $job_id . ".json";
    if (!file_exists($status_path)) {
        http_response_code(404);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'Status nao encontrado.']);
        exit;
    }
    header('Content-Type: application/json; charset=UTF-8');
    echo file_get_contents($status_path);
    exit;
}

// --- 1. RECEBER E VALIDAR DADOS ---
$url_central = filter_input(INPUT_POST, 'url_central', FILTER_SANITIZE_URL);
$login_central = filter_input(INPUT_POST, 'login_central', FILTER_SANITIZE_STRING);
$senha_central = $_POST['senha_central'] ?? null;
$platforms = $_POST['platform'] ?? [];

if (empty($url_central) || empty($login_central) || empty($senha_central) || empty($platforms) || !filter_var($url_central, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    if (is_ajax_request()) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => 'Falha na validacao.']);
    } else {
        header('Content-Type: text/plain; charset=UTF-8');
        echo "Erro: Falha na validacao.";
    }
    exit;
}
$device_type = (count($platforms) === 2) ? 'both' : $platforms[0];

// --- 2. ENFILEIRAR JOB ---
$job_id = bin2hex(random_bytes(8));
$job_dir_container = $jobs_dir . "/" . $job_id;
$output_dir_container = $job_dir_container . "/screenshots";

$job_dir_host = $job_dir_container;
if ($host_html_path !== '') {
    $job_dir_host = $host_html_path . "/onboarding/printScreen/Files/jobs/" . $job_id;
}
$output_dir_host = $job_dir_host . "/screenshots";
ensure_dir($job_dir_container);
ensure_dir($output_dir_container);
@touch($job_dir_container . "/docker.log");

// Limpa dados antigos para otimizar armazenamento (mantem apenas o job atual)
cleanup_dir_except($jobs_dir, [$job_id]);
cleanup_dir_except($status_dir, [$job_id . ".json"]);
cleanup_dir_except($queue_dir, []);

$job = [
    'id' => $job_id,
    'url_central' => $url_central,
    'login_central' => $login_central,
    'senha_central' => $senha_central,
    'device_type' => $device_type,
    'image_name' => 'harbor.ixcsoft.com.br/papaya/fastprint:latest',
    'output_dir_host' => $output_dir_host,
    'output_dir_container' => $output_dir_container,
    'created_at' => date('c'),
];

$job_path = $queue_dir . "/" . $job_id . ".json";
file_put_contents($job_path, json_encode($job, JSON_UNESCAPED_SLASHES));
@chmod($job_path, 0600);

$status = [
    'id' => $job_id,
    'status' => 'queued',
    'message' => 'Job enfileirado. Aguarde o processamento.',
    'download_url' => $_SERVER['PHP_SELF'] . '?download_zip=' . $job_id,
    'log_url' => $_SERVER['PHP_SELF'] . '?download_log=' . $job_id,
    'created_at' => date('c'),
];

$status_path = $status_dir . "/" . $job_id . ".json";
file_put_contents($status_path, json_encode($status, JSON_UNESCAPED_SLASHES));

// --- 3. RETORNAR PAGINA DE ACOMPANHAMENTO ---
$status_url = $_SERVER['PHP_SELF'] . '?status=' . $job_id;
$download_url = $_SERVER['PHP_SELF'] . '?download_zip=' . $job_id;
$log_url = $_SERVER['PHP_SELF'] . '?download_log=' . $job_id;

if (is_ajax_request()) {
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'id' => $job_id,
        'status_url' => $status_url,
        'download_url' => $download_url,
        'log_url' => $log_url,
        'message' => 'Job enfileirado.',
    ], JSON_UNESCAPED_SLASHES);
    exit;
}

$job_id_js = json_encode($job_id);

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Gerando Capturas</title></head><body>';
echo '<h1>Gerando Capturas...</h1>';
echo '<p>Job ID: <strong>' . htmlspecialchars($job_id) . '</strong></p>';
echo '<p id="status">Aguardando worker...</p>';
echo '<div style="margin-top:12px;">';
echo '  <button id="btn-log" type="button">Ver log</button> ';
echo '  <button id="btn-log-refresh" type="button" style="display:none;">Atualizar log</button>';
echo '</div>';
echo '<pre id="log-box" style="display:none; margin-top:8px; padding:10px; background:#f5f5f5; border:1px solid #ddd; max-height:320px; overflow:auto;"></pre>';
echo '<script>';
echo 'const jobId = ' . $job_id_js . ';';
echo 'try { localStorage.setItem("last_capture_job_id", jobId); } catch (e) {}';
echo 'const statusUrl = ' . json_encode($status_url) . ';';
echo 'const downloadUrl = ' . json_encode($download_url) . ';';
echo 'const logUrl = ' . json_encode($log_url) . ';';
echo 'const statusEl = document.getElementById("status");';
echo 'const logBtn = document.getElementById("btn-log");';
echo 'const logRefreshBtn = document.getElementById("btn-log-refresh");';
echo 'const logBox = document.getElementById("log-box");';
echo 'let logInterval = null;';
echo 'async function fetchLog(){';
echo '  try {';
echo '    const res = await fetch(logUrl, {cache: "no-store"});';
echo '    if (res.status === 404) {';
echo '      logBox.textContent = "Log ainda nao disponivel. Tentando novamente...";';
echo '      return;';
echo '    }';
echo '    if (!res.ok) throw new Error("Falha ao carregar log.");';
echo '    const text = await res.text();';
echo '    logBox.textContent = text || "Log vazio.";';
echo '  } catch (e) {';
echo '    logBox.textContent = "Nao foi possivel carregar o log.";';
echo '  }';
echo '}';
echo 'function toggleLog(){';
echo '  const isHidden = logBox.style.display === "none";';
echo '  if (isHidden) {';
echo '    logBox.style.display = "block";';
echo '    logRefreshBtn.style.display = "inline-block";';
echo '    fetchLog();';
echo '    logInterval = setInterval(fetchLog, 4000);';
echo '    logBtn.textContent = "Ocultar log";';
echo '  } else {';
echo '    logBox.style.display = "none";';
echo '    logRefreshBtn.style.display = "none";';
echo '    if (logInterval) clearInterval(logInterval);';
echo '    logInterval = null;';
echo '    logBtn.textContent = "Ver log";';
echo '  }';
echo '}';
echo 'logBtn.addEventListener("click", toggleLog);';
echo 'logRefreshBtn.addEventListener("click", fetchLog);';
echo 'async function poll(){';
echo '  try {';
echo '    const res = await fetch(statusUrl, {cache: "no-store"});';
echo '    if (!res.ok) throw new Error("Falha ao consultar status.");';
echo '    const data = await res.json();';
echo '    if (data.status === "done") {';
echo '      statusEl.textContent = "Concluido. Iniciando download...";';
echo '      window.location = data.download_url || downloadUrl;';
echo '      return;';
echo '    }';
echo '    if (data.status === "error") {';
echo '      statusEl.textContent = "Erro: " + (data.message || "Falha no processamento.");';
echo '      if (logBox.style.display === "none") { toggleLog(); }';
echo '      return;';
echo '    }';
echo '    statusEl.textContent = data.message || "Processando...";';
echo '  } catch (e) {';
echo '    statusEl.textContent = "Erro ao consultar status. Tentando novamente...";';
echo '  }';
echo '  setTimeout(poll, 3000);';
echo '}';
echo 'poll();';
echo '</script></body></html>';
?>
