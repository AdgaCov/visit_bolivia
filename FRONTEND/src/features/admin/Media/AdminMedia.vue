<template>
  <div class="container-fluid p-4">
    <div class="row mb-4">
      <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
          <h1 class="h3 mb-1">
            <i class="fas fa-images text-primary me-2"></i>
            Biblioteca de medios
          </h1>
          <p class="text-muted">Sube y gestiona imágenes, logos y videos</p>
        </div>
        <div>
          <label class="btn btn-primary mb-0">
            <i class="fas fa-upload me-2"></i>
            Subir archivos
            <input type="file" multiple accept="image/*,video/*" class="d-none" @change="onUpload">
          </label>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-6 col-md-3 col-xl-2" v-for="file in files" :key="file.id">
                <div class="media-card">
                  <img v-if="file.type.startsWith('image/')" :src="file.url" :alt="file.name">
                  <div v-else class="media-placeholder">
                    <i class="fas fa-video"></i>
                  </div>
                  <div class="media-meta">
                    <div class="name" :title="file.name">{{ file.name }}</div>
                    <div class="actions">
                      <button class="btn btn-sm btn-outline-secondary" @click="copy(file.url)"><i class="fas fa-link"></i></button>
                      <button class="btn btn-sm btn-outline-danger" @click="remove(file.id)"><i class="fas fa-trash"></i></button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
let nextId = 4
export default {
  name: 'AdminMedia',
  data() {
    return {
      files: [
        { id: 1, name: 'logo_hotel.png', type: 'image/png', url: '/images/5.png' },
        { id: 2, name: 'menu_restaurante.jpg', type: 'image/jpeg', url: '/images/12.png' },
        { id: 3, name: 'promo_video.mp4', type: 'video/mp4', url: '/videos/conocebolivia640x360.mp4' }
      ]
    }
  },
  methods: {
    onUpload(e) {
      const files = Array.from(e.target.files || [])
      for (const f of files) {
        this.files.unshift({ id: nextId++, name: f.name, type: f.type, url: URL.createObjectURL(f) })
      }
    },
    copy(url) {
      navigator.clipboard?.writeText(url)
      alert('URL copiada')
    },
    remove(id) {
      this.files = this.files.filter(f => f.id !== id)
    }
  }
}
</script>

<style scoped>
.media-card {
  border: 1px solid var(--border-light);
  border-radius: 10px;
  overflow: hidden;
  background: var(--white);
}
.media-card img, .media-placeholder {
  width: 100%;
  height: 140px;
  object-fit: cover;
  display: block;
  background: var(--light-gray);
}
.media-placeholder { display: grid; place-items: center; color: var(--text-secondary); }
.media-meta { padding: 0.5rem; display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
.name { font-size: .85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.actions .btn { padding: .2rem .4rem; }
</style>



