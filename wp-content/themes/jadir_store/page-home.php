<?php

// Template Name: Home
get_header();

  

  $img_url = get_stylesheet_directory_uri() . '/img';



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

$data['slide'] = format_products($products_slide, 'slide');
$data['lancamentos'] = format_products($products_new, 'medium');
$data['vendas'] = format_products($products_new, 'medium');

$home_id = get_the_ID();

$categoria_esquerda = get_post_meta($home_id, 'categoria_esquerda', true);
$categoria_direita = get_post_meta($home_id, 'categoria_direita', true);
$anime = get_post_meta($home_id, 'anime', true);

/**
 * Função para retornar os dados da categoria + contagem de produtos
 */
function get_product_category_data($slug)
{
    $term = get_term_by('slug', $slug, 'product_cat');

    if (!$term) return null;

    $term_id = $term->term_id;
    $img_id = get_term_meta($term_id, 'thumbnail_id', true);
    $img_url = wp_get_attachment_image_src($img_id, 'slide')[0];

    // Conta produtos publicados na categoria
    $args = [
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
        'tax_query'      => [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $slug,
            ],
        ],
    ];

    $query = new WP_Query($args);
    $product_count = $query->post_count;

    return [
        'id'    => $term_id,
        'name'  => $term->name,
        'link'  => get_term_link($term_id, 'product_cat'),
        'img'   => $img_url,
        'count' => $product_count
    ];
}

$data['categorias'][$categoria_esquerda] = get_product_category_data($categoria_esquerda);
$data['categorias'][$categoria_direita] = get_product_category_data($categoria_direita);
$data['anime'][$anime] = get_product_category_data($anime);


// echo "<pre>";
// print_r($data);
// echo "</pre>";

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
          <div>
            <span class="nome_categoria" ><?= $categoria['name'] ?></span>
             <strong class="count"><?= $categoria['count'] ?>  Produtos </strong>
          </div>
        </div>

            <div class="separador">
          <a href="<?= $categoria['link'] ?>">
            <img src="<?= $categoria['img'] ?>" alt="<?= $categoria['name'] ?>">
          </a>
          <div>
            <span class="nome_categoria" ><?= $categoria['name'] ?></span>
             <strong class="count"><?= $categoria['count'] ?>  Produtos </strong>
          </div>
        </div>

            <div class="separador">
          <a href="<?= $categoria['link'] ?>">
            <img src="<?= $categoria['img'] ?>" alt="<?= $categoria['name'] ?>">
          </a>
          <div>
            <span class="nome_categoria" ><?= $categoria['name'] ?></span>
             <strong class="count"><?= $categoria['count'] ?>  Produtos </strong>
          </div>
        </div>
       
      <?php } ?>
    </article>
  </div>
</section>





    <section class="container">
      <h1 class="subtitulo">Mais Vendidos</h1>
      <?php jadir_store_product_list($data['vendas']) ?>
    </section>

<div class="space"></div>

   <section class="promo_img">
  <article class="site">
    <div class="imagem_destaque carousel">
      <img src="<?= $img_url; ?>/produto_eletronico.png" alt="Destaque 0" class="carousel-img active">
      <img src="<?= $img_url; ?>/produto_2_jadir.png" alt="Destaque 1" class="carousel-img">
      <img src="<?= $img_url; ?>/produto_3_jadir.png" alt="Destaque 2" class="carousel-img">
    </div>

    <div class="texto_promo">
      <div class="card">
        <div class="content">
          <svg fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 9V5H4V9H20ZM20 11H4V19H20V11ZM3 3H21C21.5523 3 22 3.44772 22 4V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V4C2 3.44772 2.44772 3 3 3ZM5 12H8V17H5V12ZM5 6H7V8H5V6ZM9 6H11V8H9V6Z"></path>
          </svg>
          <p class="para">
            Frete Grátis em Todas as Compras!  
            Aproveite nossos produtos com frete 100% grátis para todo o Brasil.  
            Receba conforto, qualidade e inovação sem pagar nada pelo envio.  
            Compre agora e economize ainda mais!
          </p>
        </div>
      </div>
    </div>
  </article>
</section>


<section class="carrosel_produtos">
  <article class="site">
     <h1 class="subtitulo">Produtos Mais Amados</h1>
    <div class="swiper slicer-slider">
      <div class="swiper-wrapper">
        <!-- Slides com imagens reais -->
        <div class="swiper-slide">
          <img src="<?= $img_url; ?>/produto_eletronico.png" alt="Produto 1">
        </div>
        <div class="swiper-slide">
          <img src="<?= $img_url; ?>/produto_eletronico.png" alt="Produto 2">
        </div>
        <div class="swiper-slide">
          <img src="<?= $img_url; ?>/produto_eletronico.png" alt="Produto 3">
        </div>

          <div class="swiper-slide">
          <img src="<?= $img_url; ?>/produto_eletronico.png" alt="Produto 3">
        </div>
      </div>

      <!-- Botões e paginação -->
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
      <div class="swiper-pagination"></div>
    </div>
  </article>
</section>


<?php }
} ?>

<?php get_footer(); ?>

<script>
  const images = document.querySelectorAll('.carousel-img');
  let index = 0;

  setInterval(() => {
    images[index].classList.remove('active');
    index = (index + 1) % images.length;
    images[index].classList.add('active');
  }, 3000); // troca a cada 3 segundos
</script>


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


<script>
  document.addEventListener('DOMContentLoaded', function () {
    new Swiper('.slicer-slider', {
      slidesPerView: 'auto',
      spaceBetween: 20,
      centeredSlides: false,
      loop: true,
      speed: 2500,
      autoplay: {
        delay: 0,
        disableOnInteraction: false,
      },
      freeMode: true,
      freeModeMomentum: false,
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      breakpoints: {
        320: { slidesPerView: 1 },
        768: { slidesPerView: 2 },
        1024: { slidesPerView: 3 },
      },
    });
  });
</script>





