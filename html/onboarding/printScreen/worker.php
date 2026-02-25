<?php
// CLI worker para processar capturas em fila
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "Este worker deve ser executado via CLI.\n";
    exit(1);
}

$base_dir = __DIR__ . "/Files";
$queue_dir = $base_dir . "/queue";
$status_dir = $base_dir . "/status";
$jobs_dir = $base_dir . "/jobs";

$docker_path = '/usr/bin/docker';
$image_name = 'harbor.ixcsoft.com.br/papaya/fastprint:latest';
$max_seconds = (int)(getenv('WORKER_MAX_SECONDS') ?: 900);

function ensure_dir($dir) {
    if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
        echo "Erro: nao foi possivel criar a pasta: " . $dir . "\n";
        exit(1);
    }
}

function sanitize_job_id($id) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '', (string)$id);
}

function load_status($status_path) {
    if (!file_exists($status_path)) {
        return [];
    }
    $data = json_decode(file_get_contents($status_path), true);
    return is_array($data) ? $data : [];
}

function save_status($status_path, array $updates) {
    $current = load_status($status_path);
    $merged = array_merge($current, $updates);
    file_put_contents($status_path, json_encode($merged, JSON_UNESCAPED_SLASHES));
}

function add_dir_to_zip($dir, $zip, $base_path_len) {
    $items = scandir($dir);
    if ($items === false) {
        return;
    }
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $full_path = $dir . DIRECTORY_SEPARATOR . $item;
        $relative_path = substr($full_path, $base_path_len + 1);
        if (is_dir($full_path)) {
            $zip->addEmptyDir($relative_path);
            add_dir_to_zip($full_path, $zip, $base_path_len);
        } else {
            $zip->addFile($full_path, $relative_path);
        }
    }
}

function build_docker_command($docker_path, $image_name, $job, $mask_sensitive = false) {
    $url = $job['url_central'];
    $login = $mask_sensitive ? '***' : $job['login_central'];
    $senha = $mask_sensitive ? '***' : $job['senha_central'];
    $device = $job['device_type'];
    $output_dir = $job['output_dir_host'];

    $job_id = $job['id'] ?? 'job';
    $container_name = 'fastprint_' . $job_id;
    return sprintf(
        '%s run --rm --pull=always --name %s ' .
        '-e URL_CENTRAL=%s ' .
        '-e LOGIN_CENTRAL=%s ' .
        '-e SENHA_CENTRAL=%s ' .
        '-e deviceType=%s ' .
        '-e DEVICE_TYPE=%s ' .
        '-e CYPRESS_DEVICE_TYPE=%s ' .
        '-v %s:%s ' .
        '%s',
        $docker_path,
        escapeshellarg($container_name),
        escapeshellarg($url),
        escapeshellarg($login),
        escapeshellarg($senha),
        escapeshellarg($device),
        escapeshellarg($device),
        escapeshellarg($device),
        escapeshellarg($output_dir), '/cypress/screenshots',
        escapeshellarg($image_name)
    );
}

ensure_dir($base_dir);
ensure_dir($queue_dir);
ensure_dir($status_dir);
ensure_dir($jobs_dir);

$once = in_array('--once', $argv, true);
$sleep_seconds = 2;

while (true) {
    $jobs = glob($queue_dir . "/*.json");
    if ($jobs === false || count($jobs) === 0) {
        if ($once) {
            exit(0);
        }
        sleep($sleep_seconds);
        continue;
    }

    sort($jobs);
    $job_file = $jobs[0];
    $job_id = sanitize_job_id(basename($job_file, '.json'));
    if ($job_id === '') {
        @unlink($job_file);
        continue;
    }

    $processing_file = $queue_dir . "/" . $job_id . ".processing";
    if (!@rename($job_file, $processing_file)) {
        continue;
    }

    $job = json_decode(@file_get_contents($processing_file), true);
    if (!is_array($job)) {
        $status_path = $status_dir . "/" . $job_id . ".json";
        save_status($status_path, [
            'status' => 'error',
            'message' => 'Job invalido.',
            'finished_at' => date('c'),
        ]);
        @unlink($processing_file);
        continue;
    }

    $status_path = $status_dir . "/" . $job_id . ".json";
    save_status($status_path, [
        'status' => 'running',
        'message' => 'Executando capturas...',
        'started_at' => date('c'),
    ]);

    $job_dir = $jobs_dir . "/" . $job_id;
    $output_dir_container = $job['output_dir_container'] ?? ($job_dir . "/screenshots");
    ensure_dir($job_dir);
    ensure_dir($output_dir_container);

    $container_name = 'fastprint_' . $job_id;
    @shell_exec($docker_path . ' rm -f ' . escapeshellarg($container_name) . ' >/dev/null 2>&1');

    $docker_command = build_docker_command($docker_path, $image_name, $job, false);
    $docker_command_log = build_docker_command($docker_path, $image_name, $job, true);

    $log_path = $job_dir . "/docker.log";
    $log_header = "=== " . date('c') . " ===\n" .
        "Comando: " . $docker_command_log . "\n" .
        "Status: running\n" .
        "Output:\n";
    @file_put_contents($log_path, $log_header, FILE_APPEND);

    $descriptorspec = [
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($docker_command, $descriptorspec, $pipes);
    if (!is_resource($process)) {
        save_status($status_path, [
            'status' => 'error',
            'message' => 'Erro ao iniciar o Docker.',
            'finished_at' => date('c'),
        ]);
        @file_put_contents($log_path, "Erro ao iniciar o Docker.\n", FILE_APPEND);
        @unlink($processing_file);
        continue;
    }

    stream_set_blocking($pipes[1], false);
    stream_set_blocking($pipes[2], false);

    $return_status = 0;
    $timed_out = false;
    $start_time = time();
    $running = true;
    $last_status = null;
    $cancel_path = $job_dir . "/cancel";
    $canceled = false;
    while ($running) {
        if (file_exists($cancel_path)) {
            $canceled = true;
            @file_put_contents($log_path, "\nCancelado pelo usuario.\n", FILE_APPEND);
            proc_terminate($process);
            @shell_exec($docker_path . ' rm -f ' . escapeshellarg($container_name) . ' >/dev/null 2>&1');
            break;
        }
        if (time() - $start_time > $max_seconds) {
            $timed_out = true;
            @file_put_contents($log_path, "\nTimeout: excedeu " . $max_seconds . " segundos.\n", FILE_APPEND);
            proc_terminate($process);
            @shell_exec($docker_path . ' rm -f ' . escapeshellarg($container_name) . ' >/dev/null 2>&1');
            break;
        }
        $read = [$pipes[1], $pipes[2]];
        $write = null;
        $except = null;
        $changed = @stream_select($read, $write, $except, 1);
        if ($changed === false) {
            break;
        }
        foreach ($read as $r) {
            $data = stream_get_contents($r);
            if ($data !== false && $data !== '') {
                @file_put_contents($log_path, $data, FILE_APPEND);
            }
        }
        $status = proc_get_status($process);
        $last_status = $status;
        $running = $status['running'];
    }

    foreach ($pipes as $pipe) {
        if (is_resource($pipe)) {
            $data = stream_get_contents($pipe);
            if ($data !== false && $data !== '') {
                @file_put_contents($log_path, $data, FILE_APPEND);
            }
            fclose($pipe);
        }
    }
    $return_status = proc_close($process);
    if ($timed_out) {
        $return_status = 124;
    } else if ($canceled) {
        $return_status = 130;
    } else {
        $exit_code = -1;
        if (is_array($last_status) && array_key_exists('exitcode', $last_status)) {
            $exit_code = $last_status['exitcode'];
        }
        if ($return_status === -1 && $exit_code !== -1) {
            $return_status = $exit_code;
        }
        if ($return_status === -1 && $exit_code === -1) {
            // Fallback: assume sucesso se o processo terminou e nao houve timeout
            $return_status = 0;
        }
    }
    @file_put_contents($log_path, "\nStatus: " . $return_status . "\n\n", FILE_APPEND);

    if ($canceled) {
        save_status($status_path, [
            'status' => 'canceled',
            'message' => 'Execucao cancelada pelo usuario.',
            'finished_at' => date('c'),
        ]);
        @unlink($processing_file);
        continue;
    }

    if ($return_status !== 0) {
        save_status($status_path, [
            'status' => 'error',
            'message' => $timed_out ? 'Timeout ao executar o Docker. Verifique o log.' : 'Erro ao executar o Docker. Verifique o log.',
            'finished_at' => date('c'),
        ]);
        @unlink($processing_file);
        continue;
    }

    $zip_path = $job_dir . "/capturas.zip";
    $zip = new ZipArchive();
    if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        save_status($status_path, [
            'status' => 'error',
            'message' => 'Erro ao criar o ZIP.',
            'finished_at' => date('c'),
        ]);
        @unlink($processing_file);
        continue;
    }
    add_dir_to_zip($output_dir_container, $zip, strlen($output_dir_container));
    $zip->close();

    if (!file_exists($zip_path)) {
        save_status($status_path, [
            'status' => 'error',
            'message' => 'ZIP nao foi gerado.',
            'finished_at' => date('c'),
        ]);
        @unlink($processing_file);
        continue;
    }

    save_status($status_path, [
        'status' => 'done',
        'message' => 'Concluido.',
        'finished_at' => date('c'),
    ]);

    @unlink($processing_file);

    if ($once) {
        exit(0);
    }
}
?>
