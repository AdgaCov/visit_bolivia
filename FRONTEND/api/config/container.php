<?php

use App\Services\DatabaseService;
use App\Services\AuthService;
use App\Services\UserService;
use App\Services\DepartmentService;
use App\Services\CityService;
use App\Services\PlaceService;
use App\Services\CategoryService;
use App\Services\SubcategoryService;
use App\Services\RouteService;
use App\Services\EventService;
use App\Services\ImageService;
use App\Services\UploadService;
use App\Services\RestaurantService;
use App\Services\AccommodationService;
use App\Services\LocationService;
use App\Services\LocationReviewService;
use App\Services\LocationFavoriteService;
use App\Services\LocationItemService;
use App\Services\LocationSubcategoryService;
use App\Services\LocationPolicyService;
use App\Services\PlanService;
use App\Controllers\ArticleController;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\DepartmentController;
use App\Controllers\CityController;
use App\Controllers\PlaceController;
use App\Controllers\CategoryController;
use App\Controllers\SubcategoryController;
use App\Controllers\RouteController;
use App\Controllers\EventController;
use App\Controllers\RestaurantController;
use App\Controllers\AccommodationController;
use App\Controllers\LocationController;
use App\Controllers\LocationFavoriteController;
use App\Controllers\LocationReviewController;
use App\Controllers\LocationItemController;
use App\Controllers\LocationSubcategoryController;
use App\Controllers\LocationPolicyController;
use App\Controllers\PlanController;
use App\Middleware\JwtMiddleware;

return [
    // Database
    DatabaseService::class => DI\create(DatabaseService::class),
    
    // Services
    AuthService::class => DI\autowire(AuthService::class),
    UserService::class => DI\autowire(UserService::class),
    DepartmentService::class => DI\autowire(DepartmentService::class),
    CityService::class => DI\autowire(CityService::class),
    PlaceService::class => DI\autowire(PlaceService::class),
    CategoryService::class => DI\autowire(CategoryService::class),
    SubcategoryService::class => DI\autowire(SubcategoryService::class),
    RouteService::class => DI\autowire(RouteService::class),
    EventService::class => DI\autowire(EventService::class),
    ImageService::class => DI\autowire(ImageService::class),
    UploadService::class => DI\autowire(UploadService::class),
    RestaurantService::class => DI\autowire(RestaurantService::class),
    AccommodationService::class => DI\autowire(AccommodationService::class),
    LocationService::class => DI\autowire(LocationService::class),
    LocationReviewService::class => DI\autowire(LocationReviewService::class),
    LocationFavoriteService::class => DI\autowire(LocationFavoriteService::class),
    LocationItemService::class => DI\autowire(LocationItemService::class),
    LocationSubcategoryService::class => DI\autowire(LocationSubcategoryService::class),
    LocationPolicyService::class => DI\autowire(LocationPolicyService::class),
    PlanService::class => DI\autowire(PlanService::class),
    
    // Controllers
    AuthController::class => DI\autowire(AuthController::class),
    UserController::class => DI\autowire(UserController::class),
    DepartmentController::class => DI\autowire(DepartmentController::class),
    CityController::class => DI\autowire(CityController::class),
    PlaceController::class => DI\autowire(PlaceController::class),
    CategoryController::class => DI\autowire(CategoryController::class),
    SubcategoryController::class => DI\autowire(SubcategoryController::class),
    RouteController::class => DI\autowire(RouteController::class),
    EventController::class => DI\autowire(EventController::class),
    RestaurantController::class => DI\autowire(RestaurantController::class),
    AccommodationController::class => DI\autowire(AccommodationController::class),
    LocationController::class => DI\autowire(LocationController::class),
    LocationFavoriteController::class => DI\autowire(LocationFavoriteController::class),
    LocationReviewController::class => DI\autowire(LocationReviewController::class),
    LocationItemController::class => DI\autowire(LocationItemController::class),
    LocationSubcategoryController::class => DI\autowire(LocationSubcategoryController::class),
    LocationPolicyController::class => DI\autowire(LocationPolicyController::class),
    PlanController::class => DI\autowire(PlanController::class),
    ArticleController::class => DI\autowire(ArticleController::class),
    
    // Middleware
    JwtMiddleware::class => DI\autowire(JwtMiddleware::class),
];