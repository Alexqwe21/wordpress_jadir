<footer class="footer">
  <img src="<?= get_stylesheet_directory_uri(); ?>/img/logo_footer.svg" alt="Jadir Store">
  <div class="container footer-info">
    <section  class="centro" >
      <h3>Páginas</h3>
      <?php
      wp_nav_menu([
        'menu' => 'footer',
        'container' => 'nav',
        'container_class' => 'footer-menu'
      ]);
      ?>
    </section>

    <section class="centro" >
      <h3>Redes Sociais</h3>
      <?php
      wp_nav_menu([
        'menu' => 'redes',
        'container' => 'nav',
        'container_class' => 'footer-redes'
      ]);
      ?>
    </section>

    <section class="centro" >
      <h3>Meios De Pagamento</h3>

      <ul>
        <li>Cartão De Crédito</li>
        <li>Boleto Bancário</li>
        <li>PagSeguro</li>
      </ul>
    </section>
  </div>
  <small class="footer-copy">Jadir Store &copy; | Desenvolvidor Por Alex Sandro <?= date('Y'); ?></small>
</footer>


<?php wp_footer(); ?>
<script type="module" src="<?= get_stylesheet_directory_uri(); ?>/js/slide.js"></script>
<script type="module" src="<?= get_stylesheet_directory_uri(); ?>/js/script.js"></script>
<script  type="module" src="<?= get_stylesheet_directory_uri(); ?>/js/botao_fixo.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>

</html>