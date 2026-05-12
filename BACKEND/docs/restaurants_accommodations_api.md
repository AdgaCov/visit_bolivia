# API de Restaurantes y Alojamientos - Conoce Bolivia

## 🍽️ **Endpoints de Restaurantes**

### **Operaciones Básicas**
- `GET /api/restaurants` - Listar todos los restaurantes
- `GET /api/restaurants/{id}` - Obtener restaurante por ID
- `POST /api/restaurants` - Crear nuevo restaurante
- `PUT /api/restaurants/{id}` - Actualizar restaurante
- `DELETE /api/restaurants/{id}` - Eliminar restaurante

### **Búsquedas y Filtros**
- `GET /api/restaurants/nearby?latitude=X&longitude=Y&radius=Z` - Restaurantes cercanos
- `GET /api/restaurants/search?q=termino` - Buscar restaurantes
- `GET /api/restaurants/by-place/{place_id}` - Restaurantes por lugar
- `GET /api/restaurants/by-badge/{badge}` - Restaurantes por badge (Gourmet, Típico, etc.)
- `GET /api/restaurants/stats` - Estadísticas de restaurantes

### **Gestión de Imágenes**
- `POST /api/restaurants/{restaurant_id}/images` - Agregar imagen
- `PUT /api/restaurants/{restaurant_id}/images/{image_id}` - Actualizar imagen
- `DELETE /api/restaurants/{restaurant_id}/images/{image_id}` - Eliminar imagen
- `PUT /api/restaurants/{restaurant_id}/images/{image_id}/set-main` - Establecer imagen principal

### **Estructura de Datos - Restaurante**
```json
{
  "id": 1,
  "place_id": 1,
  "name": "Restaurante El Buen Sabor",
  "description": "Especialidad en comida tradicional boliviana",
  "location": "Calle Comercio #123, La Paz",
  "latitude": -16.5000,
  "longitude": -68.1500,
  "badge": "Típico",
  "link": "https://example.com/restaurante",
  "facebook": "https://facebook.com/restaurante",
  "instagram": "https://instagram.com/restaurante",
  "whatsapp": "+59112345678",
  "place": {
    "id": 1,
    "name": "Centro Histórico"
  },
  "images": [
    {
      "id": 1,
      "image_url": "https://example.com/image.jpg",
      "is_main": true
    }
  ]
}
```

---

## 🏨 **Endpoints de Alojamientos**

### **Operaciones Básicas**
- `GET /api/accommodations` - Listar todos los alojamientos
- `GET /api/accommodations/{id}` - Obtener alojamiento por ID
- `POST /api/accommodations` - Crear nuevo alojamiento
- `PUT /api/accommodations/{id}` - Actualizar alojamiento
- `DELETE /api/accommodations/{id}` - Eliminar alojamiento

### **Búsquedas y Filtros**
- `GET /api/accommodations/nearby?latitude=X&longitude=Y&radius=Z` - Alojamientos cercanos
- `GET /api/accommodations/search?q=termino` - Buscar alojamientos
- `GET /api/accommodations/by-place/{place_id}` - Alojamientos por lugar
- `GET /api/accommodations/by-room-type/{room_type}` - Alojamientos por tipo de habitación
- `GET /api/accommodations/by-badge/{badge}` - Alojamientos por badge (Ecológico, etc.)
- `GET /api/accommodations/by-price-range?min_price=X&max_price=Y` - Alojamientos por rango de precio
- `GET /api/accommodations/by-capacity?capacity=X` - Alojamientos por capacidad
- `GET /api/accommodations/stats` - Estadísticas de alojamientos

### **Gestión de Imágenes**
- `POST /api/accommodations/{accommodation_id}/images` - Agregar imagen
- `PUT /api/accommodations/{accommodation_id}/images/{image_id}` - Actualizar imagen
- `DELETE /api/accommodations/{accommodation_id}/images/{image_id}` - Eliminar imagen
- `PUT /api/accommodations/{accommodation_id}/images/{image_id}/set-main` - Establecer imagen principal

### **Estructura de Datos - Alojamiento**
```json
{
  "id": 1,
  "place_id": 1,
  "title": "Cabañas Ecológicas en los Andes",
  "description": "Alojamiento sostenible con vista a las montañas",
  "location": "La Paz, Altiplano",
  "room_type": "Cabaña",
  "price_per_night": 120.00,
  "capacity": 4,
  "latitude": -16.5000,
  "longitude": -68.1500,
  "badge": "Ecológico",
  "link": "https://example.com/cabanas",
  "facebook": "https://facebook.com/cabanas",
  "instagram": "https://instagram.com/cabanas",
  "whatsapp": "+59112345678",
  "place": {
    "id": 1,
    "name": "Centro Histórico"
  },
  "images": [
    {
      "id": 1,
      "image_url": "https://example.com/cabana.jpg",
      "is_main": true
    }
  ]
}
```

---

## 📝 **Ejemplos de Uso**

### **Crear Restaurante**
```bash
POST /api/restaurants
Content-Type: application/json

{
  "place_id": 1,
  "name": "Restaurante El Buen Sabor",
  "description": "Especialidad en comida tradicional boliviana",
  "location": "Calle Comercio #123, La Paz",
  "latitude": -16.5000,
  "longitude": -68.1500,
  "badge": "Típico",
  "link": "https://example.com/restaurante",
  "facebook": "https://facebook.com/restaurante",
  "instagram": "https://instagram.com/restaurante",
  "whatsapp": "+59112345678"
}
```

### **Crear Alojamiento**
```bash
POST /api/accommodations
Content-Type: application/json

{
  "place_id": 1,
  "title": "Cabañas Ecológicas en los Andes",
  "description": "Alojamiento sostenible con vista a las montañas",
  "location": "La Paz, Altiplano",
  "room_type": "Cabaña",
  "price_per_night": 120.00,
  "capacity": 4,
  "latitude": -16.5000,
  "longitude": -68.1500,
  "badge": "Ecológico",
  "link": "https://example.com/cabanas",
  "facebook": "https://facebook.com/cabanas",
  "instagram": "https://instagram.com/cabanas",
  "whatsapp": "+59112345678"
}
```

### **Buscar Restaurantes Cercanos**
```bash
GET /api/restaurants/nearby?latitude=-16.5000&longitude=-68.1500&radius=10
```

### **Filtrar Alojamientos por Precio**
```bash
GET /api/accommodations/by-price-range?min_price=50&max_price=200
```

### **Agregar Imagen a Restaurante**
```bash
POST /api/restaurants/1/images
Content-Type: application/json

{
  "image_url": "https://example.com/nueva-imagen.jpg",
  "is_main": false
}
```

---

## 🔍 **Códigos de Respuesta**

- `200` - Éxito
- `201` - Creado exitosamente
- `400` - Error de validación
- `404` - No encontrado
- `500` - Error interno del servidor

## 📊 **Estadísticas Disponibles**

### **Restaurantes**
- Total de restaurantes
- Restaurantes con imágenes
- Distribución por badge

### **Alojamientos**
- Total de alojamientos
- Alojamientos con imágenes
- Distribución por tipo de habitación
- Distribución por badge
- Estadísticas de precios (promedio, mínimo, máximo)

---

## 🚀 **Características Implementadas**

✅ **CRUD completo** para restaurantes y alojamientos
✅ **Búsquedas geográficas** por proximidad
✅ **Filtros avanzados** por múltiples criterios
✅ **Gestión de imágenes** con imagen principal
✅ **Estadísticas** detalladas
✅ **Validación de datos** en servicios
✅ **Manejo de errores** robusto
✅ **Relaciones** con lugares y usuarios
✅ **Coordenadas geográficas** para mapas
✅ **Redes sociales** y contacto
