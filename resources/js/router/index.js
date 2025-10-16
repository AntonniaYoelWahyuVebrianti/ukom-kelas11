import { createRouter, createWebHistory } from 'vue-router'

const HomeView = () => import('../../views/HomeView.vue')
const CartView = () => import('../../views/CartView.vue')
const CheckoutView = () => import('../../views/CheckoutView.vue')
const ProfileView = () => import('../../views/ProfileView.vue')
const AdminLayout = () => import('../../views/admin/AdminLayout.vue')
const AdminDashboard = () => import('../../views/admin/AdminDashboard.vue')
const AdminCategories = () => import('../../views/admin/AdminCategories.vue')
const AdminProducts = () => import('../../views/admin/AdminProducts.vue')
const AdminOrders = () => import('../../views/admin/AdminOrders.vue')

export const router = createRouter({
  history: createWebHistory(),
  scrollBehavior() {
    return { top: 0 }
  },
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/cart',
      name: 'cart',
      component: CartView,
    },
    {
      path: '/checkout',
      name: 'checkout',
      component: CheckoutView,
      meta: { requiresAuth: true },
    },
    {
      path: '/profile/addresses',
      name: 'profile-addresses',
      component: ProfileView,
      meta: { requiresAuth: true },
    },
    {
      path: '/admin',
      component: AdminLayout,
      meta: { requiresAdmin: true },
      children: [
        {
          path: '',
          name: 'admin-dashboard',
          component: AdminDashboard,
        },
        {
          path: 'categories',
          name: 'admin-categories',
          component: AdminCategories,
        },
        {
          path: 'products',
          name: 'admin-products',
          component: AdminProducts,
        },
        {
          path: 'orders',
          name: 'admin-orders',
          component: AdminOrders,
        },
      ],
    },
  ],
})

router.beforeEach((to, from, next) => {
  const user = window.Laravel?.user
  const isAuthenticated = Boolean(user)
  const isAdmin = Boolean(user?.is_admin)

  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ path: '/login', query: { redirect: to.fullPath } })
    return
  }

  if (to.meta.requiresAdmin) {
    if (!isAuthenticated) {
      next({ path: '/login', query: { redirect: to.fullPath } })
      return
    }

    if (!isAdmin) {
      next({ name: 'home' })
      return
    }
  }

  next()
})
