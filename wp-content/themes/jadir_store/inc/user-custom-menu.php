<?php

// PERSONALIZAR O MENU DA CONTA DO CLIENTE
function jadir_custom_menu($menu_links) {
  // Remove o item "Downloads"
  if (isset($menu_links['downloads'])) {
    unset($menu_links['downloads']);
  }

  // Remove o item "Certificados"
  if (isset($menu_links['certificados'])) {
    unset($menu_links['certificados']);
  }

  return $menu_links;
}
add_filter('woocommerce_account_menu_items', 'jadir_custom_menu');




function jadir_add_endpoint(){
  add_rewrite_endpoint('certificados', EP_PAGES);
}
add_action('init', 'jadir_add_endpoint');


function jadir_certificados(){
?>
<h2>Esses são Certificados</h2>
<?php
}

add_action('woocommerce_account_certificados_endpoint', 'jadir_certificados');


?>
