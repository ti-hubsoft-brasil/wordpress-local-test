<?php
/**
 * Plugin Name: Silktide Cookie Banner
 * Plugin URI: https://silktide.com/consent-manager
 * Description: Gerenciador de consentimento de cookies Silktide para WordPress. Adiciona um banner de cookies personalizável em todas as páginas do site.
 * Version: 1.0.0
 * Author: Seu Nome
 * Author URI: https://seusite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: silktide-cookie-banner
 */

// Previne acesso direto ao arquivo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enfileira os scripts e estilos do plugin
 * 
 * Esta função é chamada pelo hook 'wp_enqueue_scripts' do WordPress.
 * Ela registra e carrega os arquivos CSS e JavaScript necessários para o banner de cookies.
 */
function scb_enqueue_scripts()
{
    // Carrega o arquivo CSS do Silktide
    // wp_enqueue_style() registra e enfileira uma folha de estilos
    // Parâmetros: handle (identificador único), src (caminho do arquivo), deps (dependências), ver (versão), media (tipo de mídia)
    wp_enqueue_style(
        'silktide-cookie-manager-css',                    // Handle único para identificar este CSS
        plugin_dir_url(__FILE__) . 'assets/css/cookie-manager.css',  // URL completa do arquivo CSS
        array(),                                           // Sem dependências de outros CSS
        '1.0.0',                                          // Versão do arquivo (útil para cache busting)
        'all'                                             // Aplica para todos os tipos de mídia (screen, print, etc)
    );

    // Carrega o arquivo JavaScript do Silktide
    // wp_enqueue_script() registra e enfileira um script JavaScript
    // Parâmetros: handle, src, deps, ver, in_footer (se true, carrega no rodapé)
    wp_enqueue_script(
        'silktide-cookie-manager-js',                    // Handle único para identificar este JS
        plugin_dir_url(__FILE__) . 'assets/js/cookie-manager.js',    // URL completa do arquivo JS
        array(),                                          // Sem dependências de outros scripts (como jQuery)
        '1.0.0',                                          // Versão do arquivo
        true                                              // true = carrega no rodapé (melhor para performance)
    );
}
// Adiciona a função ao hook 'wp_enqueue_scripts'
// Este hook é executado quando o WordPress está carregando scripts e estilos no frontend
add_action('wp_enqueue_scripts', 'scb_enqueue_scripts');

/**
 * Adiciona a configuração do cookie banner no rodapé
 * 
 * Esta função injeta o código JavaScript de configuração do Silktide no rodapé de cada página.
 * A configuração define os tipos de cookies, textos, posicionamento e callbacks.
 */
function scb_cookie_config()
{
    ?>
    <script>
        // Verifica se o objeto silktideCookieBannerManager está disponível
        if (typeof silktideCookieBannerManager !== 'undefined') {
            // Atualiza a configuração do banner de cookies
            // Este método aceita um objeto JavaScript com todas as opções de personalização
            silktideCookieBannerManager.updateCookieBannerConfig({
                // Configuração do backdrop (fundo escurecido quando o banner aparece)
                background: {
                    showBackground: true  // true = mostra o backdrop, false = não mostra
                },

                // Configuração do ícone de cookie (aparece após aceitar/rejeitar)
                cookieIcon: {
                    position: "bottomLeft"  // Opções: bottomLeft, bottomRight
                },

                // Array com os tipos de cookies que o site utiliza
                cookieTypes: [
                    {
                        id: "cookies_necess_rios",  // ID único para este tipo de cookie
                        name: "Cookies Necessários",  // Nome exibido para o usuário
                        description: "<p>Esses cookies são necessários para o <b>funcionamento correto do site</b> e não podem ser desativados. Eles ajudam em funções como fazer login e definir suas preferências de privacidade.</p>",
                        required: true,  // true = sempre ativado, não pode ser desativado pelo usuário
                        onAccept: function () {
                            // Callback executado quando os cookies necessários são aceitos
                            console.log('Cookies necessários aceitos');
                            // Aqui você pode adicionar lógica adicional, como carregar scripts essenciais
                        }
                    },
                    {
                        id: "cookies_de_an_lises",
                        name: "Cookies de Análises",
                        description: "<p>Esses cookies nos ajudam a melhorar o site, rastreando quais páginas são mais populares e como os visitantes navegam pelo site.</p>",
                        required: false,  // false = usuário pode aceitar ou rejeitar
                        onAccept: function () {
                            // Callback executado quando cookies de análise são aceitos
                            console.log('Cookies de análise aceitos');

                            // Exemplo de integração com Google Analytics
                            // Descomente as linhas abaixo se você tiver Google Analytics instalado
                            /*
                            if (typeof gtag !== 'undefined') {
                                gtag('consent', 'update', {
                                    analytics_storage: 'granted',
                                });
                            }
                            if (typeof dataLayer !== 'undefined') {
                                dataLayer.push({
                                    'event': 'consent_accepted_cookies_de_an_lises',
                                });
                            }
                            */
                        },
                        onReject: function () {
                            // Callback executado quando cookies de análise são rejeitados
                            console.log('Cookies de análise rejeitados');

                            // Exemplo de integração com Google Analytics
                            /*
                            if (typeof gtag !== 'undefined') {
                                gtag('consent', 'update', {
                                    analytics_storage: 'denied',
                                });
                            }
                            */
                        }
                    },
                    {
                        id: "cookies_de_an_ncios",
                        name: "Cookies de Anúncios",
                        description: "<p>Esses cookies fornecem recursos adicionais e personalização para melhorar sua experiência. Eles podem ser definidos por nós ou por parceiros cujos serviços utilizamos.</p>",
                        required: false,
                        onAccept: function () {
                            // Callback executado quando cookies de anúncios são aceitos
                            console.log('Cookies de anúncios aceitos');

                            // Exemplo de integração com Google Ads
                            /*
                            if (typeof gtag !== 'undefined') {
                                gtag('consent', 'update', {
                                    ad_storage: 'granted',
                                    ad_user_data: 'granted',
                                    ad_personalization: 'granted',
                                });
                            }
                            if (typeof dataLayer !== 'undefined') {
                                dataLayer.push({
                                    'event': 'consent_accepted_cookies_de_an_ncios',
                                });
                            }
                            */
                        },
                        onReject: function () {
                            // Callback executado quando cookies de anúncios são rejeitados
                            console.log('Cookies de anúncios rejeitados');

                            /*
                            if (typeof gtag !== 'undefined') {
                                gtag('consent', 'update', {
                                    ad_storage: 'denied',
                                    ad_user_data: 'denied',
                                    ad_personalization: 'denied',
                                });
                            }
                            */
                        }
                    }
                ],

                // Textos personalizados para o banner e modal
                text: {
                    banner: {
                        // Textos do banner principal
                        description: "<p>Usamos cookies em nosso site para melhorar sua experiência de usuário, fornecer conteúdo personalizado e analisar nosso tráfego. <a href=\"<?php echo esc_url(home_url('/politica-de-privacidade')); ?>\" target=\"_blank\">Política de Privacidade</a></p>",
                        acceptAllButtonText: "Aceitar Todos",
                        acceptAllButtonAccessibleLabel: "Aceitar todos os Cookies",
                        rejectNonEssentialButtonText: "Rejeitar",
                        rejectNonEssentialButtonAccessibleLabel: "Rejeitar cookies não essenciais",
                        preferencesButtonText: "Preferências",
                        preferencesButtonAccessibleLabel: "Abrir Preferências de Cookies"
                    },
                    preferences: {
                        // Textos do modal de preferências
                        title: "Personalize suas preferências de cookies",
                        description: "<p>Respeitamos o seu direito à privacidade. Você pode optar por não permitir alguns tipos de cookies. Suas preferências de cookies serão aplicadas em todo o nosso site.</p>",
                        creditLinkText: ".",  // Link de crédito do Silktide (use "." para ocultar)
                        creditLinkAccessibleLabel: "."
                    }
                },

                // Posicionamento do banner na tela
                position: {
                    banner: "bottomLeft"  // Opções: bottomLeft, bottomRight, bottomCenter, center
                }
            });
        }
    </script>
    <?php
}
// Adiciona a função ao hook 'wp_footer'
// Este hook é executado no rodapé de cada página, antes do fechamento da tag </body>
// Prioridade 999 garante que o script seja carregado após outros scripts
add_action('wp_footer', 'scb_cookie_config', 999);
