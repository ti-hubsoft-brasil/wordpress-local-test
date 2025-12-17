<?php
/**
 * Script temporário para criar a página de Política de Privacidade
 * Execute este arquivo UMA VEZ acessando: http://localhost:8080/create-privacy-page.php
 * Após executar, REMOVA este arquivo por segurança.
 */

// Carrega o WordPress
require_once __DIR__ . '/wp-load.php';

// Verifica se a página já existe
$existing_page = get_page_by_path('politica-privacidade');

if ($existing_page) {
    echo '<h2>✅ Página já existe!</h2>';
    echo '<p>ID: ' . $existing_page->ID . '</p>';
    echo '<p>Título: ' . $existing_page->post_title . '</p>';
    echo '<p><a href="' . get_permalink($existing_page->ID) . '" target="_blank">Ver página</a></p>';
    echo '<p><strong>⚠️ LEMBRE-SE DE REMOVER ESTE ARQUIVO (create-privacy-page.php)</strong></p>';
    exit;
}

// Cria a página
$page_data = array(
    'post_type'     => 'page',
    'post_title'    => 'Política de Privacidade',
    'post_name'     => 'politica-privacidade',
    'post_status'   => 'publish',
    'post_content'  => '
        <h1>Política de Privacidade</h1>
        
        <p>Esta é a página de <strong>política de privacidade de teste</strong>.</p>
        
        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
            <h3>🔒 Teste de Bloqueio do Banner Silktide</h3>
            <p><strong>O banner de cookies Silktide NÃO deve aparecer nesta página!</strong></p>
            <p>Se você vê esta página sem o banner de cookies, o bloqueio está funcionando corretamente.</p>
        </div>
        
        <h2>1. Informações Gerais</h2>
        <p>Este é um conteúdo de exemplo para a política de privacidade.</p>
        
        <h2>2. Coleta de Dados</h2>
        <p>Informações sobre como coletamos dados dos usuários.</p>
        
        <h2>3. Uso de Cookies</h2>
        <p>Explicação sobre o uso de cookies - mas NESTA página específica, o banner de consentimento está desativado.</p>
        
        <h2>4. Seus Direitos</h2>
        <p>Informações sobre os direitos dos usuários em relação aos seus dados.</p>
    ',
    'post_author'   => 1 // Usuário admin
);

$page_id = wp_insert_post($page_data);

if (is_wp_error($page_id)) {
    echo '<h2>❌ Erro ao criar página</h2>';
    echo '<p>' . $page_id->get_error_message() . '</p>';
} else {
    echo '<h2>✅ Página criada com sucesso!</h2>';
    echo '<p><strong>ID da página:</strong> ' . $page_id . '</p>';
    echo '<p><strong>URL:</strong> <a href="' . get_permalink($page_id) . '" target="_blank">' . get_permalink($page_id) . '</a></p>';
    echo '<p><strong>Slug:</strong> politica-privacidade</p>';
    
    echo '<hr>';
    echo '<h3>🧪 Como testar:</h3>';
    echo '<ol>';
    echo '<li>Acesse <a href="' . get_permalink($page_id) . '" target="_blank">esta página</a></li>';
    echo '<li>Verifique que o banner de cookies Silktide <strong>NÃO aparece</strong></li>';
    echo '<li>Acesse qualquer outra página do site</li>';
    echo '<li>Verifique que o banner <strong>APARECE normalmente</strong></li>';
    echo '</ol>';
    
    echo '<hr>';
    echo '<p style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;">';
    echo '<strong>⚠️ IMPORTANTE:</strong> Remova este arquivo (create-privacy-page.php) após a execução por questões de segurança!';
    echo '</p>';
}
?>
