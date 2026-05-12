<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcategory extends Model
{
    protected $table = 'subcategories';
    
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'image_url'
    ];
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
    public function places(): BelongsToMany
    {
        return $this->belongsToMany(Place::class, 'place_subcategories', 'subcategory_id', 'place_id');
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_subcategories', 'subcategory_id', 'article_id');
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'location_subcategories', 'subcategory_id', 'location_id');
    }
}



// @CulturaPage.vue En culturaPage.vue , tengo un template si template_id = 6 que me cargue@ArticleSlider.vue tomando todas las imagenes en el slider , este es el json que trae {
//     "success": true,
//     "data": [
//         {
//             "id": 12,
//             "title": "Naturaleza de Bolivia",
//             "subtitle": null,
//             "description": null,
//             "content": "Bolivia posee una gran riqueza natural que abarca desde los Andes hasta la Amazonía. \r\n   En esta categoría podrás descubrir paisajes únicos, biodiversidad impresionante y actividades que conectan al viajero con la naturaleza. \r\n   Algunas de las subcategorías que encontrarás incluyen:\r\n   - Ecoturismo\r\n   - Trekking y montañismo\r\n   - Áreas protegidas y parques nacionales\r\n   - Ríos, lagos y lagunas\r\n   - Fauna y flora nativa\r\n   Explorar la naturaleza boliviana es una experiencia que muestra la esencia del país y su enorme valor ecológico.",
//             "type": "custom",
//             "author": "Equipo Conoce Bolivia",
//             "media_url": null,
//             "template_id": 1,
//             "link": "/naturaleza",
//             "created_at": "2025-09-29T10:29:14.000000Z",
//             "updated_at": "2025-09-29T10:30:19.000000Z",
//             "images": [],
//             "subcategories": [
//                 {
//                     "id": 7,
//                     "category_id": 2,
//                     "name": "Lagos y Ríos",
//                     "description": "Lagos, ríos y cuerpos de agua importantes para turismo y naturaleza.",
//                     "image_url": "https://www.ibolivia.org/wp-content/uploads/2019/08/lago-bay-pando.jpg",
//                     "pivot": {
//                         "article_id": 12,
//                         "subcategory_id": 7
//                     }
//                 }
//             ]
//         },
//         {
//             "id": 6,
//             "title": "Trekking en la Cordillera Real",
//             "subtitle": "Aventura en las montañas de los Andes",
//             "description": "Una experiencia única recorriendo los nevados de la Cordillera Real.",
//             "content": "La Cordillera Real ofrece rutas de trekking espectaculares que atraviesan lagunas, glaciares y pueblos andinos. Es ideal para los amantes de la montaña y quienes buscan desafíos en altura.",
//             "type": "place",
//             "author": "Equipo Turismo Bolivia",
//             "media_url": "https://terandes.com/wp-content/uploads/2022/04/bolivia-mountains.jpg",
//             "template_id": 6,
//             "link": null,
//             "created_at": "2025-09-24T12:55:16.000000Z",
//             "updated_at": "2025-09-29T12:48:42.000000Z",
//             "images": [
//                 {
//                     "id": 13,
//                     "article_id": 6,
//                     "image_url": "http://localhost/media/cordillera_real_1.jpg",
//                     "alt_text": "Trekking en la Cordillera Real",
//                     "is_main": 1,
//                     "uploaded_at": "2025-09-24 14:55:56"
//                 },
//                 {
//                     "id": 14,
//                     "article_id": 6,
//                     "image_url": "http://localhost/media/cordillera_real_2.jpg",
//                     "alt_text": "Laguna glacial en la Cordillera Real",
//                     "is_main": 0,
//                     "uploaded_at": "2025-09-24 14:55:56"
//                 },
//                 {
//                     "id": 15,
//                     "article_id": 6,
//                     "image_url": "http://localhost/media/cordillera_real_3.jpg",
//                     "alt_text": "Vista panorámica de nevados",
//                     "is_main": 0,
//                     "uploaded_at": "2025-09-24 14:55:56"
//                 }
//             ],
//             "subcategories": [
//                 {
//                     "id": 8,
//                     "category_id": 2,
//                     "name": "Fauna Silvestre",
//                     "description": "Animales autóctonos y vida salvaje de Bolivia en sus distintos ecosistemas.",
//                     "image_url": "https://armoniabolivia.org/wp-content/uploads/2020/04/heron-caiman-barba-azul-nr-bennett-hennessey_45055454931_o-1-1024x667.jpg",
//                     "pivot": {
//                         "article_id": 6,
//                         "subcategory_id": 8
//                     }
//                 }
//             ]
//         },
//         {
//             "id": 5,
//             "title": "Ecoturismo en el Parque Madidi",
//             "subtitle": "La biodiversidad más grande del planeta",
//             "description": "Explora la Amazonía boliviana en uno de los parques con mayor biodiversidad del mundo.",
//             "content": "El Parque Nacional Madidi es considerado uno de los lugares con mayor biodiversidad del planeta. Con más de 6.000 especies de plantas y una gran variedad de animales, es un destino único para el ecoturismo y la conservación.",
//             "type": "place",
//             "author": "Equipo Turismo Bolivia",
//             "media_url": "http://localhost/api_conoce_bolivia/public/uploads/categories/cultura/sub_cultura/27.jpg",
//             "template_id": 5,
//             "link": null,
//             "created_at": "2025-09-24T12:54:21.000000Z",
//             "updated_at": "2025-09-25T06:05:57.000000Z",
//             "images": [
//                 {
//                     "id": 10,
//                     "article_id": 5,
//                     "image_url": "http://localhost/media/madidi_1.jpg",
//                     "alt_text": "Vista aérea del Parque Madidi",
//                     "is_main": 1,
//                     "uploaded_at": "2025-09-24 14:54:45"
//                 },
//                 {
//                     "id": 11,
//                     "article_id": 5,
//                     "image_url": "http://localhost/media/madidi_2.jpg",
//                     "alt_text": "Río en el Parque Madidi",
//                     "is_main": 0,
//                     "uploaded_at": "2025-09-24 14:54:45"
//                 },
//                 {
//                     "id": 12,
//                     "article_id": 5,
//                     "image_url": "http://localhost/media/madidi_3.jpg",
//                     "alt_text": "Fauna del Parque Madidi",
//                     "is_main": 0,
//                     "uploaded_at": "2025-09-24 14:54:45"
//                 }
//             ],
//             "subcategories": [
//                 {
//                     "id": 7,
//                     "category_id": 2,
//                     "name": "Lagos y Ríos",
//                     "description": "Lagos, ríos y cuerpos de agua importantes para turismo y naturaleza.",
//                     "image_url": "https://www.ibolivia.org/wp-content/uploads/2019/08/lago-bay-pando.jpg",
//                     "pivot": {
//                         "article_id": 5,
//                         "subcategory_id": 7
//                     }
//                 }
//             ]
//         },
//         {
//             "id": 2,
//             "title": "Parques Nacionales",
//             "subtitle": "Naturaleza en su máxima expresión",
//             "description": "Guía de los parques más importantes de Bolivia.",
//             "content": "Bolivia es un país privilegiado por su riqueza natural y cultural. Sus Parques Nacionales representan verdaderos santuarios de biodiversidad, con ecosistemas que van desde la selva amazónica hasta los altiplanos andinos. Estos espacios protegidos no solo preservan la flora y fauna, sino que también resguardan comunidades originarias y tradiciones ancestrales.",
//             "type": "place",
//             "author": "Juan Nigañez",
//             "media_url": "http://localhost/api_conoce_bolivia/public/uploads/categories/cultura/sub_cultura/26.jpg",
//             "template_id": 3,
//             "link": null,
//             "created_at": "2025-09-24T08:31:57.000000Z",
//             "updated_at": "2025-09-29T10:17:23.000000Z",
//             "images": [
//                 {
//                     "id": 3,
//                     "article_id": 2,
//                     "image_url": "http://localhost/media/parques_1.jpg",
//                     "alt_text": "Parque Nacional Madidi",
//                     "is_main": 1,
//                     "uploaded_at": "2025-09-24 10:31:57"
//                 },
//                 {
//                     "id": 4,
//                     "article_id": 2,
//                     "image_url": "http://localhost/media/parques_2.jpg",
//                     "alt_text": "Fauna silvestre",
//                     "is_main": 0,
//                     "uploaded_at": "2025-09-24 10:31:57"
//                 }
//             ],
//             "subcategories": [
//                 {
//                     "id": 6,
//                     "category_id": 2,
//                     "name": "Parques Nacionales",
//                     "description": "Parques nacionales con biodiversidad y paisajes únicos de Bolivia.",
//                     "image_url": "https://www.laregion.bo/wp-content/uploads/2015/10/TIPNIS-1024x726.jpg",
//                     "pivot": {
//                         "article_id": 2,
//                         "subcategory_id": 6
//                     }
//                 },
//                 {
//                     "id": 9,
//                     "category_id": 2,
//                     "name": "Flora y Bosques",
//                     "description": "Vegetación, flora nativa y bosques representativos de Bolivia.",
//                     "image_url": "https://www.naturabolivia.org/wp-content/uploads/2021/06/DJI_0385-2.jpg",
//                     "pivot": {
//                         "article_id": 2,
//                         "subcategory_id": 9
//                     }
//                 }
//             ]
//         }
//     ]
// }   y puedes ver que el articulo con id = 6 tiene imagenes images esas imagenes debe cargar en el slider, entiendes????