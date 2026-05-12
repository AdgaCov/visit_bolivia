# API de Eventos - Ejemplos de Uso

## 📅 Endpoints de Eventos

### 1. Obtener todos los eventos
```http
GET /api/events
```

**Respuesta:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "place_id": 1,
            "name": "Festival de Música Andina",
            "description": "Gran festival de música tradicional boliviana",
            "start_date": "2024-03-15T18:00:00.000000Z",
            "end_date": "2024-03-17T23:00:00.000000Z",
            "event_type": "festival",
            "price": "50.00",
            "is_recurring": false,
            "latitude": "-16.50000000",
            "longitude": "-68.15000000",
            "place": {
                "id": 1,
                "name": "Plaza Murillo",
                "address": "Centro de La Paz"
            },
            "images": [
                {
                    "id": 1,
                    "image_url": "https://example.com/festival.jpg",
                    "alt_text": "Festival de Música Andina",
                    "is_main": true
                }
            ]
        }
    ]
}
```

### 2. Crear un nuevo evento
```http
POST /api/events
Content-Type: application/json

{
    "place_id": 1,
    "name": "Nuevo Festival Cultural",
    "description": "Descripción del evento",
    "start_date": "2024-04-15 18:00:00",
    "end_date": "2024-04-15 22:00:00",
    "event_type": "festival",
    "price": 25.50,
    "is_recurring": false,
    "latitude": -16.5000,
    "longitude": -68.1500
}
```

### 3. Obtener eventos próximos
```http
GET /api/events/upcoming
```

### 4. Buscar eventos por nombre
```http
GET /api/events/search?name=festival
```

### 5. Obtener eventos por tipo
```http
GET /api/events/by-type/festival
```

### 6. Obtener eventos por lugar
```http
GET /api/events/by-place/1
```

### 7. Obtener eventos cercanos
```http
GET /api/events/nearby?latitude=-16.5000&longitude=-68.1500&radius=10
```

### 8. Obtener eventos por rango de fechas
```http
GET /api/events/by-date-range?start_date=2024-03-01&end_date=2024-03-31
```

### 9. Obtener eventos de hoy
```http
GET /api/events/today
```

### 10. Obtener eventos de esta semana
```http
GET /api/events/this-week
```

### 11. Obtener eventos de este mes
```http
GET /api/events/this-month
```

### 12. Obtener eventos recurrentes
```http
GET /api/events/recurring
```

### 13. Obtener estadísticas de eventos
```http
GET /api/events/stats
```

**Respuesta:**
```json
{
    "success": true,
    "data": {
        "total_events": 25,
        "upcoming_events": 15,
        "recurring_events": 5,
        "events_this_month": 8
    }
}
```

## 🖼️ Endpoints de Imágenes de Eventos

### 1. Agregar imagen a evento
```http
POST /api/events/1/images
Content-Type: application/json

{
    "image_url": "https://example.com/nueva-imagen.jpg",
    "alt_text": "Descripción de la imagen",
    "is_main": false
}
```

### 2. Actualizar imagen
```http
PUT /api/events/1/images/1
Content-Type: application/json

{
    "alt_text": "Nueva descripción de la imagen",
    "is_main": true
}
```

### 3. Establecer imagen principal
```http
PUT /api/events/1/images/2/set-main
```

### 4. Eliminar imagen
```http
DELETE /api/events/1/images/1
```

## 📝 Tipos de Eventos Disponibles

- `festival` - Festivales culturales, musicales, etc.
- `concert` - Conciertos y presentaciones musicales
- `exhibition` - Exposiciones de arte, fotografía, etc.
- `tour` - Tours guiados, recorridos turísticos
- `workshop` - Talleres, cursos, capacitaciones
- `other` - Otros tipos de eventos

## 🔍 Ejemplos de Consultas Avanzadas

### Eventos gratuitos próximos
```http
GET /api/events/upcoming
```
Luego filtrar en el cliente por `price: null`

### Eventos en un radio específico
```http
GET /api/events/nearby?latitude=-16.5000&longitude=-68.1500&radius=5
```

### Eventos de un tipo específico en un lugar
```http
GET /api/events/by-place/1
```
Luego filtrar por `event_type`

## ⚠️ Notas Importantes

1. **Fechas**: Usar formato `YYYY-MM-DD HH:MM:SS` para fechas
2. **Coordenadas**: Latitud y longitud son opcionales pero recomendadas
3. **Precios**: `null` para eventos gratuitos
4. **Imágenes**: Solo una imagen puede ser principal por evento
5. **Validaciones**: 
   - `place_id` debe existir en la tabla `places`
   - `start_date` no puede ser en el pasado
   - `end_date` no puede ser anterior a `start_date`

## 🚀 Casos de Uso Comunes

### App móvil de turismo
```javascript
// Obtener eventos cercanos al usuario
const nearbyEvents = await fetch('/api/events/nearby?latitude=-16.5000&longitude=-68.1500&radius=10');

// Obtener eventos de hoy
const todayEvents = await fetch('/api/events/today');

// Buscar eventos por nombre
const searchResults = await fetch('/api/events/search?name=festival');
```

### Panel de administración
```javascript
// Obtener estadísticas
const stats = await fetch('/api/events/stats');

// Obtener todos los eventos para gestión
const allEvents = await fetch('/api/events');

// Crear nuevo evento
const newEvent = await fetch('/api/events', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(eventData)
});
```

### Widget de eventos en sitio web
```javascript
// Mostrar eventos próximos
const upcomingEvents = await fetch('/api/events/upcoming');

// Mostrar eventos por tipo
const festivals = await fetch('/api/events/by-type/festival');
```
