<?php

/**
 * Tema: Jadir Store
 * Funções customizadas para suporte ao WooCommerce, carregamento de CSS e tamanhos de imagem.
 */

// ✅ Suporte ao WooCommerce no tema
function jadir_store_add_woocommerce_support()
{
  add_theme_support('woocommerce');
}
add_action('after_setup_theme', 'jadir_store_add_woocommerce_support');

// ✅ Carregamento do CSS principal do tema

function jadir_store_css()
{
  // CSS principal
  $style_version = filemtime(get_template_directory() . '/style.css');
  wp_enqueue_style('jadir_store_style', get_template_directory_uri() . '/style.css', [], $style_version);

  // CSS base
  $base_version = filemtime(get_template_directory() . '/css/base.css');
  wp_enqueue_style('jadir_store_base', get_template_directory_uri() . '/css/base.css', [], $base_version);

  // Header
  $header_version = filemtime(get_template_directory() . '/css/header.css');
  wp_enqueue_style('jadir_store_header', get_template_directory_uri() . '/css/header.css', [], $header_version);

  // Reset
  $reset_version = filemtime(get_template_directory() . '/css/reset.css');
  wp_enqueue_style('jadir_store_reset', get_template_directory_uri() . '/css/reset.css', [], $reset_version);

  $slide_version = filemtime(get_template_directory() . '/css/slide.css');
  wp_enqueue_style('jadir_store_slide', get_template_directory_uri() . '/css/slide.css', [], $slide_version);

  $home_version = filemtime(get_template_directory() . '/css/home.css');
  wp_enqueue_style('jadir_store_home', get_template_directory_uri() . '/css/home.css', [], $home_version);

  $produtos_version = filemtime(get_template_directory() . '/css/produtos.css');
  wp_enqueue_style('jadir_store_produtos', get_template_directory_uri() . '/css/produtos.css', [], $produtos_version);

  $footer_version = filemtime(get_template_directory() . '/css/footer.css');
  wp_enqueue_style('jadir_store_footer', get_template_directory_uri() . '/css/footer.css', [], $footer_version);

  $product_version = filemtime(get_template_directory() . '/css/product.css');
  wp_enqueue_style('jadir_store_product', get_template_directory_uri() . '/css/product.css', [], $product_version);

  $woocommerce_version = filemtime(get_template_directory() . '/css/woocommerce.css');
  wp_enqueue_style('jadir_store_woocommerce', get_template_directory_uri() . '/css/woocommerce.css', [], $woocommerce_version);

}
add_action('wp_enqueue_scripts', 'jadir_store_css');




// ✅ Tamanho de imagem personalizado "slide"
function jadir_store_images()
{
  add_image_size('slide', 1000, 800, ['center', 'top']); // Corta centralizando horizontalmente e do topo verticalmente
  update_option('medium_crop', 1); // Ativa corte para imagens médias (opcional)
}
add_action('after_setup_theme', 'jadir_store_images');
//FUNÇÃO PARA MOSTRAR OS PRODUTOS POR VEZ
function jadir_store_loop_shop_per_page()
{
  return 6;
}

add_filter('loop_shop_per_page', 'jadir_store_loop_shop_per_page');





function remove_some_body_class($classes)
{
  $woo_class = array_search('woocommerce', $classes);
  $woopage_class = array_search('woocommerce-page', $classes);
  $search = in_array('archive', $classes) || in_array('product-template-default', $classes);

  if ($woo_class !== false && $woopage_class !== false && $search) {
    unset($classes[$woo_class]);
    unset($classes[$woopage_class]);
  }

 

  return $classes;
}
add_filter('body_class', 'remove_some_body_class');

include(get_template_directory() . '/inc/user-custom-menu.php' );
include(get_template_directory() . '/inc/product-list.php' );
include(get_template_directory() . '/inc/checkout-customizado.php');


function jadir_ocultar_paragrafo_checkout() {
  // Só carrega se estiver na página de finalização de compra e o usuário não for admin no painel
  if (is_checkout() && !is_admin()) {
    ?>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('p').forEach(function (p) {
          if (p.textContent.trim() === 'woocommerce_checkout') {
            p.remove();
          }
        });
      });
    </script>
    <?php
  }
}
add_action('wp_footer', 'jadir_ocultar_paragrafo_checkout');
add_filter('woocommerce_high_performance_order_storage_enabled', '__return_false');


// function handel_change_email_header() {
//   echo '<h2 style="text-align:center;">Mensagem header</h2>';
// }

// add_action('woocommerce_email_header', 'handel_change_email_header');

function jadir_change_email_footer_text($text) {
  $instagram = 'https://instagram.com/seu_perfil_aqui'; // substitua pelo seu link real
  $facebook = 'https://facebook.com/seu_perfil_aqui';   // substitua pelo seu link real

  return 'Jadir Store © - Obrigado pela sua compra! &nbsp;|&nbsp; 
    <a href="' . esc_url($instagram) . '" target="_blank" style="text-decoration:none; color:#C622E0;">Instagram</a> &nbsp;|&nbsp; 
    <a href="' . esc_url($facebook) . '" target="_blank" style="text-decoration:none; color:#C622E0;">Facebook</a>';
}
add_filter('woocommerce_email_footer_text', 'jadir_change_email_footer_text');


add_filter( 'woocommerce_checkout_should_load_checkout_block', '__return_false' );




function ocultar_menus_para_gerente_loja() {
  if (current_user_can('shop_manager')) {
    remove_menu_page('index.php');                  // Painel
    remove_menu_page('edit.php');                   // Posts
    remove_menu_page('upload.php');                 // Mídia
    remove_menu_page('edit.php?post_type=page');    // Páginas
    remove_menu_page('edit-comments.php');          // Comentários
    remove_menu_page('themes.php');                 // Aparência
    remove_menu_page('plugins.php');                // Plugins
    remove_menu_page('users.php');                  // Usuários
    remove_menu_page('tools.php');                  // Ferramentas
    remove_menu_page('options-general.php');        // Configurações
    // remove_menu_page('woocommerce');             // <- cuidado: isso remove TODO o WooCommerce
    // remove_menu_page('edit.php?post_type=shop_order'); // Pedidos
    // remove_menu_page('edit.php?post_type=product');    // Produtos
  }
}
add_action('admin_menu', 'ocultar_menus_para_gerente_loja', 999);

function ocultar_menus_para_gerente_de_loja() {
  if (current_user_can('shop_manager')) {

    // Oculta submenus específicos do WooCommerce
    remove_submenu_page('woocommerce', 'checkout_form_designer');
    remove_submenu_page('woocommerce', 'woocommerce-advanced-shipment-tracking');
    remove_submenu_page('woocommerce', 'wc-admin&path=%2Fextensions');
    remove_submenu_page('woocommerce', 'wc-status');

    // Você também pode ocultar o menu principal do plugin se ele estiver fora do menu WooCommerce:
    // remove_menu_page('woocommerce-advanced-shipment-tracking'); // Se for um menu fora do WooCommerce
  }
}
add_action('admin_menu', 'ocultar_menus_para_gerente_de_loja', 999);



function bloquear_acesso_extensoes_para_gerente() {
  if (
    current_user_can('shop_manager') &&
    is_admin() &&
    isset($_GET['page']) &&
    $_GET['page'] === 'wc-admin' &&
    isset($_GET['path']) &&
    $_GET['path'] === '/extensions'
  ) {
    wp_redirect(admin_url()); // Redireciona para o painel principal
    exit;
  }
}
add_action('admin_init', 'bloquear_acesso_extensoes_para_gerente');


function bloquear_acesso_pagamentos_para_gerente() {
  if (
    current_user_can('shop_manager') &&
    is_admin() &&
    isset($_GET['page'], $_GET['tab']) &&
    $_GET['page'] === 'wc-settings' &&
    $_GET['tab'] === 'checkout'
  ) {
    wp_redirect(admin_url('/'));
    exit;
  }
}
add_action('admin_init', 'bloquear_acesso_pagamentos_para_gerente');


function ocultar_postcustom_para_gerente() {
  if (current_user_can('shop_manager')) {
    echo '<style>
      #postcustom { display: none !important; }
      #screen-options-wrap .metabox-prefs input[name="postcustom"] { display: none !important; }
    </style>';
  }
}
add_action('admin_head', 'ocultar_postcustom_para_gerente');


function ocultar_downloads_pedido_para_gerente() {
  if (current_user_can('shop_manager')) {
    echo '<style>
      #woocommerce-order-downloads { display: none !important; }
    </style>';
  }
}
add_action('admin_head', 'ocultar_downloads_pedido_para_gerente');




