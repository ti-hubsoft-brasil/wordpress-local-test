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
    return is_page('politica-privacidade');
}

/**
 * [CORREÇÃO CRUCIAL LGPD/GDPR]
 * Injeta o comando de Consentimento Padrão do Google (Consent Mode) no <head>.
 *
 * É OBRIGATÓRIO que este código seja executado o mais cedo possível (wp_head com prioridade 1)
 * para definir TODAS as permissões de rastreamento como 'denied' (negado) antes que
 * o GTM ou outras tags do Google sejam carregadas.
 */
function scb_add_consent_default()
{
    // Bloqueia execução na página de política de privacidade
    if (scb_disable_on_privacy_policy()) {
        return;
    }
    ?>
    <script>
        // Inicializa o dataLayer (necessário para GTM e Consent Mode)
        window.dataLayer = window.dataLayer || [];

        // Define a função gtag() que adiciona comandos ao dataLayer
        function gtag(){dataLayer.push(arguments);}

        // Define o estado de consentimento como NEGADO (denied) por padrão.
        // Isso é a Negação Implícita exigida pelas leis de privacidade (LGPD/GDPR).
        // IMPORTANTE: Este código DEVE executar ANTES do GTM/GA4 ser carregado.
        gtag('consent', 'default', {
            'analytics_storage': 'denied',     // Bloqueia Google Analytics
            'ad_storage': 'denied',            // Bloqueia Google Ads
            'ad_user_data': 'denied',          // Bloqueia dados de usuário para Ads
            'ad_personalization': 'denied',    // Bloqueia personalização de Ads
            'wait_for_update': 500             // Aguarda 500ms para o usuário aceitar antes de carregar tags
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
 *
 * Esta função injeta o código JavaScript de configuração do Silktide no rodapé de cada página.
 * A configuração define os tipos de cookies, textos, posicionamento e callbacks.
 */
function scb_cookie_config()
{
    // Bloqueia execução na página de política de privacidade
    if (scb_disable_on_privacy_policy()) {
        return;
    }
    ?>
    <script>
        // Função para inicializar o cookie banner com verificação robusta
        var silktideBannerRetries = 0;
        var maxRetries = 5;  // Máximo de 5 tentativas (2.5 segundos total)

        function initSilktideCookieBanner() {
            // verifica se o JS foi carregado pela função wp_enqueue_script
            if (typeof silktideCookieBannerManager !== 'undefined') {
                silktideCookieBannerManager.updateCookieBannerConfig({
                background: {
                    showBackground: true
                },

                cookieIcon: {
                    position: "bottomLeft"
                },

                cookieTypes: [
                    {
                        id: "cookies_necessarios",
                        name: "Cookies Necessários",
                        description: "<p>Esses cookies são necessários para o <b>funcionamento correto do site</b> e não podem ser desativados. Eles ajudam em funções como fazer login e definir suas preferências de privacidade.</p>",
                        required: true,
                        onAccept: function () {
                            console.log('Cookies necessários aceitos');
                        }
                    },
                    {
                        id: "cookies_de_analises",
                        name: "Cookies de Análises",
                        description: "<p>Esses cookies nos ajudam a melhorar o site, rastreando quais páginas são mais populares e como os visitantes navegam pelo site.</p>",
                        required: false,
                        onAccept: function () {
                            console.log('Cookies de análise aceitos');

                            // Verifica se a biblioteca de tags do Google (gtag.js) está carregada na página.
                            if (typeof gtag !== 'undefined') {
                                gtag('consent', 'update', {
                                    analytics_storage: 'granted',
                                });
                            }
                            if (typeof dataLayer !== 'undefined') {
                                dataLayer.push({
                                    'event': 'consent_accepted_cookies_de_analises',
                                });
                            }
                        },
                        onReject: function () {
                            console.log('Cookies de análise rejeitados');

                            if (typeof gtag !== 'undefined') {
                                gtag('consent', 'update', {
                                    analytics_storage: 'denied',
                                });
                            }
                        }
                    },
                    {
                        id: "cookies_de_anuncios",
                        name: "Cookies de Anúncios",
                        description: "<p>Esses cookies fornecem recursos adicionais e personalização para melhorar sua experiência. Eles podem ser definidos por nós ou por parceiros cujos serviços utilizamos.</p>",
                        required: false,
                        onAccept: function () {
                            console.log('Cookies de anúncios aceitos');

                            if (typeof gtag !== 'undefined') {
                                gtag('consent', 'update', {
                                    ad_storage: 'granted',
                                    ad_user_data: 'granted',
                                    ad_personalization: 'granted',
                                });
                            }
                            if (typeof dataLayer !== 'undefined') {
                                dataLayer.push({
                                    'event': 'consent_accepted_cookies_de_anuncios',
                                });
                            }
                        },
                        onReject: function () {
                            console.log('Cookies de anúncios rejeitados');

                            if (typeof gtag !== 'undefined') {
                                gtag('consent', 'update', {
                                    ad_storage: 'denied',
                                    ad_user_data: 'denied',
                                    ad_personalization: 'denied',
                                });
                            }
                        }
                    }
                ],

                text: {
                    banner: {
                        description: "<p>Usamos cookies em nosso site para melhorar sua experiência de usuário, fornecer conteúdo personalizado e analisar nosso tráfego. <a href=\"<?php echo esc_attr(esc_url(home_url('/politica-privacidade'))); ?>\" target=\"_blank\">Política de Privacidade</a></p>",
                        acceptAllButtonText: "Aceitar Todos",
                        acceptAllButtonAccessibleLabel: "Aceitar todos os Cookies",
                        rejectNonEssentialButtonText: "Rejeitar",
                        rejectNonEssentialButtonAccessibleLabel: "Rejeitar cookies não essenciais",
                        preferencesButtonText: "Preferências",
                        preferencesButtonAccessibleLabel: "Abrir Preferências de Cookies"
                    },
                    preferences: {
                        title: "Personalize suas preferências de cookies",
                        description: "<p>Respeitamos o seu direito à privacidade. Você pode optar por não permitir alguns tipos de cookies. Suas preferências de cookies serão aplicadas em todo o nosso site.</p>",
                        creditLinkText: "",
                        creditLinkAccessibleLabel: ""
                    }
                },

                position: {
                    banner: "bottomLeft"
                }
            });
            } else {
                silktideBannerRetries++;
                if (silktideBannerRetries < maxRetries) {
                    console.warn('Silktide Cookie Banner Manager não carregado. Tentativa ' + silktideBannerRetries + ' de ' + maxRetries);
                    // Retry após 500ms se não carregar na primeira vez
                    setTimeout(initSilktideCookieBanner, 500);
                } else {
                    console.error('Silktide Cookie Banner Manager falhou ao carregar após ' + maxRetries + ' tentativas.');
                }
            }
        }

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
