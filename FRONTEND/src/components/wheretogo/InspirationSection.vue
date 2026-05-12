<template>
  <section class="inspiration-section">
    <div class="container">
      <div class="section-header">
        <h2 class="inspiration-title">¿Buscas más inspiración?</h2>
        <p class="inspiration-subtitle">Bolivia es donde los Andes se encuentran con la Amazonía, el pasado se fusiona con el presente. Es un lugar donde puedes escuchar ecos de la historia y mirar hacia un futuro fascinante.</p>
      </div>
      
      <div class="inspiration-grid">
        <div class="inspiration-card" v-for="inspiration in inspirationItems" :key="inspiration.id">
          <div class="inspiration-image">
            <img :src="inspiration.image" :alt="inspiration.title" class="inspiration-img">
          </div>
          <h3 class="inspiration-card-title">{{ inspiration.title }}</h3>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'InspirationSection',
  data() {
    return {
      inspirationItems: [
        {
          id: 1,
          title: "Los mejores destinos de naturaleza en Bolivia",
          image: "https://landingwebimg.s3.amazonaws.com/uploads/landing_page_post/image_header/1147/image-upload1606678301.jpeg"
        },
        {
          id: 2,
          title: "Las 20 mejores playas y lagos de Bolivia",
          image: "https://i.ytimg.com/vi/kJonr0qMKGQ/maxresdefault.jpg"
        },
        {
          id: 3,
          title: "Los mejores destinos para el spa y bienestar boliviano",
          image: "https://buenvivirdigital.com/user-files/blog/1659710269turismo-bol.jpg"
        },
        {
          id: 4,
          title: "Experiencias gastronómicas únicas en Bolivia",
          image: "https://qamasa.com/wp-content/uploads/2024/09/1725660413168.webp"
        },
        {
          id: 5,
          title: "Retiros de relajación en los Andes",
          image: "https://photos.tpn.to/ot/lt/fr/gt/653x490.jpg"
        },
        {
          id: 6,
          title: "Miradores y torres de observación en Bolivia",
          image: "https://www.laregion.bo/wp-content/uploads/2015/10/MIRADOR-KILLI-KILLI.jpeg"
        }
      ]
    }
  }
}
</script>

<style scoped>
/* Sección de Inspiración (copiado/adaptado de home/InspirationSection.vue) */
.inspiration-section { 
  padding: 4rem 0;
  background: var(--bg-secondary);
}

.inspiration-title {
  font-size: 2.5rem;
  font-weight: 400;
  color: var(--text-primary);
  text-align: center;
  margin-bottom: 3rem;
  letter-spacing: -0.01em;
}

.inspiration-subtitle {
  color: var(--text-secondary);
  font-size: 1.125rem;
  font-family: var(--font-family-base);
  margin: 0 auto 2rem;
  max-width: 900px;
  line-height: 1.6;
  text-align: center;
}

.inspiration-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); 
  gap: 1.5rem; 
}

/* Tarjetas de inspiración (copiado/adaptado de home/InspirationCard.vue) */
.inspiration-card { 
  background: transparent; 
  border: none; 
  border-radius: 0; 
  overflow: visible; 
  box-shadow: none; 
  cursor: pointer;
}

.inspiration-image { 
  height: 260px; 
  position: relative; 
  border-radius: 24px; 
  overflow: hidden; 
  background: var(--light-gray); 
  transition: border-radius 300ms ease; 
}

/* Ajuste para la imagen <img> existente */
.inspiration-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transform: scale(1);
  transition: transform 400ms ease;
  will-change: transform;
}

.inspiration-card:hover .inspiration-img { 
  transform: scale(1.05); 
}

.inspiration-card:hover .inspiration-image { 
  border-radius: 24px 12px 24px 24px; 
}

/* Título de la tarjeta alineado al estilo del card original */
.inspiration-card-title { 
  margin: 0.75rem 0 0.25rem; 
  font-size: 1.5rem; 
  font-weight: 400; 
  letter-spacing: -0.02em; 
  line-height: 1.25; 
  color: var(--text-primary); 
  text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
  .inspiration-grid {
    grid-template-columns: 1fr;
  }
  
  .inspiration-title {
    font-size: 2rem;
  }
}
</style>

