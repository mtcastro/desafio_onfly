import UserService from 'src/services/userService.js'

const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'home',
        component: () => import('pages/Home.vue')
      },
      {
        path: 'expense-create/:id?',
        name: 'formExpenses',
        component: () => import('pages/FormExpense.vue')
      },
      {
        path: '/logout',
        meta: { guest: true },
        beforeEnter: (to, from, next) => {
          UserService.logout()
          next('/login')
        }
      }

    ]
  },
  {
    path: '/login',
    name: 'login',
    meta: { requiresAuth: false, layout: null },
    component: () => import('pages/Login.vue')
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
