<template>
  <footer class="footer-modern">
    <div class="container">
      <!-- Sección principal del footer -->
      <div class="footer-main">
        <div class="footer-brand">
          <div class="brand-section">
            <h5 class="brand-title">
              <i class="fas fa-mountain"></i>
              Conoce Bolivia
            </h5>
            <p class="brand-description">
              Descubre los lugares más increíbles de Bolivia. Desde el majestuoso Salar de Uyuni hasta la exuberante Amazonía.
            </p>
          </div>
        </div>
        
        <div class="footer-links">
          <div class="links-section">
            <h6 class="section-title">Enlaces Rápidos</h6>
            <ul class="links-list">
              <li>
                <router-link to="/" class="footer-link">
                  <i class="fas fa-home"></i>
                  Inicio
                </router-link>
              </li>
              <li>
                <router-link to="/buscar?tab=historia" class="footer-link">
                  <i class="fas fa-map-marker-alt"></i>
                  Lugares Turísticos
                </router-link>
              </li>
              <li>
                <router-link to="/buscar?tab=naturaleza" class="footer-link">
                  <i class="fas fa-globe-americas"></i>
                  Regiones
                </router-link>
              </li>
              <li>
                <router-link to="/como-llegar" class="footer-link">
                  <i class="fas fa-plane"></i>
                  Cómo Llegar
                </router-link>
              </li>
              <li>
                <router-link to="/mapa" class="footer-link">
                  <i class="fas fa-map-marked-alt"></i>
                  Mapa Interactivo
                </router-link>
              </li>
            </ul>
          </div>
        </div>
        
        <div class="footer-contact">
          <div class="contact-section">
            <h6 class="section-title">Contacto</h6>
            <ul class="contact-list">
              <li>
                <i class="fas fa-envelope"></i>
                <span>info@boliviaturismo.com</span>
              </li>
              <li>
                <i class="fas fa-phone"></i>
                <span>+591 2 1234567</span>
              </li>
              <li>
                <i class="fas fa-map-marker-alt"></i>
                <span>La Paz, Bolivia</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
      
      <!-- Separador -->
      <div class="footer-divider"></div>
      
      <!-- Sección inferior -->
      <div class="footer-bottom">
        <div class="copyright">
          <p class="copyright-text">
            © 2025 CONOCE BOLIVIA. Todos los derechos reservados.
          </p>
          <!-- <p class="developer-info">
            <i class="fas fa-code"></i>
            Desarrollado por <strong><a href="https://www.facebook.com/juan.niganezaliaga/" title="Facebook">
              jfniganez
            </a></strong>

           
          </p> -->
        </div>
        
        <div class="social-section">
          <div class="social-links">
            <a href="https://www.facebook.com/share/1D1CjJko3U/" class="social-link" title="Facebook">
              <i class="fab fa-facebook-f"></i>
            </a>
            <a href="https://x.com/ConoceBoliviabo" class="social-link" title="Twitter">
              <i class="fab fa-twitter"></i>
            </a>
            <a href="https://www.instagram.com/conoceboliviavmt?igsh=am1mN3BuanUxNmdz" class="social-link" title="Instagram">
              <i class="fab fa-instagram"></i>
            </a>
            <a href="https://www.youtube.com/@conocebolivia5542" class="social-link" title="YouTube">
              <i class="fab fa-youtube"></i>
            </a>
            <a href="https://www.tiktok.com/@conocebolivia1?is_from_webapp=1&sender_device=pc" class="social-link" title="TikTok">
              <i class="fab fa-tiktok"></i>
            </a>
            <a href="/admin/login" class="social-link admin-link" title="Panel de Administración">
              <i class="fas fa-cog"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'AppFooter'
}
</script>

<style scoped>
/* Footer moderno y minimalista */
.footer-modern {
  /* background: var(--footer-bg); */
  border-top: 1px solid var(--footer-border);
  padding: 3rem 0 1.5rem;
  margin-top: 4rem;
}

/* Sección principal del footer */
.footer-main {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr;
  gap: 3rem;
  margin-bottom: 2rem;
}

/* Sección de marca */
.footer-brand {
  max-width: 400px;
}

.brand-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--footer-text-primary);
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.brand-title i {
  color: var(--primary-blue);
  font-size: 1.25rem;
}

.brand-description {
  color: var(--footer-text-secondary);
  line-height: 1.6;
  margin: 0;
  font-size: 0.95rem;
}

/* Secciones de enlaces y contacto */
.footer-links,
.footer-contact {
  display: flex;
  flex-direction: column;
}

.section-title {
  font-size: 1rem;
  font-weight: 600;
  color: var(--footer-text-primary);
  margin-bottom: 1.25rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Listas de enlaces */
.links-list,
.contact-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.links-list li,
.contact-list li {
  margin-bottom: 0.75rem;
}

.footer-link {
  color: var(--footer-text-secondary);
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  transition: all 0.2s ease;
  padding: 0.25rem 0;
  border-radius: 4px;
}

.footer-link:hover {
  color: var(--footer-link-hover);
  transform: translateX(4px);
}

.footer-link i {
  width: 16px;
  text-align: center;
  color: var(--footer-text-muted);
}

.footer-link:hover i {
  color: var(--footer-link-hover);
}

/* Lista de contacto */
.contact-list li {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: var(--footer-text-secondary);
  font-size: 0.9rem;
}

.contact-list i {
  width: 16px;
  text-align: center;
  color: var(--footer-text-muted);
}

/* Separador */
.footer-divider {
  height: 1px;
  background: var(--footer-border);
  margin: 2rem 0;
}

/* Sección inferior */
.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

/* Copyright */
.copyright-text {
  color: var(--footer-text-secondary);
  margin: 0;
  font-size: 0.9rem;
}

.developer-info {
  color: var(--footer-text-muted);
  margin: 0.25rem 0 0;
  font-size: 0.8rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.developer-info i {
  color: var(--primary-blue);
}

/* Enlaces sociales */
.social-links {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.social-link {
  color: var(--footer-text-secondary);
  text-decoration: none;
  font-size: 1.1rem;
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
}

.social-link:hover {
  color: var(--footer-link-hover);
  background: var(--footer-social-hover);
  transform: translateY(-2px);
}

.social-link.admin-link {
  font-size: 0.9rem;
  border: 1px solid var(--border-color);
}

.social-link.admin-link:hover {
  border-color: var(--primary-blue);
}

/* Responsive */
@media (max-width: 768px) {
  .footer-main {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .footer-brand {
    max-width: none;
    text-align: center;
  }
  
  .brand-title {
    justify-content: center;
  }
  
  .footer-bottom {
    flex-direction: column;
    text-align: center;
    gap: 1.5rem;
  }
  
  .social-links {
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .footer-modern {
    padding: 2rem 0 1rem;
  }
  
  .footer-main {
    gap: 1.5rem;
  }
  
  .social-links {
    gap: 0.75rem;
  }
  
  .social-link {
    width: 36px;
    height: 36px;
    font-size: 1rem;
  }
}
</style> 
