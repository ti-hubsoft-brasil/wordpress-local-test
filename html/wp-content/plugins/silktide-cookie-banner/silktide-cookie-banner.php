<?php

/**

 * Plugin Name: Silktide Cookie Banner

 * Description: Gerenciador de consentimento de cookies Silktide para WordPress. Adiciona um banner de cookies personalizável em todas as páginas do site.

 * Version: 1.0.0

 * Author: Hubsoft TI

 */



// previnir acesso direto

if (!defined('ABSPATH')) {
    exit;
}



/**

 * Verifica se o plugin NÃO deve rodar nesta página

 */

function scb_disable_on_privacy_policy() {
    return in_array(true, [
        is_page('politica-privacidade'), 
        is_page('politica-de-privacidade-app-mobile')
    ], true);
}



/**

 * [CORREÇÃO CRUCIAL LGPD/GDPR]

 * Injeta o comando de Consentimento Padrão do Google (Consent Mode) no <head>.

 *

 * É OBRIGATÓRIO que este código seja executado o mais cedo possível (wp_head com prioridade 1)

 * para definir TODAS as permissões de rastreamento como 'denied' (negado) antes que

 * o GTM ou outras tags do Google sejam carregadas.

 */

function scb_add_consent_default() {
    if (scb_disable_on_privacy_policy()) return;
    ?>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}

        gtag('consent', 'default', {
            'analytics_storage': 'granted',
            'ad_storage': 'granted',
            'ad_user_data': 'granted',
            'ad_personalization': 'granted',
            'wait_for_update': 500
        });
    </script>
    <?php
}

/**

 * Enfileira os scripts e estilos do plugin
 *
 * Esta função é chamada pelo hook 'wp_enqueue_scripts' do WordPress.
 * Ela registra e carrega os arquivos CSS e JavaScript necessários para o banner de cookies.
 */

function scb_enqueue_scripts()
{
    // Bloqueia execução na página de política de privacidade
    if (scb_disable_on_privacy_policy()) {
        return;
    }

    wp_enqueue_style(
        'silktide-cookie-manager-css',
        plugin_dir_url(__FILE__) . 'assets/css/stcm-styles.css',
        array(),
        '1.0.0',
        'all'
    );

    // Compatibilidade com WordPress 6.3+ que suporta 'strategy'

    // Para versões antigas, usar boolean true para in_footer

    global $wp_version;

    if (version_compare($wp_version, '6.3', '>=')) {
        wp_enqueue_script(
            'silktide-cookie-manager-js',
            plugin_dir_url(__FILE__) . 'assets/js/stcm-script.js',
            array(),
            '1.0.0',
            array(
                'in_footer' => true,
                'strategy' => 'defer'  // defer garante execução após HTML ser parseado
            )
        );

    } else {

        // Fallback para WordPress < 6.3
        wp_enqueue_script(
            'silktide-cookie-manager-js',
            plugin_dir_url(__FILE__) . 'assets/js/stcm-script.js',
            array(),
            '1.0.0',
            true  // Carrega no footer
        );
    }
}

/**

 * Adiciona a configuração do cookie banner no rodapé
 * Esta função injeta o código JavaScript de configuração do Silktide no rodapé de cada página.
 * A configuração define os tipos de cookies, textos, posicionamento e callbacks.

 */

function scb_cookie_config() {
    if (scb_disable_on_privacy_policy()) return;
    ?>
    <script>
        function initSilktideCookieBanner() {
            // Verifica se o gerenciador do Silktide existe
            if (typeof silktideCookieBannerManager !== 'undefined') {
                
                silktideCookieBannerManager.updateCookieBannerConfig({
                    background: { showBackground: true },
                    cookieIcon: { position: "bottomLeft" },
                    
                    // Configuração dos tipos de cookies
                    cookieTypes: [
                        {
                            id: "cookies_necessarios",
                            name: "Cookies Necessários",
                            description: "Esses cookies são necessários para o funcionamento correto do site.",
                            required: true,
                            initialState: "accepted" // Garante que venha ON
                        },
                        {
                            id: "cookies_de_analises",
                            name: "Cookies de Análises",
                            description: "Ajudam a melhorar o site, rastreando páginas populares.",
                            required: false,
                            initialState: "accepted", // Força o estado inicial como MARCADO
                            onAccept: function () {
                                if (typeof gtag !== 'undefined') {
                                    gtag('consent', 'update', { 'analytics_storage': 'granted' });
                                }
                            },
                            onReject: function () {
                                if (typeof gtag !== 'undefined') {
                                    gtag('consent', 'update', { 'analytics_storage': 'denied' });
                                }
                            }
                        },
                        {
                            id: "cookies_de_anuncios",
                            name: "Cookies de Anúncios",
                            description: "Utilizados para marketing e personalização de anúncios (Google Ads).",
                            required: false,
                            initialState: "accepted", // Força o estado inicial como MARCADO
                            onAccept: function () {
                                if (typeof gtag !== 'undefined') {
                                    gtag('consent', 'update', {
                                        'ad_storage': 'granted',
                                        'ad_user_data': 'granted',
                                        'ad_personalization': 'granted'
                                    });
                                }
                            },
                            onReject: function () {
                                if (typeof gtag !== 'undefined') {
                                    gtag('consent', 'update', {
                                        'ad_storage': 'denied',
                                        'ad_user_data': 'denied',
                                        'ad_personalization': 'denied'
                                    });
                                }
                            }
                        }
                    ],
                    text: {
                        banner: {
                            description: "Usamos cookies para melhorar sua experiência. <a href='<?php echo esc_url(home_url('/politica-privacidade')); ?>'>Política de Privacidade</a>",
                            acceptAllButtonText: "Aceitar Todos",
                            rejectNonEssentialButtonText: "Rejeitar",
                            preferencesButtonText: "Preferências"
                        },
                        preferences: {
                            title: "Customize suas preferências",
                            description: "Você pode escolher quais tipos de cookies deseja permitir."
                        }
                    }
                });
            } else {
                // Tenta novamente em 500ms se o script ainda não carregou
                setTimeout(initSilktideCookieBanner, 500);
            }
        }

        // Executa a inicialização
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initSilktideCookieBanner);
        } else {
            initSilktideCookieBanner();
        }
    </script>
    <?php
}

add_action('wp_head', 'scb_add_consent_default', 1); // Prioridade 1 (a mais alta) garante que carregue antes do GTM, que geralmente usa a prioridade 10.
add_action('wp_enqueue_scripts', 'scb_enqueue_scripts');
add_action('wp_footer', 'scb_cookie_config', 999);