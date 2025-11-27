<template>
  <v-container class="pa-6">
    <!-- 🔹 Cabeçalho -->
    <v-row class="align-center justify-space-between mb-4">
      <h1 class="text-h5 font-weight-bold">Cadastro de Munições</h1>
      <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog">
        Nova Munição
      </v-btn>
    </v-row>

    <!-- 📋 Tabela de munições -->
   <v-data-table
  :headers="headers"
  :items="municoes"
  :loading="loading"
  class="elevation-2 tabela-municoes"
  item-value="id"
  no-data-text="Nenhuma munição cadastrada"
>
  <!-- Calibre formatado -->
  <template #item.calibre="{ item }">
    {{ item.calibre ? item.calibre.nome + ' ' + item.calibre.medidas : '-' }}
  </template>

  <!-- Ações -->
  <template #item.acoes="{ item }">
    <v-btn icon size="small" variant="text" color="primary" @click="editMunicao(item)">
      <v-icon>mdi-pencil</v-icon>
    </v-btn>
    <v-btn icon size="small" variant="text" color="error" @click="confirmDelete(item)">
      <v-icon>mdi-delete</v-icon>
    </v-btn>
  </template>
</v-data-table>


    <!-- 🧩 Modal de cadastro/edição -->
    <v-dialog v-model="dialog" persistent max-width="500px">
      <v-card>
        <v-card-title class="text-h6 font-weight-bold">
          {{ editMode ? 'Editar Munição' : 'Nova Munição' }}
        </v-card-title>

        <v-card-text>
          <v-form ref="formRef" v-model="valid" lazy-validation>
            <v-text-field
              v-model="form.tipo"
              label="Tipo de Munição"
              placeholder="Ex: CBC, MAGTECH, etc."
              :rules="[v => !!v || 'Campo obrigatório']"
            />

            <v-select
              v-model="form.calibre_id"
              :items="calibres"
              item-title="nome"
              item-value="id"
              label="Calibre"
              :rules="[v => !!v || 'Campo obrigatório']"
            />

            <v-text-field
              v-model="form.quantidade"
              label="Quantidade"
              type="number"
              min="1"
              :rules="[v => v > 0 || 'Informe uma quantidade válida']"
            />
          </v-form>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="closeDialog">Cancelar</v-btn>
          <v-btn color="primary" @click="saveMunicao">
            {{ editMode ? 'Salvar Alterações' : 'Cadastrar' }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- ⚠️ Diálogo de confirmação de exclusão -->
    <v-dialog v-model="confirmDialog" max-width="400px">
      <v-card>
        <v-card-title class="text-h6 font-weight-bold">
          Confirmar exclusão
        </v-card-title>
        <v-card-text>
          Tem certeza que deseja excluir a munição
          <strong>{{ selectedMunicao?.tipo }}</strong>?
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="confirmDialog = false">Cancelar</v-btn>
          <v-btn color="error" @click="deleteMunicao">Excluir</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import api from "@/services/api";

// ================================
// 📦 ESTADOS REATIVOS
// ================================
const municoes = ref([])
const calibres = ref([])
const armas = ref([])
const loading = ref(false)
const dialog = ref(false)
const editMode = ref(false)
const valid = ref(false)
const confirmDialog = ref(false)
const selectedMunicao = ref(null)

const formRef = ref(null)
const form = ref({
  id: null,
  tipo: '',
  calibre_id: '',
  arma_id: '',
  quantidade: 1
})

// ================================
// 📋 CABEÇALHO DA TABELA
// ================================
const headers = [
  { title: 'ID', key: 'id', align: 'start' },
  { title: 'Tipo', key: 'tipo' },
  { title: 'Calibre', key: 'calibre' },
  { title: 'Quantidade', key: 'quantidade' },
  { title: 'Ações', key: 'acoes', sortable: false, align: 'center' }
]

// ================================
// ⚙️ CONFIGURAÇÃO DO AXIOS
// ================================
// const api = axios.create({
//   baseURL: 'http://localhost:8000/api',
// })

// ================================
// 🚀 INICIALIZAÇÃO
// ================================
onMounted(() => {
  fetchMunicoes()
  fetchCalibres()
  fetchArmas()
})

// ================================
// 📡 REQUISIÇÕES À API
// ================================
const fetchMunicoes = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/municoes')
    municoes.value = data
  } catch (err) {
    console.error('Erro ao carregar munições', err)
  } finally {
    loading.value = false
  }
}

const fetchCalibres = async () => {
  const { data } = await api.get('/calibres')
  calibres.value = data
}

const fetchArmas = async () => {
  const { data } = await api.get('/armas')
  armas.value = data
}

// ================================
// 💾 CRUD
// ================================
const openDialog = () => {
  editMode.value = false
  resetForm()
  dialog.value = true
}

const closeDialog = () => {
  dialog.value = false
}

const saveMunicao = async () => {
  const isValid = await formRef.value.validate()
  if (!isValid) return

  try {
    if (editMode.value) {
      await api.put(`/municoes/${form.value.id}`, form.value)
    } else {
      await api.post('/municoes', form.value)
    }
    await fetchMunicoes()
    dialog.value = false
  } catch (err) {
    console.error('Erro ao salvar munição', err)
  }
}

const editMunicao = (municao) => {
  form.value = { ...municao }
  editMode.value = true
  dialog.value = true
}

const confirmDelete = (municao) => {
  selectedMunicao.value = municao
  confirmDialog.value = true
}

const deleteMunicao = async () => {
  try {
    await api.delete(`/municoes/${selectedMunicao.value.id}`)
    confirmDialog.value = false
    await fetchMunicoes()
  } catch (err) {
    console.error('Erro ao excluir munição', err)
  }
}

const resetForm = () => {
  form.value = { id: null, tipo: '', calibre_id: '', arma_id: '', quantidade: 1 }
}
</script>

<style scoped>
.tabela-municoes thead {
  background-color: #f2f2f2;
}
.tabela-municoes thead th {
  color: #333;
  font-weight: 600;
}
</style>
