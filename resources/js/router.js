import { createRouter, createWebHistory } from 'vue-router';
import store from '@/store/index';

// Views principais
import Login from '@/views/Login.vue';
import Dashboard from '@/components/dashboard/Dashboard.vue';

// Páginas principais
import Usuarios from '@/views/Usuarios.vue';
import Relatorios from '@/views/Relatorios.vue';

// Cautelas
import CautelasIndex from '@/views/cautelas/CautelasIndex.vue';

// Cadastros (Módulos internos)
import Armas from '@/views/Armas.vue';
import Municoes from '@/views/Municoes.vue';
// Caso você tenha coletes/algemas/etc, importe depois

const routes = [

  // LOGIN
  { 
    path: '/login', 
    name: 'Login', 
    component: Login, 
    meta: { requiresGuest: true, title: 'Login' } 
  },

  // REDIRECIONAMENTO PADRÃO
  { 
    path: '/', 
    redirect: '/dashboard',
    meta: { requiresAuth: true }
  },

  // DASHBOARD
  { 
    path: '/dashboard', 
    name: 'Dashboard', 
    component: Dashboard, 
    meta: { requiresAuth: true } 
  },

  // CAUTELAS
  { 
    path: '/cautelas', 
    name: 'CautelasIndex', 
    component: () => import('@/views/cautelas/CautelasIndex.vue'),
    meta: { requiresAuth: true }
  },
  { 
    path: '/cautelas/create', 
    name: 'CautelasCreate', 
    component: () => import('@/views/cautelas/CautelasCreate.vue'),
    meta: { requiresAuth: true }
  },
  { 
    path: '/cautelas/:id', 
    name: 'CautelasShow', 
    component: () => import('@/views/cautelas/CautelasShow.vue'),
    meta: { requiresAuth: true }
  },

  // MÓDULOS DE CADASTROS
  {
    path: '/cadastros',
    name: 'CadastrosIndex',
    component: () => import('@/views/cadastros/Index.vue'),
    meta: { requiresAuth: true }
  },

  // ROTAS INTERNAS DO MÓDULO DE CADASTROS
  { 
    path: '/armas', 
    name: 'Armas', 
    component: Armas, 
    meta: { requiresAuth: true } 
  },

  { 
    path: '/municoes', 
    name: 'Municoes', 
    component: Municoes, 
    meta: { requiresAuth: true } 
  },

  // Se tiver mais, basta adicionar:
  // {
  //   path: '/coletes',
  //   name: 'Coletes',
  //   component: () => import('@/views/Coletes.vue'),
  //   meta: { requiresAuth: true }
  // },

  // USUÁRIOS
  { 
    path: '/usuarios', 
    name: 'Usuarios', 
    component: Usuarios, 
    meta: { requiresAuth: true } 
  },

  // RELATÓRIOS
  { 
    path: '/relatorios', 
    name: 'Relatorios', 
    component: Relatorios, 
    meta: { requiresAuth: true } 
  },

  // ROTA 404
  { 
    path: '/:catchAll(.*)', 
    redirect: '/login' 
  },
];

// CONFIGURAÇÃO DO ROUTER
const router = createRouter({
  history: createWebHistory(),
  routes,
});

// GUARD DE AUTENTICAÇÃO
router.beforeEach(async (to, from, next) => {
  const token = store.state.auth.token;

  if (token && !store.state.auth.user) {
    try {
      await store.dispatch('auth/fetchUser');
    } catch {
      await store.dispatch('auth/logout');
    }
  }

  const isAuth = store.getters['auth/isAuthenticated'];

  if (to.meta.requiresAuth && !isAuth) {
    next({ name: 'Login', query: { redirect: to.fullPath } });
  } 
  else if (to.meta.requiresGuest && isAuth) {
    next({ name: 'Dashboard' });
  } 
  else {
    next();
  }
});

export default router;
