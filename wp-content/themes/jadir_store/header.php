<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php bloginfo('name') ?> <?php wp_title('|'); ?></title>

  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php

  $img_url = get_stylesheet_directory_uri() . '/img';
$cart_count =  WC()->cart->get_cart_contents_count();
  ?>
<!-- Botão WhatsApp -->
<a href="https://api.whatsapp.com/send?phone=5511996038329" target="_blank" class="whatsapp-fixo"
   aria-label="Fale conosco pelo WhatsApp">
   <img src="<?= $img_url;  ?>/whatsapp_Flutuante.svg" alt="WhatsApp"/>
</a>

<!-- Botão Voltar ao Topo -->
<button id="back-to-top" title="Voltar para o Topo">
   <img src="<?= $img_url;  ?>/seta_para_cima.svg" alt="seta">
</button>



  <header class="header container">
    <a href="/" class="logo_centro"><img src="<?= $img_url;  ?>/logo_jadir_store.svg" alt="Jadir store"></a>
    <div class="busca">
      <form action="<?php bloginfo('url'); ?>/loja/" method="get">
        <input type="text" name="s" id="s" placeholder="Buscar" value="<?php the_search_query(); ?>">
        <input type="text" name="post_type" value="product" class="hidden">
        <input type="submit" id="searchbutton" value="Buscar">
      </form>
    </div>
    <nav class="conta">
      <a href="/wordpress_jadir/minha-conta/" class="minha-conta">Minha Conta</a>
      <a href="/wordpress_jadir/carrinho/" class="carrinho" >Carrinho
        <?php if($cart_count){ ?>
        <span class="carrinho-count"><?= $cart_count ?></span>
        <?php  } ?>
      </a>
    </nav>
  </header>

  <?php 
  wp_nav_menu([
    'Menu' => 'categorias',
    'container' => 'nav',
    'container_class' => 'menu-categorias' 
  ])
  ?>