import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/store/auth'


const HomePage = () => import('@/features/public/Home/HomePage.vue')
const PlacesList = () => import('@/features/places/List/PlacesList.vue')
const DetalleLugar = () => import('@/features/places/Detail/DetalleLugar.vue')
const RegionsPage = () => import('@/features/regions/RegionsPage.vue')
const ComoLlegar = () => import('@/features/travel/ComoLlegar.vue')
const AdminLogin = () => import('@/features/admin/Login/AdminLogin.vue')
const AdminDashboard = () => import('@/features/admin/Dashboard/AdminDashboard.vue')
const AdminPlaces = () => import('@/features/admin/Places/AdminPlaces.vue')
const AboutPage = () => import('@/features/public/About/AboutPage.vue')
const InteractiveMapPage = () => import('@/features/places/Map/InteractiveMapPage.vue')
const SearchPage = () => import('@/features/places/Search/SearchPage.vue')
const StatsPage = () => import('@/features/public/Stats/StatsPage.vue')
const FavoritesPage = () => import('@/features/public/Favorites/FavoritesPage.vue')
const ColorPalette = () => import('@/components/ColorPalette.vue')
const WhatToDoPage = () => import('@/features/public/WhatToDo/WhatToDoPage.vue')
const WhereToGoPage = () => import('@/features/public/WhereToGo/WhereToGoPage.vue')
const PlanningPage = () => import('@/features/public/Planning/PlanningPage.vue')
const CulturaPage = () => import('@/features/categories/Cultura/CulturaPage.vue')
const EventsPage = () => import('@/features/public/Events/EventsPage.vue')
const GastronomyPage = () => import('@/features/public/Gastronomy/GastronomyPage.vue')
const AccommodationPage = () => import('@/features/public/Accommodation/AccommodationPage.vue')

const routes = [
  { path: '/', name: 'Home', component: HomePage },
  { path: '/lugares', name: 'Lugares', component: PlacesList },
  { path: '/lugar/:id', name: 'DetalleLugar', component: DetalleLugar, props: true },
  { path: '/regiones', name: 'Regiones', component: RegionsPage },
  { path: '/como-llegar', name: 'ComoLlegar', component: ComoLlegar },
  { path: '/acerca-de', name: 'About', component: AboutPage },
  { path: '/mapa', name: 'InteractiveMap', component: InteractiveMapPage },
  { path: '/buscar', name: 'Search', component: SearchPage },
  { path: '/estadisticas', name: 'Stats', component: StatsPage },
  { path: '/eventos', name: 'Events', component: EventsPage },
  { path: '/evento/:id', name: 'EventDetail', component: EventsPage, props: true },
  { path: '/gastronomia', name: 'Gastronomy', component: GastronomyPage },
  // { path: '/restaurante/:id', name: 'GastronomyDetail', component: GastronomyPage, props: true },
  { path: '/location/:id', name: 'GastronomyDetail', component: GastronomyPage, props: true },
  { path: '/hotel/:id', name: 'AccommodationDetail', component: AccommodationPage, props: true },
   { path: '/favoritos', name: 'Favorites', component: FavoritesPage },
  { path: '/museo/:id', name: 'MuseumDetail', component: () => import('@/features/public/Museum/MuseumDetail.vue'), props: true },
  { path: '/ciudad/:id', name: 'CityDetail', component: () => import('@/features/public/City/CityDetail.vue'), props: true },
  { path: '/atractivo/:id', name: 'AttractionDetail', component: () => import('@/features/public/Attraction/AttractionDetail.vue'), props: true },
  { path: '/colores', name: 'ColorPalette', component: ColorPalette },
  { path: '/que-hacer', name: 'WhatToDo', component: WhatToDoPage },
  { path: '/donde-ir', name: 'WhereToGo', component: WhereToGoPage },
  { path: '/planificacion', name: 'Planning', component: PlanningPage},
  
  // Categorías dinámicas: todas usan CulturaPage con contenido dinámico.
  { path: '/que-hacer/:slug', name: 'Cultura', component: CulturaPage, props: true },
  { path: '/que-hacer/:slug/subcultura/:subslug', name: 'CulturaSubcultura', component: CulturaPage, props: true },
  // Ruta directa por ID de categoría para testing
  { path: '/categoria/:id', name: 'CategoryById', component: CulturaPage, props: true },
  
  // Admin
  { path: '/admin/login', name: 'AdminLogin', component: AdminLogin },
  {
    path: '/admin',
    component: () => import('@/layouts/AdminLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      { path: '', name: 'AdminDashboard', component: AdminDashboard },
      { path: 'places', name: 'AdminPlaces', component: AdminPlaces },
      { path: 'business', name: 'AdminBusiness', component: () => import('@/features/admin/Business/AdminBusiness.vue') },
      { path: 'business/new/hotel', name: 'AdminBusinessNewHotel', component: () => import('@/features/admin/Business/NewHotel.vue') },
      { path: 'business/new/restaurant', name: 'AdminBusinessNewRestaurant', component: () => import('@/features/admin/Business/NewRestaurant.vue') },

      // Pro admin sections
      { path: 'analytics', name: 'AdminAnalytics', component: () => import('@/features/admin/Analytics/AdminAnalytics.vue') },
      { path: 'submissions', name: 'AdminSubmissions', component: () => import('@/features/admin/Submissions/AdminSubmissions.vue') },
      { path: 'media', name: 'AdminMedia', component: () => import('@/features/admin/Media/AdminMedia.vue') },
      { path: 'settings', name: 'AdminSettings', component: () => import('@/features/admin/Settings/AdminSettings.vue') },
      { path: 'users', name: 'AdminUsers', component: () => import('@/features/admin/Users/AdminUsers.vue') },
      { path: 'plans', name: 'AdminPlans', component: () => import('@/features/admin/Plans/AdminPlans.vue') },

      // Content managers
      { path: 'content/hotels', name: 'AdminHotels', component: () => import('@/features/admin/Content/AdminHotels.vue') },
      { path: 'content/gastronomy', name: 'AdminGastronomy', component: () => import('@/features/admin/Content/AdminGastronomy.vue') },
      { path: 'content/events', name: 'AdminEvents', component: () => import('@/features/admin/Content/AdminEvents.vue') },
      { path: 'content/nature', name: 'AdminNature', component: () => import('@/features/admin/Content/AdminNature.vue') },
      { path: 'content/articles', name: 'AdminArticles', component: () => import('@/features/admin/Content/AdminArticles.vue') },
      { path: 'content/article-relations', name: 'AdminArticleRelations', component: () => import('@/features/admin/Content/AdminArticleRelations.vue') },
      { path: 'content/locations', name: 'AdminLocations', component: () => import('@/features/admin/Content/AdminLocations.vue') },
      { path: 'content/location-images', name: 'AdminLocationImages', component: () => import('@/features/admin/Content/AdminLocationImages.vue') },
       { path: 'content/location-items', name: 'AdminLocationItems', component: () => import('@/features/admin/Content/AdminLocationItems.vue') },
      { path: 'content/location-subcategories', name: 'AdminLocationSubcategories', component: () => import('@/features/admin/Content/AdminLocationSubcategories.vue') },
      { path: 'content/location-reviews', name: 'AdminLocationReviews', component: () => import('@/features/admin/Content/AdminLocationReviews.vue') },
      { path: 'content/location-policies', name: 'AdminLocationPolicies', component: () => import('@/features/admin/Content/AdminLocationPolicies.vue') },
      { path: 'content/categories', name: 'AdminCategories', component: () => import('@/features/admin/Content/AdminCategories.vue') },
      { path: 'content/subcategories', name: 'AdminSubcategories', component: () => import('@/features/admin/Content/AdminSubcategories.vue') }
    ]
  },
]

const router = createRouter({
  // Usar history mode para URLs limpias sin #
  history: createWebHistory(process.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    return { top: 0 }
  }
})

router.beforeEach((to, from, next) => {
  // Verificar autenticación usando el store
  const authStore = useAuthStore()
  authStore.checkAuth()
  
  // Si la ruta requiere autenticación
  if (to.meta.requiresAuth) {
    if (!authStore.authenticated) {
      next({ path: '/admin/login', query: { redirect: to.fullPath } })
      return
    }
  }
  
  // Si ya está autenticado y va al login, redirigir al dashboard
  if (to.path === '/admin/login' && authStore.authenticated) {
    next('/admin')
    return
  }
  
  next()
})

export default router
