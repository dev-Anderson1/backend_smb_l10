<!-- src/components/layout/Sidebar.vue -->
<template>
  <v-navigation-drawer
    :model-value="isOpen"
    :rail="!isOpen"
    permanent
    elevation="4"
    rail-width="64"
    width="256"
    app
  >
    <!-- Cabeçalho -->
    <div class="d-flex align-center justify-space-between pa-2">
      <v-avatar
        v-if="isOpen"
        size="36"
        color="primary"
      >
        <v-icon color="white">mdi-shield</v-icon>
      </v-avatar>
      
      <v-btn
        variant="text"
        icon
        @click="toggleSidebar"
        size="small"
        :class="{ 'mx-auto': !isOpen }"
      >
        <v-icon>{{ chevronIcon }}</v-icon>
      </v-btn>
    </div>

    <v-divider />

    <!-- Menu Items -->
   <v-list nav>
  <template v-for="item in filteredMenu">

    <!-- ITEM COM CHILDREN (MÓDULO) -->
    <v-list-group
      v-if="item.children"
      :key="item.name"
      value="false"
    >
      <template #activator="{ props }">
        <v-list-item
          v-bind="props"
          :title="item.name"
          rounded="xl"
        >
          <template #prepend>
            <v-icon>{{ item.icon }}</v-icon>
          </template>
        </v-list-item>
      </template>

      <!-- SUB-ITENS -->
      <v-list-item
        v-for="child in item.children"
        :key="child.path"
        :to="child.path"
        :title="child.name"
        rounded="xl"
        class="ml-6"
      >
        <template #prepend>
          <v-icon small>{{ child.icon }}</v-icon>
        </template>
      </v-list-item>

    </v-list-group>

    <!-- ITEM NORMAL -->
    <v-list-item
      v-else
      :key="item.path"
      :to="item.path"
      :title="item.name"
      rounded="xl"
    >
      <template #prepend>
        <v-icon>{{ item.icon }}</v-icon>
      </template>
    </v-list-item>

  </template>
</v-list>


    <!-- User section no final -->
    <template #append>
      <v-divider />
      <v-list>
        <v-list-item
          :title="userName"
          :subtitle="userRole"
          density="comfortable"
        >
          <template #prepend>
            <v-avatar color="primary" size="32">
              <v-icon color="white">mdi-account</v-icon>
            </v-avatar>
          </template>
          
          <template #append>
            <v-menu v-if="isOpen">
              <template>
                <v-btn icon size="small" v-bind="props">
                  <v-icon>mdi-dots-vertical</v-icon>
                  

                </v-btn>
              </template>
              <v-list>
                <v-list-item 
                  prepend-icon="mdi-account" 
                  title="Perfil" 
                  @click="goToProfile"
                />
                <v-list-item 
                  prepend-icon="mdi-logout"  
                  title="Sair"   
                  @click="logout"
                />
              </v-list>
            </v-menu>
          </template>
        </v-list-item>
      </v-list>
    </template>
  </v-navigation-drawer>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'

const props = defineProps({
  isOpen: { 
    type: Boolean, 
    required: true 
  }
})

const emit = defineEmits(['toggle-sidebar'])
const store = useStore()
const router = useRouter()

const toggleSidebar = () => emit('toggle-sidebar')

const chevronIcon = computed(() => 
  props.isOpen ? 'mdi-chevron-left' : 'mdi-chevron-right'
)

 const menuItems = [
  // TODOS → admin, instrutor e user
  { name: 'Dashboard', path: '/dashboard', icon: 'mdi-view-dashboard', role: 'all' },

  // ADMIN → vê a lista de instrutores
  { name: 'Instrutores', path: '/instrutores', icon: 'mdi-account-tie', role: 'admin' },

  // ADMIN → vê saldos de qualquer instrutor
  //{ name: 'Saldos de Instrutores', path: '/instrutores/saldos', icon: 'mdi-ammunition', role: 'admin' },
    // ADMIN → adiciona saldo para qualquer instrutor
  //{ name: 'Adicionar Saldo', path: '/saldos/adicionar', icon: 'mdi-plus-circle', role: 'admin' },


  // INSTRUTOR → vê apenas o saldo dele
  { name: 'Meu Saldo', path: `/instrutores/${store.state.auth.user?.id}/saldos`, icon: 'mdi-ammunition', role: 'all' },

  // ADMIN → vê todas reservas
  // INSTRUTOR → vê apenas reservas dele
  { name: 'Reservas de Munições', path: '/reservas_municoes', icon: 'mdi-clipboard-list', role: 'all' },

  // ADMIN + INSTRUTOR + USER → todo mundo vê Cautelas
  { name: 'Cautelas', path: '/cautelas', icon: 'mdi-file-document', role: 'all' },

  // ADMIN → vê cadastros
  { name: 'Cadastros', path: '/cadastros', icon: 'mdi-folder-cog', role: 'admin' },

  // ADMIN
  { name: 'Usuários', path: '/usuarios', icon: 'mdi-account-group', role: 'admin' },

  // ADMIN
  { name: 'Relatórios', path: '/relatorios', icon: 'mdi-chart-box', role: 'admin' },

  // ADMIN
  { name: 'Configurações', path: '/configuracoes', icon: 'mdi-cog', role: 'admin' },
];



const userName = computed(() => store.state.auth?.user?.name || 'Usuário')
const userRole = computed(() => {
  const u = store.state.auth?.user || {}
  const isAdmin = u.is_admin == 1 || u.is_admin === true || u.is_admin === "1"
  const isInstrutor = u.is_instrutor == 1 || u.is_instrutor === true || u.is_instrutor === "1"

  if (isAdmin) return 'admin'
  if (isInstrutor) return 'instrutor'
  return 'user'
})

const filteredMenu = computed(() => {
  const role = userRole.value;

  // ADMIN → vê tudo normalmente
  if (role === "admin") return menuItems;

  // INSTRUTOR
  if (role === "instrutor") {
    return menuItems.filter(item => {

      // permite tudo que for "all"
      if (item.role === "all") return true;

      // permite itens específicos do instrutor
      if (item.role === "instrutor") return true;

      // bloqueia itens de admin
      return false;
    });
  }

  // USUÁRIO COMUM (role === user)
  return menuItems.filter(item => item.role === "all");
});






const goToProfile = () => router.push('/profile')

const logout = async () => {
  await store.dispatch('auth/logout')
  router.replace({ name: 'Login' })
}
</script>
