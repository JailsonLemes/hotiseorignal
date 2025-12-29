// cypress/support/commands.js - VERSÃO CORRIGIDA

Cypress.Commands.add('dpiAndResize', (scaleFactor, width, height) => {
  cy.viewport(width, height);
  cy.wrap(Cypress.automation('remote:debugger:protocol', {
    command: 'Emulation.setDeviceMetricsOverride',
    params: {
      deviceScaleFactor: scaleFactor,
      width: width,
      height: height,
      mobile: true,
    },
  }));
});

// ... (comandos de login permanecem os mesmos) ...

Cypress.Commands.add('printPgLoginAntiga', (aspect) => {
  cy.get('img.img-logo', { timeout: 30000 }).should('have.prop', 'naturalWidth').and('be.gt', 0)
    .then(() => cy.screenshot(`${aspect}_pg_login`, { capture: 'viewport' }));
});

Cypress.Commands.add('loginCentralAntiga', (aspect, login_central, senha_central) => {
  if (aspect.startsWith('cel') || aspect === 'iphone') {
    cy.get('.login-mobile-usuario > input').type(login_central);
  } else {
    cy.get('input.login').first().type(login_central);
  }
  cy.document().then(doc => {
    const elemento = doc.querySelector('.tipo_login_senha input[type="password"]')
    if (elemento) {
      cy.get('.tipo_login_senha input[type="password"]').type(senha_central, { log: false })
    } else {
      cy.log('Campo de senha não encontrado. Ação ignorada!');
    }
  })
  cy.contains('button', 'Entrar').click()
});


Cypress.Commands.add('printPgLoginNova', (aspect) => {
  cy.contains('button', 'Entrar', { timeout: 30000 }).should('be.visible');
  cy.screenshot(`${aspect}_pg_login`, { capture: 'viewport' });
});

Cypress.Commands.add('enterUsernameNova', (username) => {
  cy.get('input[placeholder="Digite o seu login"]').type(username);
  cy.contains('button', 'Entrar').click({ force: true });
  cy.url().should('include', '/senha');
});

Cypress.Commands.add('enterPasswordNova', (password) => {
  cy.get('input[placeholder="Digite a sua senha"]', { timeout: 10000 }).type(password, { log: false, force: true });
  cy.contains('button', 'Entrar').click({ force: true });
});


// --- COMANDOS UNIVERSAIS (PÓS-LOGIN) ---

Cypress.Commands.add('printPgPrincipal', (aspect, animation_wait = 1000) => {
  // Seletor Universal Final: Procura por um elemento de qualquer uma das versões.
  // CORREÇÃO: Adicionados os seletores '.logo' e '.dashboard > .row'
  const seletorUniversal = `
    .usuario .navbar-toggle, 
    .align-middle, 
    #navbarDropdownMenuLink > .nome, 
    .logo, 
    .dashboard > .row
  `;

  // Filtra para encontrar qual dos seletores está visível e usa o primeiro que encontrar.
  cy.get(seletorUniversal, { timeout: 30000 }).filter(':visible').first().should('be.visible')
    .then(() => {
      cy.get('body').then(($body) => { $body.css('overflow', 'hidden'); });
      cy.get('body').then($body => {
        if ($body.find('#loader').length > 0) {
          cy.get('#loader', { timeout: 10000 }).should('not.be.visible');
        } else {
          cy.log('Loader não encontrado, prosseguindo...');
        }
      });
      cy.wait(animation_wait);
      cy.screenshot(`${aspect}_pg_principal`, { capture: 'viewport' });
    });
});

// ... (outros comandos de menu permanecem os mesmos) ...

Cypress.Commands.add('printMenuLateral', (aspect) => {
  const seletorMenuUniversal = '.usuario .navbar-toggle, #sidebarToggleTop > img';
  cy.get('body').then($body => {
    if ($body.find(seletorMenuUniversal).length > 0) {
      cy.get(seletorMenuUniversal).first().click({ force: true });
      cy.wait(300);
    }
  });
  cy.screenshot(aspect + '_pg_menu_lateral', { capture: 'viewport' });
});

Cypress.Commands.add('printPgMenus', (name, selector, aspect, animation_wait = 1000) => {
  const seletorMenuUniversal = '.usuario .navbar-toggle, #sidebarToggleTop > img';
  cy.get('body').then($body => {
    if ($body.find(seletorMenuUniversal).length > 0) {
      cy.get(seletorMenuUniversal).first().click({ force: true });
    }
  });
  cy.get(selector).click({ force: true });
  cy.wait(animation_wait);
  cy.screenshot(`${aspect}_${name}`, { capture: 'viewport' });
});

Cypress.Commands.add('printPgMenusWithDoubleCheck', (name, selector, aspect, animation_wait = 1000) => {
  const seletorMenuUniversal = '.usuario .navbar-toggle, #sidebarToggleTop > img';
  cy.get('body').then($body => {
    if ($body.find(seletorMenuUniversal).length > 0) {
      cy.get(seletorMenuUniversal).first().click({ force: true });
    }
  });
  cy.get(selector).last().click({ force: true });
  cy.wait(animation_wait);
  cy.screenshot(`${aspect}_${name}`, { capture: 'viewport' });
});