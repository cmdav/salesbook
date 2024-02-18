import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../views/UserDashboard.vue'
import IndexPage from '../views/auth/IndexPage.vue'
import useAuthStore from '../store';





const routes = [
  {
    path: '/',
    name: 'home',
    component: IndexPage
  },
  {
    path: '/register',
    name: 'register',
    component: function () {
      return import( '../views/auth/RegisterPage.vue')
     }

  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component:Dashboard,
    meta: {
      requiresAuth: false,
    },
  },
  
  {
    path: '/forbidden',
    name: 'forbidden',
    component: function () {
     return import( '../views/auth/401Page.vue')
    }
  },
  
  {
    path: '/email-verification/:token',
    name: 'email-verification',
    component: function () {
     return import( '../views/auth/VerifyEmailPage.vue')
    }
  },
  {
    path: '/logout',
    name: 'logout',
    component: function () {
     return import( '../views/UserDashboard.vue')
    }
  },
  {
    // Catch-all route for any other paths
    path: '/:catchAll(.*)',
    name: 'not-found',
    component: function () {
      return import( '../views/auth/404Page.vue')
     }
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})


  
  // Navigation guard
router.beforeEach((to, from, next) => {
  const requiresAuth = to.meta.requiresAuth;

      // Check if the route requires authentication
      if (requiresAuth) {
        const isUserLoggedIn = useAuthStore().token !== null;

        // redirect non login users
        if (!isUserLoggedIn) {
          next({ path: '/forbidden' });
        } else {
          
          next();
        }
      } else {
        // un protected route
        next();
      }
});
export default router