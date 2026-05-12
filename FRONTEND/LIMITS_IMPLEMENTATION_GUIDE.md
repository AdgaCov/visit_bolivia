# 📋 Guía de Implementación: Sistema de Límites Escalable

## 🎯 ¿Por qué una tabla separada?

En lugar de agregar campos como `max_locations` y `max_images_per_location` directamente a la tabla `roles`, creamos una tabla **`role_limits`** que es:

- ✅ **Escalable**: Puedes agregar nuevos tipos de límites sin modificar la estructura
- ✅ **Flexible**: Fácil de actualizar y gestionar
- ✅ **Normalizada**: Evita redundancia de datos
- ✅ **Mantenible**: Cambios centralizados en un solo lugar

---

## 📊 Estructura Propuesta

### Tabla: `role_limits`

```sql
CREATE TABLE `role_limits` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `role_id` INT NOT NULL,
  `limit_type` VARCHAR(50) NOT NULL,  -- 'max_locations', 'max_images_per_location', etc.
  `limit_value` INT NOT NULL DEFAULT 0,
  `description` VARCHAR(255),
  UNIQUE KEY `unique_role_limit_type` (`role_id`, `limit_type`)
);
```

**Ventajas:**
- Cada rol puede tener múltiples tipos de límites
- Fácil agregar nuevos tipos: solo INSERT, no ALTER TABLE
- Consulta eficiente con índices

---

## 🚀 Implementación Backend (Laravel)

### 1. Modelo `RoleLimit`

```php
// app/Models/RoleLimit.php
class RoleLimit extends Model
{
    protected $fillable = ['role_id', 'limit_type', 'limit_value', 'description'];
    
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
    // Obtener límite de un usuario por tipo
    public static function getUserLimit($userId, $limitType)
    {
        $user = User::find($userId);
        if (!$user) return 0;
        
        $limit = self::where('role_id', $user->role_id)
            ->where('limit_type', $limitType)
            ->value('limit_value');
            
        return $limit ?? 0; // Retorna 0 si no encuentra límite
    }
}
```

### 2. Validación en Controladores

```php
// app/Http/Controllers/LocationController.php
public function store(Request $request)
{
    $user = auth()->user();
    
    // Obtener límite de locations
    $maxLocations = RoleLimit::getUserLimit($user->id, 'max_locations');
    
    // Contar locations actuales
    $currentCount = Location::where('user_id', $user->id)->count();
    
    if ($currentCount >= $maxLocations) {
        return response()->json([
            'success' => false,
            'message' => "Has alcanzado el límite de {$maxLocations} locations."
        ], 403);
    }
    
    // Continuar con la creación...
}

// Para imágenes
public function storeImage(Request $request)
{
    $location = Location::findOrFail($request->location_id);
    $user = $location->user;
    
    $maxImages = RoleLimit::getUserLimit($user->id, 'max_images_per_location');
    $currentCount = LocationImage::where('location_id', $location->id)->count();
    
    if ($currentCount >= $maxImages) {
        return response()->json([
            'success' => false,
            'message' => "Esta location ya tiene el máximo de {$maxImages} imágenes."
        ], 403);
    }
    
    // Continuar...
}
```

### 3. Endpoint para obtener límites del usuario

```php
// app/Http/Controllers/UserController.php
public function getLimits()
{
    $user = auth()->user();
    
    $limits = RoleLimit::where('role_id', $user->role_id)
        ->get()
        ->mapWithKeys(function ($limit) {
            return [$limit->limit_type => $limit->limit_value];
        });
    
    return response()->json([
        'success' => true,
        'data' => $limits
    ]);
}
```

---

## 🎨 Implementación Frontend (Vue.js)

### 1. Service para límites

```typescript
// src/services/admin/limits.admin.service.ts
import adminApiClient from './api.admin'
import ADMIN_API_ENDPOINTS from './endpoints.admin'

export interface UserLimits {
  max_locations: number
  max_images_per_location: number
  max_reviews_per_day?: number
}

export interface UserLimitsResponse {
  success: boolean
  data: UserLimits
}

class LimitsService {
  async getUserLimits(): Promise<UserLimitsResponse> {
    return adminApiClient.get<UserLimitsResponse>('/api/admin/users/limits')
  }
}

export const limitsService = new LimitsService()
export default limitsService
```

### 2. Validación en `AdminLocations.vue`

```vue
<script>
import limitsService from '@/services/admin/limits.admin.service'
import { useAuthStore } from '@/store/auth'

export default {
  data() {
    return {
      userLimits: {
        max_locations: 999,
        max_images_per_location: 5
      }
    }
  },
  async mounted() {
    await this.loadUserLimits()
  },
  methods: {
    async loadUserLimits() {
      try {
        const response = await limitsService.getUserLimits()
        if (response.success) {
          this.userLimits = response.data
        }
      } catch (error) {
        console.error('Error cargando límites:', error)
      }
    },
    
    async save() {
      const authStore = useAuthStore()
      const user = authStore.currentUser
      
      // Validar límite solo al crear
      if (this.form.id === 0) {
        const currentCount = await this.countUserLocations(user.id)
        
        if (currentCount >= this.userLimits.max_locations) {
          alert(`Has alcanzado el límite de ${this.userLimits.max_locations} locations.`)
          return
        }
      }
      
      // Continuar con el guardado...
    },
    
    async countUserLocations(userId) {
      // Llamar al backend para obtener el conteo
      const response = await adminLocationsService.getCountByUser(userId)
      return response.data.count || 0
    }
  }
}
</script>

<template>
  <div>
    <!-- Indicador de límite -->
    <div v-if="userLimits" class="limit-indicator">
      <small>
        Locations: {{ currentLocationsCount }} / {{ userLimits.max_locations }}
      </small>
    </div>
    
    <!-- Botón deshabilitado si alcanzó el límite -->
    <button 
      @click="openNew" 
      :disabled="currentLocationsCount >= userLimits.max_locations"
    >
      Crear Location
    </button>
  </div>
</template>
```

### 3. Validación en `AdminLocationImages.vue`

```javascript
async saveImage() {
  if (!form.location_id) {
    alert('Selecciona la locación')
    return
  }
  
  const authStore = useAuthStore()
  const user = authStore.currentUser
  
  // Obtener límite de imágenes
  const limitsResponse = await limitsService.getUserLimits()
  const maxImages = limitsResponse.data.max_images_per_location || 5
  
  // Contar imágenes actuales
  const currentImages = this.images.filter(
    img => img.location_id === parseInt(form.location_id)
  )
  
  const count = editing.value ? currentImages.length : currentImages.length + 1
  
  if (count > maxImages) {
    alert(`Esta location ya tiene el máximo de ${maxImages} imágenes permitidas.`)
    return
  }
  
  // Continuar...
}
```

---

## 🔧 Menú según Rol

### `AdminSidebar.vue`

```vue
<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/store/auth'

const authStore = useAuthStore()
const user = computed(() => authStore.currentUser)

// Computed para verificar roles
const isSuperAdmin = computed(() => user.value?.role_id === 1)
const isAdmin = computed(() => user.value?.role_id === 2)
const isBusiness = computed(() => user.value?.role_id === 3)

// Función para verificar permisos
const canAccess = (permission) => {
  if (!user.value) return false
  
  // Super Admin tiene acceso total
  if (user.value.role_id === 1) return true
  
  // Admin puede ver contenido pero no usuarios
  if (user.value.role_id === 2) {
    return permission !== 'users'
  }
  
  // Negocio solo puede ver sus propias cosas
  if (user.value.role_id === 3) {
    return ['my-locations', 'my-images'].includes(permission)
  }
  
  return false
}
</script>

<template>
  <nav class="sidebar-nav">
    <!-- Solo Super Admin y Admin -->
    <router-link 
      v-if="canAccess('content')"
      to="/admin/content/locations"
    >
      Locaciones
    </router-link>
    
    <!-- Solo Super Admin -->
    <router-link 
      v-if="isSuperAdmin"
      to="/admin/users"
    >
      Usuarios
    </router-link>
    
    <!-- Negocio: Solo sus locations -->
    <router-link 
      v-if="isBusiness"
      to="/admin/content/my-locations"
    >
      Mis Locaciones
    </router-link>
  </nav>
</template>
```

---

## 📝 Ejemplos de Consultas SQL

### Obtener límites de un usuario

```sql
-- Opción 1: Usando la vista
SELECT * FROM v_user_limits WHERE user_id = 1;

-- Opción 2: JOIN manual
SELECT 
    u.id AS user_id,
    u.name AS user_name,
    r.name AS role_name,
    rl.limit_type,
    rl.limit_value
FROM users u
INNER JOIN roles r ON u.role_id = r.id
LEFT JOIN role_limits rl ON r.id = rl.role_id
WHERE u.id = 1;

-- Opción 3: Obtener un límite específico
SELECT limit_value 
FROM role_limits 
WHERE role_id = (SELECT role_id FROM users WHERE id = 1)
  AND limit_type = 'max_locations';
```

### Agregar nuevo tipo de límite

```sql
-- Agregar límite de artículos sin modificar estructura
INSERT INTO role_limits (role_id, limit_type, limit_value, description) VALUES
(3, 'max_articles', 5, 'Máximo de artículos que puede crear'),
(2, 'max_articles', 50, 'Máximo de artículos que puede crear'),
(1, 'max_articles', 9999, 'Máximo de artículos que puede crear');
```

### Actualizar límites

```sql
-- Cambiar límite de locations para negocios
UPDATE role_limits 
SET limit_value = 20 
WHERE role_id = 3 AND limit_type = 'max_locations';
```

---

## ✅ Ventajas de esta Estructura

1. **Escalabilidad**: Agregar nuevos límites es solo un INSERT
2. **Mantenibilidad**: Cambios centralizados en una tabla
3. **Flexibilidad**: Diferentes roles pueden tener diferentes combinaciones
4. **Performance**: Consultas eficientes con índices
5. **Auditoría**: Fácil rastrear cambios históricos si agregas timestamps

---

## 🎯 Siguientes Pasos

1. ✅ Ejecutar el SQL de creación de tablas
2. ✅ Migrar datos existentes (si hay límites definidos)
3. ✅ Implementar validación en backend
4. ✅ Implementar validación en frontend
5. ✅ Agregar indicadores visuales de límites
6. ✅ Crear panel de administración de límites (opcional)

