<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\DepartmentController;
use App\Controllers\LocationController;
use App\Controllers\LocationItemController;
use App\Controllers\CategoryController;
use App\Controllers\SubcategoryController;
use App\Controllers\RouteController;
use App\Controllers\ArticleController;
use App\Controllers\LocationFavoriteController;
use App\Controllers\LocationReviewController;
use App\Controllers\LocationSubcategoryController;
use App\Controllers\LocationPolicyController;
use App\Controllers\PlanController;
use App\Middleware\JwtMiddleware;

$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'success' => true,
        'message' => 'API Conoce Bolivia esta funcionando correctamente',
        'endpoints' => [
            'health' => '/api/health',
            'locations' => '/api/locations',
            'categories' => '/api/categories',
            'articles' => '/api/articles'
        ]
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/api', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'success' => true,
        'message' => 'API Conoce Bolivia',
        'health' => '/api/health'
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Auth routes (public)
$app->group('/api/auth', function (RouteCollectorProxy $group) {
    $group->post('/register', [AuthController::class, 'register']);
    $group->post('/login', [AuthController::class, 'login']);
    $group->post('/google', [AuthController::class, 'googleAuth']);
    $group->get('/me', [AuthController::class, 'me']);
});

// Users (protected)
$app->group('/api/users', function (RouteCollectorProxy $group) {
    $group->get('', [UserController::class, 'index']);
    $group->get('/{id}', [UserController::class, 'show']);
    $group->post('', [UserController::class, 'store']);
    $group->put('/{id}', [UserController::class, 'update']);
    $group->delete('/{id}', [UserController::class, 'destroy']);
    $group->put('/{id}/toggle-status', [UserController::class, 'toggleStatus']);
})->add(JwtMiddleware::class);

// Plans (public listing, protected CRUD)
$app->group('/api/plans', function (RouteCollectorProxy $group) {
    $group->get('', [PlanController::class, 'index']);
    $group->get('/active', [PlanController::class, 'getActive']);
    $group->get('/{id}', [PlanController::class, 'show']);
    
    $group->group('', function (RouteCollectorProxy $protected) {
        $protected->post('', [PlanController::class, 'store']);
        $protected->put('/{id}', [PlanController::class, 'update']);
        $protected->delete('/{id}', [PlanController::class, 'destroy']);
        $protected->put('/{id}/toggle-active', [PlanController::class, 'toggleActive']);
    })->add(JwtMiddleware::class);
});

// Departments (public)
$app->group('/api/departments', function (RouteCollectorProxy $group) {
    $group->get('', [DepartmentController::class, 'index']);
    $group->get('/with-cities', [DepartmentController::class, 'getWithCities']);
    $group->get('/with-places', [DepartmentController::class, 'getWithPlaces']);
    $group->get('/{id}', [DepartmentController::class, 'show']);
    $group->group('', function (RouteCollectorProxy $protected) {
        $protected->post('', [DepartmentController::class, 'store']);
        $protected->put('/{id}', [DepartmentController::class, 'update']);
        $protected->delete('/{id}', [DepartmentController::class, 'destroy']);
    })->add(JwtMiddleware::class);
});

// Locations (unified - public)
$app->group('/api/locations', function (RouteCollectorProxy $group) {
    // General location endpoints
    $group->get('', [LocationController::class, 'index']);
    $group->get('/with-subcategories', [LocationController::class, 'getWithAnySubcategory']);
    $group->get('/with-reviews', [LocationController::class, 'getWithReviews']);
    $group->get('/nearby', [LocationController::class, 'getNearby']);
    $group->get('/search', [LocationController::class, 'search']);
    $group->get('/by-type/{type}', [LocationController::class, 'getByType']);
    //filtrar por tipo las locaciones
    $group->get('/by-type-with-reviews/{type}', [LocationController::class, 'getByTypeWithReviews']);
    $group->get('/by-department/{department_id}', [LocationController::class, 'getByDepartment']);
    $group->get('/by-user/{user_id}', [LocationController::class, 'getByUser']);
    $group->get('/by-subcategory/{subcategory_id}', [LocationController::class, 'getBySubcategory']);
    $group->get('/{id}/articles', [LocationController::class, 'getArticlesByLocation']);
    $group->get('/stats', [LocationController::class, 'getStats']);
    $group->get('/popular/favorites', [LocationController::class, 'getPopularFavorites']);
    $group->get('/featured', [LocationController::class, 'getFeaturedPlaces']);
    $group->get('/{id}', [LocationController::class, 'show']);
    
    // Event-specific endpoints
    $group->get('/events/upcoming', [LocationController::class, 'getUpcomingEvents']);
    $group->get('/events/recurring', [LocationController::class, 'getRecurringEvents']);
    $group->get('/events/by-date-range', [LocationController::class, 'getEventsByDateRange']);
    $group->get('/events/today', [LocationController::class, 'getTodayEvents']);
    $group->get('/events/this-week', [LocationController::class, 'getThisWeekEvents']);
    $group->get('/events/this-month', [LocationController::class, 'getThisMonthEvents']);
    
    // Restaurant-specific endpoints
    $group->get('/restaurants/by-badge/{badge}', [LocationController::class, 'getRestaurantsByBadge']);
    
    // Accommodation-specific endpoints
    $group->get('/accommodations/by-room-type/{room_type}', [LocationController::class, 'getAccommodationsByRoomType']);
    $group->get('/accommodations/by-price-range', [LocationController::class, 'getAccommodationsByPriceRange']);
    $group->get('/accommodations/by-capacity', [LocationController::class, 'getAccommodationsByCapacity']);
    
    // Protected routes
    $group->group('', function (RouteCollectorProxy $protected) {
        $protected->post('', [LocationController::class, 'store']);
        $protected->put('/{id}', [LocationController::class, 'update']);
        $protected->post('/{id}', [LocationController::class, 'update']);
        $protected->delete('/{id}', [LocationController::class, 'destroy']);
        
        // Image management routes
        $protected->post('/{id}/images', [LocationController::class, 'addImage']);
        $protected->post('/{id}/images/{image_id}', [LocationController::class, 'updateImage']);
        $protected->delete('/{id}/images/{image_id}', [LocationController::class, 'deleteImage']);
        $protected->put('/{id}/images/{image_id}/set-main', [LocationController::class, 'setMainImage']);
    })->add(JwtMiddleware::class);
});

// Legacy endpoints for backward compatibility
$app->group('/api/cities', function (RouteCollectorProxy $group) {
    $group->get('', [LocationController::class, 'index'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'city']));
        return $handler->handle($request);
    });
    $group->get('/nearby', [LocationController::class, 'getNearby'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'city']));
        return $handler->handle($request);
    });
    $group->get('/search', [LocationController::class, 'search'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'city']));
        return $handler->handle($request);
    });
    $group->get('/by-department/{department_id}', [LocationController::class, 'getByDepartment'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'city']));
        return $handler->handle($request);
    });
    $group->get('/{id}', [LocationController::class, 'show']);
    $group->group('', function (RouteCollectorProxy $protected) {
        $protected->post('', [LocationController::class, 'store'])->add(function($request, $handler) {
            $body = $request->getParsedBody();
            $body['type'] = 'city';
            $request = $request->withParsedBody($body);
            return $handler->handle($request);
        });
        $protected->put('/{id}', [LocationController::class, 'update']);
        $protected->delete('/{id}', [LocationController::class, 'destroy']);
    })->add(JwtMiddleware::class);
});

$app->group('/api/places', function (RouteCollectorProxy $group) {
    $group->get('', [LocationController::class, 'index'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'attraction']));
        return $handler->handle($request);
    });
    $group->get('/nearby', [LocationController::class, 'getNearby'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'attraction']));
        return $handler->handle($request);
    });
    $group->get('/search', [LocationController::class, 'search'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'attraction']));
        return $handler->handle($request);
    });
    $group->get('/by-user/{user_id}', [LocationController::class, 'getByUser'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'attraction']));
        return $handler->handle($request);
    });
    $group->get('/by-subcategory/{subcategory_id}', [LocationController::class, 'getBySubcategory'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'attraction']));
        return $handler->handle($request);
    });
    $group->get('/{id}', [LocationController::class, 'show']);
    $group->group('', function (RouteCollectorProxy $protected) {
        $protected->post('', [LocationController::class, 'store'])->add(function($request, $handler) {
            $body = $request->getParsedBody();
            $body['type'] = 'attraction';
            $request = $request->withParsedBody($body);
            return $handler->handle($request);
        });
        $protected->put('/{id}', [LocationController::class, 'update']);
        $protected->delete('/{id}', [LocationController::class, 'destroy']);
    })->add(JwtMiddleware::class);
});

// Categories (public)
$app->group('/api/categories', function (RouteCollectorProxy $group) {
    $group->get('', [CategoryController::class, 'index']);
    $group->get('/with-locations', [CategoryController::class, 'getWithLocations']);
    $group->get('/with-articles', [CategoryController::class, 'getWithArticles']);
    $group->get('/{id}/articles', [CategoryController::class, 'getArticlesByCategory']);
    $group->get('/{id}/locations', [CategoryController::class, 'getLocationsByCategory']);
    $group->get('/{id}', [CategoryController::class, 'show']);
    $group->group('', function (RouteCollectorProxy $protected) {
        $protected->post('', [CategoryController::class, 'store']);
        $protected->post('/{id}/update', [CategoryController::class, 'update']); // para form-data
        $protected->put('/{id}', [CategoryController::class, 'update']); // JSON puro
        $protected->delete('/{id}', [CategoryController::class, 'destroy']);
    })->add(JwtMiddleware::class);
});

// Articles by page section (public)
$app->group('/api/articles', function (RouteCollectorProxy $group) {
    $group->get('', [ArticleController::class, 'index']);
    //listado de articulos por articulos
    $group->get('/by-parent/{parent_id}', [ArticleController::class, 'getByParentId']);
    $group->get('/by-page-section/{page_section}', [CategoryController::class, 'getArticlesByPageSection']);
    $group->get('/by-category/{category_id}', [CategoryController::class, 'getArticlesByCategoryId']);
    $group->group('', function (RouteCollectorProxy $protected) {
        $protected->post('', [ArticleController::class, 'store']);
        $protected->post('/{id}/update', [ArticleController::class, 'update']); // POST para form-data
        $protected->put('/{id}', [ArticleController::class, 'update']); // PUT para JSON (compatibilidad)
        $protected->delete('/{id}', [ArticleController::class, 'destroy']);
    })->add(JwtMiddleware::class);
});

// Subcategories (public)
$app->group('/api/subcategories', function (RouteCollectorProxy $group) {
    $group->get('', [SubcategoryController::class, 'index']);
    $group->get('/{id}', [SubcategoryController::class, 'show']);
    $group->get('/by-category/{category_id}', [SubcategoryController::class, 'getByCategory']);
    $group->get('/{id}/articles', [SubcategoryController::class, 'getArticlesBySubcategory']);
    $group->get('/{id}/locations', [SubcategoryController::class, 'getLocationsBySubcategory']);
    $group->group('', function (RouteCollectorProxy $protected) {
        $protected->post('', [SubcategoryController::class, 'store']);
        $protected->post('/{id}/update', [SubcategoryController::class, 'update']); // para form-data
        $protected->post('/{id}', [SubcategoryController::class, 'update']); // JSON puro
        $protected->delete('/{id}', [SubcategoryController::class, 'destroy']);
    })->add(JwtMiddleware::class);
});

// Location-Subcategory associations (public GET, protected POST/PUT/DELETE)
$app->group('/api/location-subcategories', function (RouteCollectorProxy $group) {
    $group->get('', [LocationSubcategoryController::class, 'index']);
    $group->get('/by-location/{location_id}', [LocationSubcategoryController::class, 'getSubcategoriesByLocation']);
    $group->get('/by-subcategory/{subcategory_id}', [LocationSubcategoryController::class, 'getLocationsBySubcategory']);
    $group->group('', function (RouteCollectorProxy $protected) {
        $protected->post('', [LocationSubcategoryController::class, 'store']);
        $protected->put('/{location_id}', [LocationSubcategoryController::class, 'update']);
        $protected->delete('/{location_id}/{subcategory_id}', [LocationSubcategoryController::class, 'destroy']);
    })->add(JwtMiddleware::class);
});

// Routes (public)
$app->group('/api/routes', function (RouteCollectorProxy $group) {
    $group->get('', [RouteController::class, 'index']);
    $group->get('/between', [RouteController::class, 'getRoutesBetween']);
    $group->get('/by-origin-location/{location_id}', [RouteController::class, 'getByOriginLocation']);
    $group->get('/by-destination-location/{location_id}', [RouteController::class, 'getByDestinationLocation']);
    $group->get('/by-origin-city/{city_id}', [RouteController::class, 'getByOriginCity']);
    $group->get('/by-destination-place/{place_id}', [RouteController::class, 'getByDestinationPlace']);
    $group->get('/{id}', [RouteController::class, 'show']);
    $group->group('', function (RouteCollectorProxy $protected) {
        $protected->post('', [RouteController::class, 'store']);
        $protected->put('/{id}', [RouteController::class, 'update']);
        $protected->delete('/{id}', [RouteController::class, 'destroy']);
    })->add(JwtMiddleware::class);
});

// Legacy endpoints for backward compatibility
$app->group('/api/events', function (RouteCollectorProxy $group) {
    $group->get('', [LocationController::class, 'index'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'event']));
        return $handler->handle($request);
    });
    $group->get('/upcoming', [LocationController::class, 'getUpcomingEvents']);
    $group->get('/recurring', [LocationController::class, 'getRecurringEvents']);
    $group->get('/nearby', [LocationController::class, 'getNearby'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'event']));
        return $handler->handle($request);
    });
    $group->get('/search', [LocationController::class, 'search'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'event']));
        return $handler->handle($request);
    });
    $group->get('/by-date-range', [LocationController::class, 'getEventsByDateRange']);
    $group->get('/today', [LocationController::class, 'getTodayEvents']);
    $group->get('/this-week', [LocationController::class, 'getThisWeekEvents']);
    $group->get('/this-month', [LocationController::class, 'getThisMonthEvents']);
    $group->get('/stats', [LocationController::class, 'getStats'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'event']));
        return $handler->handle($request);
    });
    $group->get('/by-type/{type}', [LocationController::class, 'getByType'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'event']));
        return $handler->handle($request);
    });
    $group->get('/{id}', [LocationController::class, 'show']);
    $group->group('', function (RouteCollectorProxy $protected) {
        $protected->post('', [LocationController::class, 'store'])->add(function($request, $handler) {
            $body = $request->getParsedBody();
            $body['type'] = 'event';
            $request = $request->withParsedBody($body);
            return $handler->handle($request);
        });
        $protected->put('/{id}', [LocationController::class, 'update']);
        $protected->delete('/{id}', [LocationController::class, 'destroy']);
    })->add(JwtMiddleware::class);
});

$app->group('/api/restaurants', function (RouteCollectorProxy $group) {
    $group->get('', [LocationController::class, 'index'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'restaurant']));
        return $handler->handle($request);
    });
    $group->get('/nearby', [LocationController::class, 'getNearby'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'restaurant']));
        return $handler->handle($request);
    });
    $group->get('/search', [LocationController::class, 'search'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'restaurant']));
        return $handler->handle($request);
    });
    $group->get('/stats', [LocationController::class, 'getStats'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'restaurant']));
        return $handler->handle($request);
    });
    $group->get('/by-badge/{badge}', [LocationController::class, 'getRestaurantsByBadge']);
    $group->get('/{id}', [LocationController::class, 'show']);
    $group->group('', function (RouteCollectorProxy $protected) {
        $protected->post('', [LocationController::class, 'store'])->add(function($request, $handler) {
            $body = $request->getParsedBody();
            $body['type'] = 'restaurant';
            $request = $request->withParsedBody($body);
            return $handler->handle($request);
        });
        $protected->put('/{id}', [LocationController::class, 'update']);
        $protected->delete('/{id}', [LocationController::class, 'destroy']);
    })->add(JwtMiddleware::class);
});

$app->group('/api/gastronomy', function (RouteCollectorProxy $group) {
    $group->get('', [LocationController::class, 'index'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'restaurant']));
        return $handler->handle($request);
    });
    $group->get('/{id}', [LocationController::class, 'show']);
});

$app->group('/api/accommodations', function (RouteCollectorProxy $group) {
    $group->get('', [LocationController::class, 'index'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'accommodation']));
        return $handler->handle($request);
    });
    $group->get('/nearby', [LocationController::class, 'getNearby'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'accommodation']));
        return $handler->handle($request);
    });
    $group->get('/search', [LocationController::class, 'search'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'accommodation']));
        return $handler->handle($request);
    });
    $group->get('/stats', [LocationController::class, 'getStats'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'accommodation']));
        return $handler->handle($request);
    });
    $group->get('/by-room-type/{room_type}', [LocationController::class, 'getAccommodationsByRoomType']);
    $group->get('/by-badge/{badge}', [LocationController::class, 'getByType'])->add(function($request, $handler) {
        $request = $request->withQueryParams(array_merge($request->getQueryParams(), ['type' => 'accommodation']));
        return $handler->handle($request);
    });
    $group->get('/by-price-range', [LocationController::class, 'getAccommodationsByPriceRange']);
    $group->get('/by-capacity', [LocationController::class, 'getAccommodationsByCapacity']);
    $group->get('/{id}', [LocationController::class, 'show']);
    $group->group('', function (RouteCollectorProxy $protected) {
        $protected->post('', [LocationController::class, 'store'])->add(function($request, $handler) {
            $body = $request->getParsedBody();
            $body['type'] = 'accommodation';
            $request = $request->withParsedBody($body);
            return $handler->handle($request);
        });
        $protected->put('/{id}', [LocationController::class, 'update']);
        $protected->delete('/{id}', [LocationController::class, 'destroy']);
    })->add(JwtMiddleware::class);
});

// ENDPOINT PARA IMÁGENES DE LOCACIONES (global)
$app->get('/api/location-images', [\App\Controllers\LocationImageController::class, 'index']);
$app->get('/api/location-images/by-user/{user_id}', [\App\Controllers\LocationImageController::class, 'getByUserId']);
$app->group('/api/location-images', function (RouteCollectorProxy $group) {
    $group->post('', [\App\Controllers\LocationImageController::class, 'store']);
    $group->post('/{id}', [\App\Controllers\LocationImageController::class, 'update']);
    $group->put('/{id}/set-main', [\App\Controllers\LocationImageController::class, 'setMain']);
    $group->delete('/{id}', [\App\Controllers\LocationImageController::class, 'destroy']);
})->add(JwtMiddleware::class);

// Location Items (public endpoints)
$app->group('/api/location-items', function (RouteCollectorProxy $group) {
    $group->get('', [LocationItemController::class, 'index']);
    $group->get('/by-location/{location_id}', [LocationItemController::class, 'getByLocation']);
    $group->get('/by-type/{type}', [LocationItemController::class, 'getByType']);
    $group->get('/by-user/{user_id}', [LocationItemController::class, 'getByUserId']);
    $group->get('/{id}', [LocationItemController::class, 'show']);
    
    // Protected routes
    $group->group('', function (RouteCollectorProxy $protected) {
        $protected->post('', [LocationItemController::class, 'store']);
        $protected->post('/{id}', [LocationItemController::class, 'update']);
        $protected->delete('/{id}', [LocationItemController::class, 'destroy']);
    })->add(JwtMiddleware::class);
});

// Favorites (require auth)
// $app->group('/api/favorites', function (RouteCollectorProxy $group) {
//     $group->get('', [LocationFavoriteController::class, 'index']); // Mis favoritos
//     $group->get('/all', [LocationFavoriteController::class, 'getAll']); // Todos los favoritos
//     $group->post('', [LocationFavoriteController::class, 'store']);
//     $group->post('/toggle', [LocationFavoriteController::class, 'toggle']);
//     $group->delete('/{location_id}', [LocationFavoriteController::class, 'destroy']);
// })->add(JwtMiddleware::class);


// 🔓 Grupo público
$app->group('/api/favorites', function (RouteCollectorProxy $group) {
    $group->get('/all', [LocationFavoriteController::class, 'getAll']);
});

// 🔒 Grupo privado (con token)
$app->group('/api/favorites', function (RouteCollectorProxy $group) {
    $group->get('', [LocationFavoriteController::class, 'index']);
    $group->post('', [LocationFavoriteController::class, 'store']);
    $group->post('/toggle', [LocationFavoriteController::class, 'toggle']);
    $group->delete('/{location_id}', [LocationFavoriteController::class, 'destroy']);
})->add(JwtMiddleware::class);

// Location Reviews
$app->group('/api/location-reviews', function (RouteCollectorProxy $group) {
    $group->get('', [LocationReviewController::class, 'index']);
    $group->get('/by-location/{location_id}', [LocationReviewController::class, 'getByLocation']);

    $group->group('', function (RouteCollectorProxy $protected) {
        $protected->post('', [LocationReviewController::class, 'store']);
        $protected->put('/{id}', [LocationReviewController::class, 'update']);
        $protected->delete('/{id}', [LocationReviewController::class, 'destroy']);
    })->add(JwtMiddleware::class);
});

// Location Policies (public GET, protected CRUD)
$app->group('/api/location-policies', function (RouteCollectorProxy $group) {
    $group->get('', [LocationPolicyController::class, 'index']);
    $group->get('/by-location/{location_id}', [LocationPolicyController::class, 'getByLocation']);
    $group->get('/by-user/{user_id}', [LocationPolicyController::class, 'getByUserId']);
    $group->get('/{id}', [LocationPolicyController::class, 'show']);
    
    $group->group('', function (RouteCollectorProxy $protected) {
        $protected->post('', [LocationPolicyController::class, 'store']);
        $protected->put('/{id}', [LocationPolicyController::class, 'update']);
        $protected->post('/by-location/{location_id}/update-or-create', [LocationPolicyController::class, 'updateOrCreate']);
        $protected->delete('/{id}', [LocationPolicyController::class, 'destroy']);
    })->add(JwtMiddleware::class);
});

// Health check endpoint
$app->get('/api/health', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'success' => true,
        'message' => 'API Conoce Bolivia está funcionando correctamente',
        'timestamp' => date('Y-m-d H:i:s')
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});
