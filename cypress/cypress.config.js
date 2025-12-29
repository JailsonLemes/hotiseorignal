// cypress.config.js — versão final com resize interno e log limpo
const { defineConfig } = require('cypress');
const sharp = require('sharp');
const fs = require('fs');
const path = require('path');

const screenshotsDir = path.join(__dirname, 'screenshots');
const specScreenshotsDir = path.join(screenshotsDir, 'mobile_screenshots.cy.js');

function ensureDir(dirPath) {
  if (!fs.existsSync(dirPath)) {
    fs.mkdirSync(dirPath, { recursive: true });
  }
}

ensureDir(screenshotsDir);
ensureDir(specScreenshotsDir);

// Tamanhos-alvo para cada tipo de dispositivo
const targets = {
  iphone: { width: 1284, height: 2778 },
  ipad: { width: 2048, height: 2732 },
  cel: { width: 389, height: 691 },
  tab10: { width: 1185, height: 1896 },
};

// Função auxiliar para obter tamanho atual de uma imagem
async function getImageSize(filePath) {
  try {
    const metadata = await sharp(filePath).metadata();
    return { width: metadata.width, height: metadata.height };
  } catch {
    return null;
  }
}

// Redimensiona imagem e verifica resultado
async function resizeAndVerify(filePath, width, height) {
  const ext = path.extname(filePath).toLowerCase();

  const resize = async () => {
    const pipeline = sharp(filePath, { density: 72 })
      .resize(width, height, {
        fit: 'fill',
        kernel: sharp.kernel.lanczos3,
        withoutEnlargement: false,
      });

    const output =
      ext === '.png'
        ? pipeline.png({ compressionLevel: 9 })
        : pipeline.jpeg({ quality: 95 });

    const buffer = await output.toBuffer();
    fs.writeFileSync(filePath, buffer);

    const meta = await getImageSize(filePath);
    if (!meta) return false;

    if (meta.width !== width || meta.height !== height) {
      // Se ainda não bate, refaz o processo
      return await resize();
    }
    return true;
  };

  return await resize();
}

// Espera o Cypress terminar de gravar todos os screenshots
function waitForStableScreenshots(timeoutMs = 8000) {
  return new Promise((resolve) => {
    console.log('⌛ Aguardando finalização das capturas...');
    setTimeout(resolve, timeoutMs);
  });
}

module.exports = defineConfig({
  e2e: {
    screenshotsFolder: 'screenshots/',
    specPattern: 'e2e/*.cy.js',
    supportFile: 'support/e2e.js',

    setupNodeEvents(on, config) {
      config.env.URL_CENTRAL = process.env.URL_CENTRAL;
      config.env.LOGIN_CENTRAL = process.env.LOGIN_CENTRAL;
      config.env.SENHA_CENTRAL = process.env.SENHA_CENTRAL;

      // 🚀 Redimensionamento automático após os testes
      on('after:run', async () => {
        console.log('\n📸 Testes concluídos. Iniciando verificação de screenshots...');
        await waitForStableScreenshots();

        if (!fs.existsSync(specScreenshotsDir)) {
          console.log('⚠️ Nenhum screenshot encontrado.');
          return;
        }

        const files = fs.readdirSync(specScreenshotsDir);
        let resizedCount = 0;

        for (const file of files) {
          const aspect = Object.keys(targets).find((k) => file.startsWith(k));
          if (!aspect) continue;

          const { width, height } = targets[aspect];
          const filePath = path.join(specScreenshotsDir, file);

          const meta = await getImageSize(filePath);
          if (!meta) continue;

          // Só redimensiona se o tamanho estiver errado
          if (meta.width !== width || meta.height !== height) {
            await resizeAndVerify(filePath, width, height);
            resizedCount++;
            console.log(`✔ Corrigido: ${file} (${meta.width}x${meta.height} → ${width}x${height})`);
          }
        }

        if (resizedCount === 0) {
          console.log('✅ Nenhum redimensionamento necessário. Todas as imagens já estão corretas.');
        } else {
          console.log(`\n✅ ${resizedCount} imagem(ns) redimensionada(s) com sucesso.`);
        }
      });

      return config;
    },
  },
});
