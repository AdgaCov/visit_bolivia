# Featured Places API

## Endpoint: GET /api/locations/featured

Este endpoint devuelve los lugares más destacados basados en las reseñas y calificaciones de los usuarios.

### Parámetros de consulta (Query Parameters)

- `limit` (opcional): Número de lugares destacados a devolver. Por defecto es 3.

### Respuesta exitosa (200)

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Lugar Ejemplo",
            "description": "Descripción del lugar",
            "latitude": -16.5000,
            "longitude": -68.1500,
            "address": "Dirección del lugar",
            "type": "attraction",
            "average_rating": 4.5,
            "review_count": 10,
            "department": {
                "id": 1,
                "name": "La Paz"
            },
            "user": {
                "id": 1,
                "name": "Usuario"
            },
            "subcategories": [],
            "images": [],
            "reviews": []
        }
    ]
}
```

### Respuesta de error (500)

```json
{
    "success": false,
    "message": "Error al obtener lugares destacados: [mensaje de error]"
}
```

### Ejemplos de uso

1. **Obtener los 3 lugares más destacados (por defecto):**
   ```
   GET /api/locations/featured
   ```

2. **Obtener los 5 lugares más destacados:**
   ```
   GET /api/locations/featured?limit=5
   ```

### Criterios de selección

Los lugares destacados se seleccionan basándose en:

1. **Calificación promedio** (de mayor a menor)
2. **Número de reseñas** (de mayor a menor)
3. **Fecha de creación** (más recientes primero)

Solo se incluyen lugares que tengan al menos una reseña.

### Notas

- Este endpoint es público y no requiere autenticación
- Los lugares se ordenan por calificación promedio, número de reseñas y fecha de creación
- Solo se devuelven lugares que tengan al menos una reseña
- El límite máximo recomendado es 10 lugares para evitar respuestas muy grandes
