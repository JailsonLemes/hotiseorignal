require('dotenv').config();

describe('Screenshots app central assinante', () => {

  const max_screenshots = 12;
  const animation_wait = 1500;
  
  // 1. Lista com TODAS as resoluções possíveis, agora com a propriedade 'type'
  const allResolutions = [
    { aspect: 'cel', width: 504, height: 896, scale: 1.5, type: 'android' },
    { aspect: 'tab10', width: 880, height: 1408, scale: 1.5, type: 'android' },
    { aspect: 'iphone', width: 430, height: 932, scale: 8.0, type: 'ios' },
    { aspect: 'ipad', width: 1024, height: 1366, scale: 3.0, type: 'ios' },
  ];
  
  // 2. Lê a variável de ambiente passada pelo script .sh
  // Se não for definida, o padrão é 'both' (ambos)
  const deviceType = Cypress.env('DEVICE_TYPE') || 'both';

  // 3. Filtra a lista de resoluções com base na escolha do usuário
  const resolutions = allResolutions.filter(res => {
    if (deviceType === 'both') {
      return true; // Inclui todas se a escolha for 'both'
    }
    return res.type === deviceType; // Inclui apenas as que correspondem a 'ios' ou 'android'
  });
  
  const url_central = Cypress.env('URL_CENTRAL') || '';
  const login_central = Cypress.env('LOGIN_CENTRAL') || '';
  const senha_central = Cypress.env('SENHA_CENTRAL') || '';

  Cypress.on('uncaught:exception', (err, runnable) => {
    return false; // Ignora todos os erros de JS da aplicação
  });

  // 4. O forEach agora executa apenas sobre a lista de resoluções JÁ FILTRADA
  resolutions.forEach(res => {
    let count = 0;

    it(`Screenshots app central assinante ('${res.aspect}')`, () => {
      cy.dpiAndResize(res.scale, res.width, res.height);
      cy.visit(url_central);

      // Lógica condicional para os diferentes fluxos da aplicação
      if (url_central.includes('/central_assinante_web')) {
        // --- FLUXO PARA A APLICAÇÃO ANTIGA ---
        cy.log('URL da versão ANTIGA detectada.');
        
        let menuItemsToScreenshot = [
          { name: 'pg_fatura', selector: '#pg_fatura' },
          { name: 'pg_plano', selector: '#pg_plano' },
          { name: 'pg_relatorios', selector: '#pg_relatorios', useDoubleCheck: true },
          { name: 'pg_consumo', selector: '#pg_consumo' },
          { name: 'pg_atendimento', selector: '#pg_atendimento' },
          { name: 'pg_nota', selector: '#pg_nota' },
          { name: 'pg_connections', selector: '#pg_connections' },
          { name: 'pg_config', selector: '#pg_config', useDoubleCheck: true },
        ];

        cy.printPgLoginAntiga(res.aspect);
        if (++count === max_screenshots) return;

        cy.loginCentralAntiga(res.aspect, login_central, senha_central);
        
        cy.printPgPrincipal(res.aspect, animation_wait);
        if (++count === max_screenshots) return;
        
        if (res.aspect !== 'ipad') {
          cy.printMenuLateral(res.aspect);
          if (++count === max_screenshots) return;
        }
        
        for (const item of menuItemsToScreenshot) {
          cy.get('body').then($body => {
            if ($body.find(item.selector).length > 0) {
              if (item.useDoubleCheck) {
                cy.printPgMenusWithDoubleCheck(item.name, item.selector, res.aspect, animation_wait);
              } else {
                cy.printPgMenus(item.name, item.selector, res.aspect, animation_wait);
              }
            } else {
              cy.log(`AVISO: Item de menu '${item.name}' não encontrado. Pulando.`);
            }
          });
        }

      } else if (url_central.includes('/central-assinante')) {
        // --- FLUXO PARA A APLICAÇÃO NOVA ---
        cy.log('URL da versão NOVA detectada.');
        
        let menuItemsToScreenshot = [
          { name: 'faturas', selector: '#sidebar-btn-billing' },
          { name: 'meu-plano', selector: '#sidebar-btn-plan' },
          { name: 'suporte', selector: '#sidebar-btn-support' },
          { name: 'dispositivos-conexoes', selector: '#sidebar-btn-device' },
          { name: 'notas-fiscais', selector: '#sidebar-btn-invoice' },
          { name: 'relatorios', selector: '#sidebar-btn-report > span' },
          { name: 'consumo', selector: '#sidebar-btn-consumption > span' },
        ];
        
        cy.printPgLoginNova(res.aspect);
        if (++count === max_screenshots) return;

        cy.enterUsernameNova(login_central);
        cy.screenshot(`${res.aspect}_pg_senha`, { capture: 'viewport' });
        if (++count === max_screenshots) return;
        cy.enterPasswordNova(senha_central);

        cy.printPgPrincipal(res.aspect, animation_wait);
        if (++count === max_screenshots) return;
        
        if (res.aspect !== 'ipad' && res.aspect !== 'tab10') {
            cy.printMenuLateral(res.aspect);
            if (++count === max_screenshots) return;
        }
        
        for (const item of menuItemsToScreenshot) {
          cy.get('body').then($body => {
            if ($body.find(item.selector).length > 0) {
              if (item.useDoubleCheck) {
                cy.printPgMenusWithDoubleCheck(item.name, item.selector, res.aspect, animation_wait);
              } else {
                cy.printPgMenus(item.name, item.selector, res.aspect, animation_wait);
              }
            } else {
              cy.log(`AVISO: Item de menu '${item.name}' não encontrado. Pulando.`);
            }
          });
        }

      } else {
        cy.log('AVISO: A URL não corresponde a nenhum padrão conhecido.');
      }
    });
  });
});