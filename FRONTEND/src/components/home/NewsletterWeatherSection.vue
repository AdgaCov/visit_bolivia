<template>
  <section class="newsletter-weather-section" role="region" aria-label="Suscripción y clima">
    <div class="nw-container">
      <div class="nw-grid">
        <!-- Newsletter -->
        <div class="nw-card1 nw-newsletter" aria-labelledby="nw-title">
          <div class="nw-inner">
          <h3 id="nw-title" class="nw-title">{{ title || '¡Suscríbete a nuestro boletín!' }}</h3>
          <div class="nw-tabs" role="tablist">
            <button
              class="nw-tab"
              :class="{ 'is-active': activeTab === 'visitor' }"
              type="button"
              role="tab"
              :aria-selected="activeTab === 'visitor'"
              @click="setTab('visitor')"
            >Visitante</button>
            <button
              class="nw-tab"
              :class="{ 'is-active': activeTab === 'business' }"
              type="button"
              role="tab"
              :aria-selected="activeTab === 'business'"
              @click="setTab('business')"
            >Negocios</button>
            <!-- <button
              class="nw-tab"
              :class="{ 'is-active': activeTab === 'nomad' }"
              type="button"
              role="tab"
              :aria-selected="activeTab === 'nomad'"
              @click="setTab('nomad')"
            >Nómada digital</button> -->
          </div>
          <p class="nw-desc" v-if="activeTab === 'visitor'">{{ visitorDescription || 'Mantente al día con noticias, ofertas especiales, próximos eventos y más.' }}</p>
          <p class="nw-desc" v-else-if="activeTab === 'business'">{{ businessDescription || 'No te pierdas nada que te ayude a presentar Bolivia a tus clientes.' }}</p>
          <p class="nw-desc" v-else>{{ nomadDescription || 'Consejos, visas y oportunidades para trabajadores remotos.' }}</p>

          <!-- Visitor form (simple) -->
          <form v-if="activeTab === 'visitor'" class="nw-form" @submit.prevent="handleVisitorRegister">
            <label class="nw-label" for="nw-name">Nombre</label>
            <input
              id="nw-name"
              type="text"
              class="nw-input"
              v-model="visitorName"
              placeholder="Tu nombre"
              autocomplete="name"
              required
            />

            <label class="nw-label" for="nw-email">Correo electrónico</label>
            <input
              id="nw-email"
              type="email"
              class="nw-input"
              v-model="visitorEmail"
              placeholder="tu@correo.com"
              autocomplete="email"
              required
            />

            <label class="nw-label" for="nw-password">Contraseña</label>
            <input
              id="nw-password"
              type="password"
              class="nw-input"
              v-model="visitorPassword"
              placeholder="Mínimo 6 caracteres"
              autocomplete="new-password"
              required
            />

            <button class="nw-btn" type="submit" :disabled="visitorLoading">
              <span v-if="visitorLoading">Enviando...</span>
              <span v-else>SUSCRIBIRME</span>
            </button>
          </form>
          <p v-if="visitorError" class="nw-register-error">{{ visitorError }}</p>
          <p v-if="visitorSuccess" class="nw-register-success">{{ visitorSuccess }}</p>

          <!-- Business form (same fields as visitor) -->
          <form
            v-else-if="activeTab === 'business'"
            class="nw-form"
            @submit.prevent="handleBusinessRegister"
          >

            <!-- Empresa -->
            <label class="nw-label" for="nw-biz-company">Empresa o Negocio</label>
            <input
              id="nw-biz-company"
              type="text"
              class="nw-input"
              v-model="businessOwner"
              placeholder="Nombre de tu Empresa o Negocio"
              autocomplete="organization"
              required
            />

            <!-- Sector -->
            <label class="nw-label" for="nw-biz-sector">Sector</label>
            <select
              id="nw-biz-sector"
              class="nw-input"
              v-model="businessSector"
              required
            >
              <option value="" disabled>Selecciona un sector</option>
              <option value="gastronomico">Gastronómico</option>
              <option value="hotelero">Hotelero</option>
              <option value="atractivo">Atractivo</option>
              <option value="evento">Evento</option>
              <option value="otro">Otro</option>
            </select>

            <!-- Sector personalizado (solo si es "otro") -->
            <label
              v-if="businessSector === 'otro'"
              class="nw-label"
              for="nw-biz-sector-other"
            >
              Especifica el sector
            </label>
            <input
              v-if="businessSector === 'otro'"
              id="nw-biz-sector-other"
              type="text"
              class="nw-input"
              v-model="businessSectorOther"
              placeholder="Ej: Transporte, Turismo rural, Cultural..."
              required
            />

            <!-- Nombre del responsable -->
            <label class="nw-label" for="nw-biz-owner">Nombre</label>
            <input
              id="nw-biz-owner"
              type="text"
              class="nw-input"
              v-model="businessName"
              placeholder="Tu nombre"
              autocomplete="name"
              required
            />

            <!-- Correo -->
            <label class="nw-label" for="nw-biz-email">Correo electrónico</label>
            <input
              id="nw-biz-email"
              type="email"
              class="nw-input"
              v-model="businessEmail"
              placeholder="tu@correo.com"
              autocomplete="email"
              required
            />

            <!-- Contraseña -->
            <label class="nw-label" for="nw-biz-password">Contraseña</label>
            <input
              id="nw-biz-password"
              type="password"
              class="nw-input"
              v-model="businessPassword"
              placeholder="Mínimo 6 caracteres"
              autocomplete="new-password"
              required
            />

            <!-- Botón -->
            <button class="nw-btn" type="submit" :disabled="businessLoading">
              <span v-if="businessLoading">Enviando...</span>
              <span v-else>SUSCRIBIRME</span>
            </button>

          </form>

          <p v-if="businessError" class="nw-register-error">{{ businessError }}</p>
          <p v-if="businessSuccess" class="nw-register-success">{{ businessSuccess }}</p>

          <!-- Nomad form (short) -->
          <!-- <form v-else class="nw-form" @submit.prevent>
            <label class="nw-label" for="nw-nomad-email">Correo electrónico</label>
            <input id="nw-nomad-email" type="email" class="nw-input" placeholder="tu@correo.com" autocomplete="email" />
            <button class="nw-btn" type="submit">SUSCRIBIRME</button>
          </form> -->

          <!-- Google login (solo para visitante) -->
          <div v-if="activeTab === 'visitor'" class="nw-google-wrapper" aria-label="Iniciar sesión con Google">
            <!-- Botón oficial de Google (renderizado por el SDK) -->
            <div
              v-if="googleInitialized"
              ref="googleBtn"
              class="nw-google-placeholder"
            ></div>

            <!-- Fallback: botón propio si el SDK aún no está listo -->
            <button
              v-else
              type="button"
              class="nw-google-fallback-btn"
              @click="loadGoogleSdk"
            >
              <i class="fab fa-google nw-google-icon"></i>
              Iniciar sesión con Google
            </button>

            <p v-if="googleError" class="nw-google-error">{{ googleError }}</p>
          </div>
          <p class="nw-privacy">
            Al suscribirte, aceptas el tratamiento de datos personales según la <router-link to="#" class="nw-link">política de privacidad</router-link>.
          </p>
          </div>
        </div>

        <!-- Modal de confirmación de registro -->
        <div
          v-if="showRegisterModal"
          class="nw-modal-backdrop"
          role="dialog"
          aria-modal="true"
        >
          <div class="nw-modal">
            <h4 class="nw-modal-title">Registro completado</h4>
            <p class="nw-modal-text">
              {{ registerModalMessage || 'Te has registrado correctamente. Revisa tu correo si es necesario confirmar la cuenta.' }}
            </p>
            <button type="button" class="nw-btn" @click="showRegisterModal = false">
              Cerrar
            </button>
          </div>
        </div>

        <!-- Weather (estático placeholder) -->
        <div class="nw-card2 nw-weather" aria-label="Widget del clima">
          <div class="nw-inner">
          <div class="nw-weather-row">
            <div class="nw-weather-left">
              <div class="nw-weather-icon" aria-hidden="true">
                <i :class="weatherIcon || 'fas fa-cloud-sun'"></i>
              </div>
              <div class="nw-temp">{{ weatherTemp || '22°' }}<span class="nw-temp-suffix">C</span></div>
              <div class="nw-cond">{{ weatherCondition || 'Parcialmente nublado' }}</div>
            </div>
            <div class="nw-weather-right">
              <div class="nw-time">{{ weatherTime || '14:30' }}</div>
              <div class="nw-tz">{{ weatherTz || 'GMT-4' }}</div>
            </div>
          </div>
          <div class="nw-credit">Datos del clima: <span>{{ weatherSource || 'OpenWeatherMap' }}</span></div>
          <div class="nw-map-decor" aria-hidden="true"></div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import authService from '@/services/auth.service'

export default {
  name: 'NewsletterWeatherSection',
  props: {
    title: {
      type: String,
      default: ''
    },
    visitorDescription: {
      type: String,
      default: ''
    },
    businessDescription: {
      type: String,
      default: ''
    },
    nomadDescription: {
      type: String,
      default: ''
    },
    weatherTemp: {
      type: String,
      default: ''
    },
    weatherCondition: {
      type: String,
      default: ''
    },
    weatherTime: {
      type: String,
      default: ''
    },
    weatherTz: {
      type: String,
      default: ''
    },
    weatherSource: {
      type: String,
      default: ''
    },
    weatherIcon: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      activeTab: 'visitor',
      googleInitialized: false,
      googleError: '',
      visitorName: '',
      visitorEmail: '',
      visitorPassword: '',
      visitorLoading: false,
      visitorError: '',
      visitorSuccess: '',
      businessOwner:'',
      businessSector: '',
      businessSectorOther:'',
      businessName: '',
      businessEmail: '',
      businessPassword: '',
      businessLoading: false,
      businessError: '',
      businessSuccess: '',
      showRegisterModal: false,
      registerModalMessage: '',
      
    }
  },
  mounted() {
    console.log('🌤️ NewsletterWeatherSection mounted con props:', {
      weatherTemp: this.weatherTemp,
      weatherCondition: this.weatherCondition,
      weatherTime: this.weatherTime,
      weatherTz: this.weatherTz,
      weatherSource: this.weatherSource
    })
    this.loadGoogleSdk()
  },
  watch: {
    weatherTemp(newVal) {
      console.log('🌡️ weatherTemp cambió a:', newVal)
    },
    weatherCondition(newVal) {
      console.log('☁️ weatherCondition cambió a:', newVal)
    },
    weatherTime(newVal) {
      console.log('🕐 weatherTime cambió a:', newVal)
    },
    weatherTz(newVal) {
      console.log('🌍 weatherTz cambió a:', newVal)
    },
    weatherSource(newVal) {
      console.log('📡 weatherSource cambió a:', newVal)
    },
    weatherIcon(newVal) {
      console.log('🌤️ weatherIcon cambió a:', newVal)
    }
  },
  methods: {
    setTab(tab) {
      this.activeTab = tab
      // Limpiar errores y mensajes al cambiar de tab
      this.visitorError = ''
      this.visitorSuccess = ''
      this.businessError = ''
      this.businessSuccess = ''
    },
    async handleVisitorRegister() {
      this.visitorError = ''
      this.visitorSuccess = ''

      if (!this.visitorName || !this.visitorEmail || !this.visitorPassword) {
        this.visitorError = 'Por favor completa nombre, correo y contraseña.'
        return
      }

      if (this.visitorPassword.length < 6) {
        this.visitorError = 'La contraseña debe tener al menos 6 caracteres.'
        return
      }

      this.visitorLoading = true
      try {
        const payload = {
          name: this.visitorName,
          email: this.visitorEmail,
          password: this.visitorPassword,
          role_id: 4,
          status: 1
        }
        const response = await authService.register(payload)
        console.log('✅ Registro exitoso:', response)
        const backendMsg = response?.message || response?.data?.message
        this.visitorSuccess = backendMsg || 'Te has registrado correctamente.'
        // Opcional: limpiar campos
        this.visitorPassword = ''
      this.visitorEmail = ''
      this.visitorName = ''
      this.registerModalMessage = this.visitorSuccess
      this.showRegisterModal = true
      } catch (err) {
        console.error('❌ Error en registro:', err)
        this.visitorError = err?.response?.data?.message || err?.message || 'No se pudo completar el registro.'
      } finally {
        this.visitorLoading = false
      }
    },
    async handleBusinessRegister() {
      this.businessError = ''
      this.businessSuccess = ''

      if (!this.businessOwner||!this.businessSector||!this.businessName || !this.businessEmail || !this.businessPassword || (this.businessSector === 'otro' && !this.businessSectorOther)) {
        this.businessError = 'Por favor completa nombre o negocio, sector nombre, correo y contraseña.'
        return
      }

      if (this.businessPassword.length < 6) {
        this.businessError = 'La contraseña debe tener al menos 6 caracteres.'
        return
      }
      const finalSector =
        this.businessSector === 'otro'
          ? this.businessSectorOther
          : this.businessSector

      this.businessLoading = true
      try {
        const payload = {
          business:this.businessOwner,
          sector: finalSector,
          name: this.businessName,
          email: this.businessEmail,
          password: this.businessPassword,
          role_id: 3,
          status: 0,
          plan_id: 1
        }
        const response = await authService.register(payload)
        console.log('✅ Registro de negocio exitoso:', response)
        const backendMsg = response?.message || response?.data?.message
        this.businessSuccess = backendMsg || 'Te has registrado correctamente. Tu cuenta está pendiente de aprobación.'
        // Limpiar campos
        this.businessPassword = ''
        this.businessEmail = ''
        this.businessName = ''
        this.businessOwner=''
        this.businessSector=''
        this.registerModalMessage = this.businessSuccess
        this.showRegisterModal = true
      } catch (err) {
        console.error('❌ Error en registro de negocio:', err)
        this.businessError = err?.response?.data?.message || err?.message || 'No se pudo completar el registro.'
      } finally {
        this.businessLoading = false
      }
    },
    loadGoogleSdk() {
      try {
        if (window.google && window.google.accounts && window.google.accounts.id) {
          this.initGoogle()
          return
        }
      } catch (e) {
        // window.google aún no existe
      }

      const existingScript = document.querySelector('script[src="https://accounts.google.com/gsi/client"]')
      if (existingScript) {
        existingScript.addEventListener('load', () => {
          this.initGoogle()
        })
        return
      }

      const script = document.createElement('script')
      script.src = 'https://accounts.google.com/gsi/client'
      script.async = true
      script.defer = true
      script.onload = () => {
        this.initGoogle()
      }
      script.onerror = () => {
        this.googleError = 'No se pudo cargar Google. Intenta de nuevo más tarde.'
      }
      document.head.appendChild(script)
    },
    initGoogle() {
      console.log('🔹 initGoogle llamado')
      try {
        const clientId =
          (typeof process !== 'undefined' && process.env && process.env.VUE_APP_GOOGLE_CLIENT_ID) ||
          ''

        if (!clientId) {
          console.warn('⚠️ Falta configurar VUE_APP_GOOGLE_CLIENT_ID')
          this.googleError = 'Falta configurar el Cliente de Google en el frontend.'
          return
        }

        const google = window.google
        if (!google || !google.accounts || !google.accounts.id) {
          this.googleError = 'SDK de Google no está disponible.'
          return
        }

        google.accounts.id.initialize({
          client_id: clientId,
          callback: this.handleGoogleCredential
        })

        if (this.$refs.googleBtn) {
          console.log('🧩 Renderizando botón oficial de Google en ref googleBtn')
          google.accounts.id.renderButton(this.$refs.googleBtn, {
            type: 'standard',
            theme: 'outline',
            size: 'large',
            text: 'continue_with',
            shape: 'pill',
            logo_alignment: 'left',
            width: 260
          })
          this.googleInitialized = true
        } else {
          console.warn('⚠️ No se encontró ref googleBtn al inicializar Google')
        }
      } catch (e) {
        console.error('Error inicializando Google Identity:', e)
        this.googleError = 'No se pudo inicializar el inicio de sesión con Google.'
      }
    },
    async handleGoogleCredential(response) {
      try {
        const idToken = response && response.credential
        if (!idToken) {
          this.googleError = 'No se recibió el token de Google.'
          return
        }

        this.googleError = ''
        // Enviar el id_token a tu API
        const apiResponse = await authService.loginWithGoogle({ id_token: idToken })
        console.log('✅ Login con Google exitoso:', apiResponse)

        // Emitir evento para que el padre pueda reaccionar (guardar sesión, etc.)
        this.$emit('google-login-success', apiResponse)
      } catch (err) {
        console.error('❌ Error al iniciar sesión con Google:', err)
        const msg = err?.response?.data?.message || err?.message || 'No se pudo iniciar sesión con Google.'
        this.googleError = msg
        this.$emit('google-login-error', err)
      }
    }
  }
}
</script>

<style scoped>
.newsletter-weather-section { padding: 2.5rem 0; background: var(--bg-primary); }
/* Contenedor full-bleed (rompe el max-width de la página) */
.nw-container { width: 100vw; margin-left: 50%; transform: translateX(-50%); padding: 0; }

.nw-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

.nw-card1 {
  position: relative;
  border-radius: 0 50px 50px 0; 
  background: var(--bg-secondary);/* suave lavanda */
  padding: 2rem; 
  min-height: 280px; 
  overflow: hidden;
}

.nw-card2 {
  position: relative;
  border-radius: 50px 0 0 50px; 
  padding: 2rem; 
  min-height: 280px; 
  overflow: hidden;
}

/* Padding interno de seguridad para texto en extremos */
.nw-newsletter .nw-title,
.nw-newsletter .nw-desc,
.nw-newsletter .nw-form,
.nw-newsletter .nw-privacy { padding-left: 0.5rem; }
.nw-weather .nw-weather-row,
.nw-weather .nw-credit { padding-right: 0.5rem; }

/* Full-bleed edges (sin padding/margen lateral) */
.nw-newsletter { padding-left: 1rem; margin-left: 0; }
.nw-weather { padding-right: 1rem; margin-right: 0; }

/* Centro y padding interno consistente */
.nw-inner { 
  max-width: 680px; 
  margin: 0 auto; 
  padding: 1rem 1.25rem; 
}

/* Newsletter */
.nw-title { font-size: 2.4rem; font-weight: 400; color: var(--text-primary); margin: 0 0 1.25rem 0; letter-spacing: -0.01em; }
.nw-tabs { display: flex; gap: 1.75rem; border-bottom: 2px solid rgba(0,0,0,0.08); margin-bottom: 1.25rem; }
.nw-tab { background: transparent; border: 0; padding: 0.6rem 0; color: var(--text-secondary); cursor: pointer; font-weight: 500; font-size: 1.05rem; }
.nw-tab.is-active { color: #1B1B1B; border-bottom: 3px solid var(--primary-blue); }
.nw-desc { color: var(--text-secondary); margin: 0.75rem 0 1.25rem 0; font-size: 1.05rem; }

.nw-form { display: grid; grid-template-columns: 1fr auto; gap: 0.75rem; align-items: end; max-width: 520px; }
.nw-label { grid-column: 1 / -1; font-size: 0.9rem; color: var(--text-secondary); margin: 0; }
.nw-input { width: 100%; height: 46px; border: 0; border-bottom: 2px solid rgba(0,0,0,0.2); background: transparent; outline: none; font-size: 1.05rem; }
.nw-input:focus { border-color: var(--primary-blue); }
.nw-btn { 
  height: 46px; 
  padding: 0 1.5rem; 
  border-radius: 10px; 
  background: var(--light-gray); 
  color: var(--text-primary); 
  border: 1px solid var(--border-color); 
  font-weight: var(--font-weight-medium); 
  letter-spacing: 0.02em; 
  cursor: pointer; 
  font-size: 0.95rem; 
  box-shadow: var(--shadow-sm); 
  transition: background .2s ease, box-shadow .2s ease, transform .15s ease, color .2s ease, border-color .2s ease; 
}
.nw-btn:hover { 
  background: var(--medium-gray); 
  color: var(--text-primary); 
  transform: translateY(-1px); 
  box-shadow: var(--shadow-md); 
}
.nw-privacy { margin-top: 0.75rem; font-size: 0.85rem; color: var(--text-secondary); }
.nw-link { color: var(--primary-blue); text-decoration: underline; }

.nw-register-error {
  margin-top: 0.5rem;
  font-size: 0.85rem;
  color: #dc3545;
}

.nw-register-success {
  margin-top: 0.5rem;
  font-size: 0.85rem;
  color: #198754;
}

/* Modal de registro */
.nw-modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.nw-modal {
  background: #fff;
  border-radius: 16px;
  padding: 1.75rem 2rem;
  max-width: 420px;
  width: 100%;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  text-align: center;
}

.nw-modal-title {
  font-size: 1.4rem;
  margin-bottom: 0.75rem;
  color: var(--text-primary);
}

.nw-modal-text {
  font-size: 0.98rem;
  color: var(--text-secondary);
  margin-bottom: 1.25rem;
}

/* Business form layout */
.nw-form-rows { display: grid; grid-template-columns: 1fr; gap: 0.9rem; max-width: 520px; }
.nw-field { display: grid; gap: 0.25rem; }
.nw-input-line { width: 100%; height: 44px; border: 0; border-bottom: 2px solid rgba(0,0,0,0.2); background: transparent; outline: none; font-size: 1.05rem; }
.nw-input-line:focus { border-color: var(--primary-blue); }
.nw-actions { display: flex; justify-content: flex-end; margin-top: 0.25rem; }

/* Weather */
.nw-weather { background: var(--bg-secondary); }
.nw-weather-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
.nw-weather-left { display: grid; grid-template-columns: auto auto; align-items: center; gap: 0.75rem 1rem; }
.nw-weather-icon {
  width: 56px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.nw-weather-icon i {
  font-size: 28px;
  color: #64b5f6;
  text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

/* Iconos específicos con colores */
.nw-weather-icon .fa-sun {
  color: #ffb74d;
}

.nw-weather-icon .fa-cloud-sun {
  color: #ffa726;
}

.nw-weather-icon .fa-cloud {
  color: #90a4ae;
}

.nw-weather-icon .fa-cloud-rain,
.nw-weather-icon .fa-cloud-drizzle {
  color: #42a5f5;
}

.nw-weather-icon .fa-bolt {
  color: #ff7043;
}

.nw-weather-icon .fa-snowflake {
  color: #e1f5fe;
}

.nw-weather-icon .fa-smog {
  color: #bdbdbd;
}
.nw-temp { font-size: 2.6rem; font-weight: 400; color: var(--text-primary); }
.nw-temp-suffix { font-size: 1rem; margin-left: 2px; }
.nw-cond { grid-column: 1 / -1; color: var(--text-secondary); font-size: 1.05rem; }
.nw-weather-right { text-align: right; }
.nw-time { font-size: 2.3rem; font-weight: 400; color: var(--text-primary); }
.nw-tz { color: var(--text-secondary); }
.nw-credit { position: absolute; left: 1.5rem; bottom: 1rem; font-size: 0.9rem; color: var(--text-secondary); }
.nw-map-decor { position: absolute; right: -10%; bottom: -20%; width: 70%; height: 110%; background: rgba(0,0,0,0.05); border-radius: 40% 60% 50% 50% / 50% 40% 60% 50%; transform: rotate(10deg); }

/* Google login */
.nw-google-wrapper {
  margin-top: 1.25rem;
}

.nw-google-placeholder {
  display: inline-block;
}

.nw-google-error {
  margin-top: 0.5rem;
  font-size: 0.85rem;
  color: #dc3545;
}

.nw-google-fallback-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 999px;
  border: 1px solid var(--border-color);
  background: #fff;
  color: var(--text-primary);
  font-size: 0.95rem;
  cursor: pointer;
  box-shadow: var(--shadow-sm);
  transition: background 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
}

.nw-google-fallback-btn:hover {
  background: var(--light-gray);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

.nw-google-icon {
  color: #4285f4;
}

/* Responsive */
@media (max-width: 992px) {
  .nw-grid { grid-template-columns: 1fr; }
}
</style>

