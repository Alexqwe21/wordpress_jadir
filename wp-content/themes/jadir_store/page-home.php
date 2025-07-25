<?php

// Template Name: Home
get_header();



$products_slide = wc_get_products([
  'limit' => 6,
  'tag' => ['slide'],
  'stock_status' => 'instock'
]);

$products_new = wc_get_products([
  'limit' => 9,
  'orderby' => 'date',
  'order' => 'DESC',
  'stock_status' => 'instock'
]);

$products_sales = wc_get_products([
  'limit' => 9,
  'meta_key' => 'total_sales',
  'orderby' => 'meta_value_num',
  'order' => 'DESC',
  'stock_status' => 'instock'
]);




$data = [];

$data['slide'] =  format_products($products_slide, 'slide');

$data['lancamentos'] =  format_products($products_new, 'medium');

$data['vendas'] =  format_products($products_new, 'medium');

$home_id = get_the_ID();



$categoria_esquerda = get_post_meta($home_id, 'categoria_esquerda', true);

$categoria_direita = get_post_meta($home_id, 'categoria_direita', true);

$anime = get_post_meta($home_id, 'anime', true);



function get_product_category_data($category)
{
  $cat = get_term_by('slug', $category, 'product_cat');
  $cat_id = $cat->term_id;
  $img_id = get_term_meta($cat_id, 'thumbnail_id', true);

  return [
    'name' => $cat->name,
    'id' => $cat_id,
    'link' => get_term_link($cat_id, 'product_cat'),
    'img' => wp_get_attachment_image_src($img_id, 'slide')[0]
  ];
}

$data['categorias'][$categoria_esquerda] = get_product_category_data($categoria_esquerda);

$data['categorias'][$categoria_direita] = get_product_category_data($categoria_direita);

$data['anime'][$anime] = get_product_category_data($anime);

?>



<?php if (have_posts()) {
  while (have_posts()) {
    the_post();  ?>

    <ul class="vantagens">
      <li>Frete Grátis</li>
      <li>Troca Fácil</li>
      <li>Até 12x</li>
    </ul>



    <section class="slide-wrapper">
      <ul class="slide">
        <?php foreach ($data['slide'] as $product) { ?>
          <li class="slide-item">
            <img src="<?= $product['img']; ?>" alt="<?= $product['name']; ?>">
            <div class="slide-info">
              <span class="slide-preco"><?= $product['price']; ?></span>
              <h2 class="slide-nome"><?= $product['name']; ?></h2>
              <a class="btn-link" href="<?= $product['link']; ?>">Ver Produto</a>
            </div>
          </li>
        <?php  } ?>
      </ul>

    </section>

    <section class="container">
      <h1 class="subtitulo">Lançamentos</h1>
      <?php jadir_store_product_list($data['lancamentos']) ?>
    </section>



<section class="categorias-home">
<?php foreach ($data['categorias'] as $categoria) {?>
  <a href="<?=  $categoria['link'] ?>">
    <img src="<?=$categoria['img']?>" alt="<?=$categoria['name']?>">
    <span class="btn-link" ><?= $categoria['name'] ?></span>
  </a>
<?php } ?>
</section>


 


  <section class="anime">
  <h1 class="subtitulo">Categorias</h1>
  <div class="carousel-container">
    <article class="site carousel-slide">
      <?php foreach ($data['anime'] as $categoria) { ?>
        <div class="separador">
          <a href="<?= $categoria['link'] ?>">
            <img src="<?= $categoria['img'] ?>" alt="<?= $categoria['name'] ?>">
          </a>
        </div>
        <div class="separador">
          <a href="<?= $categoria['link'] ?>">
            <img src="<?= $categoria['img'] ?>" alt="<?= $categoria['name'] ?>">
          </a>
        </div>
        <div class="separador">
          <a href="<?= $categoria['link'] ?>">
            <img src="<?= $categoria['img'] ?>" alt="<?= $categoria['name'] ?>">
          </a>
        </div>
        <div class="separador">
          <a href="<?= $categoria['link'] ?>">
            <img src="<?= $categoria['img'] ?>" alt="<?= $categoria['name'] ?>">
          </a>
        </div>
      <?php } ?>
    </article>
  </div>
</section>





    <section class="container">
      <h1 class="subtitulo">Mais Vendidos</h1>
      <?php jadir_store_product_list($data['vendas']) ?>
    </section>




<?php }
} ?>

<?php get_footer(); ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const wrapper = document.querySelector('.carrossel-wrapper');
  const items = Array.from(wrapper.children);
  const totalWidth = wrapper.scrollWidth;

  // Clona os itens
  items.forEach(item => {
    const clone = item.cloneNode(true);
    wrapper.appendChild(clone);
  });

  let position = 0;
  const speed = 5; // quanto maior, mais rápido

  function animate() {
    position += speed;
    if (position >= totalWidth) {
      position = 0;
    }
    wrapper.style.transform = `translateX(-${position}px)`;
    requestAnimationFrame(animate);
  }

  animate();
});
</script>


<script>
  document.addEventListener('DOMContentLoaded', () => {
    const slide = document.querySelector('.carousel-slide');
    slide.innerHTML += slide.innerHTML; // duplica os itens
  });
</script>
