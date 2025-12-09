# Silktide Cookie Banner - Plugin WordPress

Plugin WordPress para gerenciamento de consentimento de cookies usando o Silktide Consent Manager.

## Descrição

Este plugin adiciona automaticamente um banner de cookies personalizável em todas as páginas do seu site WordPress. Ele permite que os visitantes gerenciem suas preferências de cookies de acordo com a LGPD/GDPR.

## Características

- ✅ Banner de cookies totalmente personalizável
- ✅ Modal de preferências com tipos de cookies individuais
- ✅ Ícone de cookie para reabrir preferências
- ✅ Armazenamento de preferências no localStorage
- ✅ Callbacks para integração com Google Analytics/Ads
- ✅ Totalmente responsivo
- ✅ Acessível (WCAG)

## Instalação

1. Faça upload da pasta `silktide-cookie-banner` para o diretório `/wp-content/plugins/`
2. Ative o plugin através do menu 'Plugins' no WordPress
3. O banner aparecerá automaticamente em todas as páginas do site

## Estrutura de Arquivos

```
silktide-cookie-banner/
├── silktide-cookie-banner.php    # Arquivo principal do plugin
├── assets/
│   ├── js/
│   │   └── silktide-consent-manager.js    # JavaScript do banner
│   └── css/
│       └── silktide-consent-manager.css   # Estilos do banner
└── README.md                      # Este arquivo
```

## Personalização

Para personalizar o plugin, edite o arquivo `silktide-cookie-banner.php`:

### Alterar Textos

Edite a seção `text` na função `scb_cookie_config()`:

```php
text: {
    banner: {
        description: "Seu texto personalizado aqui...",
        acceptAllButtonText: "Aceitar",
        // ...
    }
}
```

### Alterar Posicionamento

Modifique a propriedade `position`:

```php
position: {
    banner: "bottomLeft"  // Opções: bottomLeft, bottomRight, bottomCenter, center
}
```

### Adicionar/Remover Tipos de Cookies

Edite o array `cookieTypes`:

```php
cookieTypes: [
    {
        id: "meu_cookie_personalizado",
        name: "Nome do Cookie",
        description: "<p>Descrição...</p>",
        required: false,
        onAccept: function() {
            // Código executado quando aceito
        },
        onReject: function() {
            // Código executado quando rejeitado
        }
    }
]
```

## Integração com Google Analytics

Para integrar com Google Analytics, descomente as linhas nos callbacks `onAccept` e `onReject`:

```php
onAccept: function() {
    if (typeof gtag !== 'undefined') {
        gtag('consent', 'update', {
            analytics_storage: 'granted',
        });
    }
}
```

## Suporte

Para mais informações sobre o Silktide Consent Manager, visite:
https://silktide.com/consent-manager

## Licença

GPL v2 or later
