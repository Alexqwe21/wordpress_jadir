

document.addEventListener('DOMContentLoaded', () => {
  // Inicializa o slide (se estiver usando)
  const slide = new Slide('.slide', '.slide-wrapper');
  slide.init();

  // Classe da galeria de produto
  class Gallery {
    constructor() {
      this.gallery = document.querySelector('[data-gallery="gallery"]');
      this.galleryList = document.querySelectorAll('[data-gallery="list"]');
      this.galleryMain = document.querySelector('[data-gallery="main"]');
      this.changeImage = this.changeImage.bind(this);
    }

    // Troca imagem principal
    changeImage({ currentTarget }) {
      if (this.galleryMain && currentTarget?.src) {
        this.galleryMain.src = currentTarget.src;
      }
    }

    // Adiciona eventos de clique e mouseover
    addChangeEvent() {
      if (!this.galleryMain || !this.galleryList.length) {
        console.warn('Elemento principal ou galeria não encontrados.');
        return;
      }

      this.galleryList.forEach((img) => {
        img.style.cursor = 'pointer';
        img.addEventListener('click', this.changeImage);
        img.addEventListener('mouseover', this.changeImage);
      });
    }

    // Inicia galeria
    init() {
      if (this.gallery) {
        this.addChangeEvent();

        // Teste no console para depuração
        console.log('Gallery detectada:', this.gallery);
        console.log('Imagens da galeria:', this.galleryList);
        console.log('Imagem principal:', this.galleryMain);
      } else {
        console.warn('A galeria não foi encontrada no DOM.');
      }
    }
  }

  // Instancia e inicializa a galeria
  const gallery = new Gallery();
  gallery.init();
});