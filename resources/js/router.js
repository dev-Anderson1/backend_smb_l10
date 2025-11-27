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
import Coletes from '@/views/Coletes.vue';
import Algemas from '@/views/Algemas.vue';
import Espadas from '@/views/Espadas.vue';
import PostoGraduacoes from '@/views/PostoGraduacoes.vue';
import Opms from '@/views/Opms.vue';
import ReservasMunicoes from '@/views/ReservasMunicoes.vue';



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

  { 
    path: '/coletes', 
    name: 'Coletes', 
    component: Coletes, 
    meta: { requiresAuth: true } 
  },

  { 
    path: '/algemas', 
    name: 'Algemas', 
    component: Algemas, 
    meta: { requiresAuth: true } 
  },

  { 
    path: '/espadas', 
    name: 'Espadas', 
    component: Espadas, 
    meta: { requiresAuth: true } 
  },

  { 
    path: '/posto_graduacoes', 
    name: 'PostoGraduacoes', 
    component: PostoGraduacoes, 
    meta: { requiresAuth: true } 
  },

  { 
    path: '/opms', 
    name: 'Opms', 
    component: Opms, 
    meta: { requiresAuth: true } 
  },

  { 
    path: '/reservas_municoes', 
    name: 'ReservasMunicoes', 
    component: ReservasMunicoes, 
    meta: { requiresAuth: true } 
  },

// INSTRUTORES
{
  path: '/instrutores',
  name: 'InstrutoresIndex',
  component: () => import('@/views/instrutores/Instrutores.vue'),
  meta: { requiresAuth: true }
},

{
  path: '/instrutores/:id/saldos',
  name: 'InstrutorSaldos',
  component: () => import('@/views/instrutores/InstrutorSaldos.vue'),
  meta: { requiresAuth: true }
},

{
  path: '/instrutores/:id/adicionar-saldo',
  name: 'InstrutorAddSaldo',
  component: () => import('@/views/instrutores/InstrutorAddSaldo.vue'),
  meta: { requiresAuth: true }
},

{
  path: '/turmas',
  name: 'Turmas',
  component: () => import('@/views/Turmas.vue'),
  meta: { requiresAuth: true }
},

{
  path: '/tipos_aula',
  name: 'TiposAula',
  component: () => import('@/views/TiposAula.vue'),
  meta: { requiresAuth: true }
},

{
  path: "/calibres",
  name: "Calibres",
  component: () => import("@/views/Calibres.vue"),
  meta: { requiresAuth: true }
},

{
  path: "/saldos/adicionar",
  name: "SaldoAddGlobal",
  component: () => import("@/views/instrutores/AdicionarSaldoGlobal.vue"),
  meta: { requiresAuth: true, adminOnly: true }

},

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
