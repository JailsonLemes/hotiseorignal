// Removemos a função pasteToAreaTransfer se não for mais usada em outros lugares.
// Removemos o listener para labels de input/textarea se não forem mais usados.

// Lógica para os checkboxes de plataforma
const checkAndroid = document.getElementById('input-check-android');
const checkIOS = document.getElementById('input-check-ios');
const btnAmbos = document.getElementById('btn-ambos');
const btnVerLogs = document.getElementById('btn-ver-logs');
const logWrapper = document.getElementById('log-wrapper');
const logPanel = document.getElementById('log-panel');
const logRefresh = document.getElementById('btn-log-refresh');
const logJobLabel = document.getElementById('log-job-label');
const jobStatus = document.getElementById('job-status');
const btnCancel = document.getElementById('btn-cancel');
const captureForm = document.getElementById('capture-form');
const btnGerar = document.getElementById('btn-gerar');
let currentJobId = '';
let statusInterval = null;
let logInterval = null;

btnAmbos.addEventListener('click', () => {
    checkAndroid.checked = true;
    checkIOS.checked = true;
    // Adiciona feedback visual se desejar (opcional)
    document.querySelectorAll('.platform-label').forEach(label => label.style.opacity = '1');
});

// Adiciona listener para desmarcar 'Ambos' implicitamente se um for deselecionado
// (Embora não haja um checkbox 'Ambos', isso garante a lógica correta no backend)
// E ajusta a opacidade
document.querySelectorAll('.platform-check').forEach(checkbox => {
    checkbox.addEventListener('change', () => {
        // Atualiza opacidade baseado no estado atual
        const label = document.querySelector(`label[for="${checkbox.id}"]`);
        if (label) {
            label.style.opacity = checkbox.checked ? '1' : '0.3';
        }
    });
});

// Inicializa a opacidade correta no carregamento da página
document.querySelectorAll('.platform-check').forEach(checkbox => {
    const label = document.querySelector(`label[for="${checkbox.id}"]`);
    if (label) {
        label.style.opacity = checkbox.checked ? '1' : '0.3';
    }
});

function getLastJobId() {
    try {
        return localStorage.getItem('last_capture_job_id') || '';
    } catch (e) {
        return '';
    }
}

function updateLogLabel(jobId) {
    if (!logJobLabel) return;
    logJobLabel.textContent = jobId ? `Job recente: ${jobId}` : 'Nenhum job recente.';
}

async function fetchLog(jobId) {
    if (!jobId) return;
    try {
        const res = await fetch(`/onboarding/printScreen/gerar_capturas.php?download_log=${jobId}`, { cache: 'no-store' });
        if (res.status === 404) {
            logPanel.textContent = 'Log ainda nao disponivel. Tentando novamente...';
            return;
        }
        if (!res.ok) throw new Error('Falha ao carregar log.');
        const text = await res.text();
        logPanel.textContent = text || 'Log vazio.';
    } catch (e) {
        logPanel.textContent = 'Nao foi possivel carregar o log.';
    }
}

function toggleLogs() {
    if (!logWrapper || !logPanel) return;
    const jobId = getLastJobId();
    if (!jobId) {
        if (typeof toast === 'function') {
            toast('Nenhum job recente para exibir.', { type: 'warning' });
        }
        return;
    }
    updateLogLabel(jobId);
    const isHidden = logWrapper.style.display === 'none' || logWrapper.style.display === '';
    if (isHidden) {
        logWrapper.style.display = 'block';
        btnVerLogs.textContent = 'Ocultar logs';
        fetchLog(jobId);
        logInterval = setInterval(() => fetchLog(jobId), 4000);
    } else {
        logWrapper.style.display = 'none';
        btnVerLogs.textContent = 'Ver logs';
        if (logInterval) clearInterval(logInterval);
        logInterval = null;
    }
}

if (btnVerLogs) {
    btnVerLogs.addEventListener('click', toggleLogs);
}

if (logRefresh) {
    logRefresh.addEventListener('click', () => {
        const jobId = getLastJobId();
        if (jobId) fetchLog(jobId);
    });
}

updateLogLabel(getLastJobId());

function setStatus(text) {
    if (jobStatus) jobStatus.textContent = `Status: ${text}`;
}

async function pollStatus(jobId) {
    if (!jobId) return;
    try {
        const res = await fetch(`/onboarding/printScreen/gerar_capturas.php?status=${jobId}`, { cache: 'no-store' });
        if (!res.ok) throw new Error('Falha ao consultar status.');
        const data = await res.json();
        if (data.status === 'done') {
            setStatus('concluido. iniciando download...');
            if (btnCancel) btnCancel.disabled = true;
            if (btnGerar) btnGerar.disabled = false;
            if (statusInterval) clearInterval(statusInterval);
            statusInterval = null;
            downloadZip(data.download_url);
            return;
        }
        if (data.status === 'error') {
            setStatus(`erro: ${data.message || 'falha no processamento.'}`);
            if (btnCancel) btnCancel.disabled = true;
            if (btnGerar) btnGerar.disabled = false;
            if (statusInterval) clearInterval(statusInterval);
            statusInterval = null;
            return;
        }
        if (data.status === 'canceled') {
            setStatus('cancelado.');
            if (btnCancel) btnCancel.disabled = true;
            if (btnGerar) btnGerar.disabled = false;
            if (statusInterval) clearInterval(statusInterval);
            statusInterval = null;
            return;
        }
        setStatus(data.message || 'processando...');
    } catch (e) {
        setStatus('erro ao consultar status.');
    }
}

async function downloadZip(url) {
    try {
        const res = await fetch(url, { cache: 'no-store' });
        if (!res.ok) throw new Error('Falha ao baixar ZIP.');
        const blob = await res.blob();
        const blobUrl = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = blobUrl;
        a.download = 'capturas.zip';
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(blobUrl);
        setStatus('download iniciado.');
    } catch (e) {
        setStatus('erro ao baixar ZIP.');
    }
}

async function startCapture(form) {
    const formData = new FormData(form);
    try {
        setStatus('enfileirando...');
        const res = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        });
        if (!res.ok) {
            const text = await res.text();
            throw new Error(text || 'Falha ao iniciar.');
        }
        const data = await res.json();
        currentJobId = data.id;
        try { localStorage.setItem('last_capture_job_id', currentJobId); } catch (e) {}
        updateLogLabel(currentJobId);
        if (btnCancel) btnCancel.disabled = false;
        if (btnGerar) btnGerar.disabled = true;
        if (logWrapper && logWrapper.style.display === 'block') {
            fetchLog(currentJobId);
        }
        if (statusInterval) clearInterval(statusInterval);
        statusInterval = setInterval(() => pollStatus(currentJobId), 3000);
        setStatus('job enfileirado.');
    } catch (e) {
        setStatus('erro ao iniciar.');
        if (btnGerar) btnGerar.disabled = false;
    }
}

if (captureForm) {
    captureForm.addEventListener('submit', (e) => {
        e.preventDefault();
        startCapture(captureForm);
    });
}

if (btnCancel) {
    btnCancel.addEventListener('click', async () => {
        const jobId = currentJobId || getLastJobId();
        if (!jobId) return;
        try {
            setStatus('cancelando...');
            await fetch(`/onboarding/printScreen/gerar_capturas.php?cancel=${jobId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            });
            setStatus('cancelado.');
            btnCancel.disabled = true;
            if (btnGerar) btnGerar.disabled = false;
        } catch (e) {
            setStatus('erro ao cancelar.');
            if (btnGerar) btnGerar.disabled = false;
        }
    });
}
