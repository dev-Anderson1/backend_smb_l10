  import axios from 'axios';
  import store from '@/store';
  import router from '@/router';

  const api = axios.create({
  baseURL:
    process.env.NODE_ENV === 'development'
      ? 'http://localhost:8000/api'
      : 'https://sisapmg.devcons.com.br/api',

  timeout: 20000,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
});



  api.interceptors.request.use((config) => {
  const token = store.state.auth?.token || localStorage.getItem('token');
  if (token) config.headers.Authorization = `Bearer ${token}`;
  return config;
});


  api.interceptors.response.use(
  (r) => r,
  async (err) => {
    if (err?.response?.status === 401) {
      try {
        await store.dispatch('auth/logout');
      } catch {}

      // evita redirecionar pra login se já estiver nela
      if (router.currentRoute.value.name !== 'Login') {
        router.replace({ name: 'Login' });
      }
    }
    return Promise.reject(err);
  }
);

  export default api;
