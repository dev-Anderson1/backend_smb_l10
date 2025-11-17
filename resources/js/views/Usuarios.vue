<template>
  <v-container class="pa-6">
    <!-- 🔹 Cabeçalho -->
    <v-row class="align-center justify-space-between mb-4">
      <h1 class="text-h5 font-weight-bold">Gerenciamento de Usuários</h1>
      <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog">
        Novo Usuário
      </v-btn>
    </v-row>

    <!-- 📋 Tabela -->
    <v-data-table
      :headers="headers"
      :items="usuarios"
      :loading="loading"
      class="elevation-2 tabela-usuarios"
      item-value="id"
      no-data-text="Nenhum usuário encontrado"
    >
      <!-- Colunas personalizadas -->
      <template #item.opm="{ item }">
        {{ item.opm?.bpm || '—' }}
      </template>

      <template #item.postoGraduacao="{ item }">
        {{ item.posto_graduacao?.nome || '—' }}
      </template>

      <template #item.acoes="{ item }">
        <v-btn icon size="small" color="primary" variant="text" @click="editUsuario(item)">
          <v-icon>mdi-pencil</v-icon>
        </v-btn>
        <v-btn icon size="small" color="error" variant="text" @click="confirmDelete(item)">
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>

    <!-- 🧩 Modal de cadastro/edição -->
    <v-dialog v-model="dialog" persistent max-width="600px">
      <v-card>
        <v-card-title class="text-h6 font-weight-bold">
          {{ editMode ? 'Editar Usuário' : 'Novo Usuário' }}
        </v-card-title>

        <v-card-text>
          <v-form ref="formRef" v-model="valid" lazy-validation>
            <v-text-field v-model="form.name" label="Nome completo" :rules="[v => !!v || 'Campo obrigatório']" />
            <v-text-field v-model="form.email" label="Email" type="email" :rules="[v => !!v || 'Campo obrigatório']" />
            <v-text-field v-model="form.apelido" label="Apelido" />

            <v-text-field
              v-if="!editMode"
              v-model="form.password"
              label="Senha"
              type="password"
              :rules="[v => v && v.length >= 6 || 'Mínimo de 6 caracteres']"
            />

            <v-select
              v-model="form.is_admin"
              :items="[
                { text: 'Usuário Comum', value: 0 },
                { text: 'Administrador', value: 1 }
              ]"
              item-title="text"
              item-value="value"
              label="Tipo de Usuário"
            />

            <!-- 🔹 Selecionar OPM -->
            <v-select
              v-model="form.opm_id"
              :items="opms"
              item-title="bpm"
              item-value="id"
              label="OPM"
              :rules="[v => !!v || 'Campo obrigatório']"
            />

            <!-- 🔹 Selecionar Posto/Graduação -->
            <v-select
              v-model="form.posto_graduacoes_id"
              :items="postos"
              item-title="nome"
              item-value="id"
              label="Posto / Graduação"
              :rules="[v => !!v || 'Campo obrigatório']"
            />
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="closeDialog">Cancelar</v-btn>
          <v-btn color="primary" @click="saveUsuario">
            {{ editMode ? 'Salvar Alterações' : 'Cadastrar' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- ⚠️ Confirmação de exclusão -->
    <v-dialog v-model="confirmDialog" max-width="400px">
      <v-card>
        <v-card-title class="text-h6 font-weight-bold">Confirmar exclusão</v-card-title>
        <v-card-text>
          Tem certeza que deseja excluir o usuário
          <strong>{{ selectedUsuario?.name }}</strong>?
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="confirmDialog = false">Cancelar</v-btn>
          <v-btn color="error" @click="deleteUsuario">Excluir</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import api from "@/services/api";

// Estado
const usuarios = ref([])
const opms = ref([])
const postos = ref([])
const loading = ref(false)
const dialog = ref(false)
const editMode = ref(false)
const valid = ref(false)
const confirmDialog = ref(false)
const selectedUsuario = ref(null)

const formRef = ref(null)
const form = ref({
  id: null,
  name: '',
  email: '',
  apelido: '',
  password: '',
  is_admin: 0,
  opm_id: '',
  posto_graduacoes_id: ''
})

// Cabeçalhos
const headers = [
  { title: 'ID', key: 'id', align: 'start' },
  { title: 'Nome', key: 'name' },
  { title: 'Email', key: 'email' },
  { title: 'Apelido', key: 'apelido' },
  { title: 'OPM', key: 'opm' },
  { title: 'Posto / Graduação', key: 'postoGraduacao' },
  //{ title: 'Admin', key: 'is_admin' },
  { title: 'Ações', key: 'acoes', sortable: false, align: 'center' }
]

// API
// const api = axios.create({
//   baseURL: 'http://localhost:8000/api'
// })

// Lifecycle
onMounted(() => {
  fetchUsuarios()
  fetchOpms()
  fetchPostos()
})

// Métodos
const fetchUsuarios = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/users')
    usuarios.value = data
  } catch (err) {
    console.error('Erro ao carregar usuários', err)
  } finally {
    loading.value = false
  }
}

const fetchOpms = async () => {
  try {
    const { data } = await api.get('/opms')
    opms.value = data
  } catch (err) {
    console.error('Erro ao carregar OPMs', err)
  }
}

const fetchPostos = async () => {
  try {
    const { data } = await api.get('/posto_graduacoes')
    postos.value = data
  } catch (err) {
    console.error('Erro ao carregar postos', err)
  }
}

const openDialog = () => {
  editMode.value = false
  resetForm()
  dialog.value = true
}

const closeDialog = () => (dialog.value = false)

const saveUsuario = async () => {
  const isValid = await formRef.value.validate()
  if (!isValid) return

  try {
    if (editMode.value) {
      await api.put(`/users/${form.value.id}`, form.value)
    } else {
      await api.post('/register', form.value)
    }
    await fetchUsuarios()
    dialog.value = false
  } catch (err) {
    console.error('Erro ao salvar usuário', err)
  }
}

const editUsuario = (usuario) => {
  form.value = { ...usuario, password: '' }
  editMode.value = true
  dialog.value = true
}

const confirmDelete = (usuario) => {
  selectedUsuario.value = usuario
  confirmDialog.value = true
}

const deleteUsuario = async () => {
  try {
    await api.delete(`/users/${selectedUsuario.value.id}`)
    confirmDialog.value = false
    await fetchUsuarios()
  } catch (err) {
    console.error('Erro ao excluir usuário', err)
  }
}

const resetForm = () => {
  form.value = {
    id: null,
    name: '',
    email: '',
    apelido: '',
    password: '',
    is_admin: 0,
    opm_id: '',
    posto_graduacoes_id: ''
  }
}
</script>

<style scoped>
.tabela-usuarios thead {
  background-color: #f2f2f2;
}
.tabela-usuarios thead th {
  color: #333;
  font-weight: 600;
}
</style>
