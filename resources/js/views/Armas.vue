<template>
  <v-container class="pa-6">
    <!-- 🔹 Cabeçalho -->
    <v-row class="align-center justify-space-between mb-4">
      <h1 class="text-h5 font-weight-bold">Cadastro de Armas</h1>
      <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog">
        Nova Arma
      </v-btn>
    </v-row>

    <!-- 📋 Tabela de armas -->
    <v-data-table
  :headers="headers"
  :items="armas"
  :loading="loading"
  class="elevation-2 tabela-armas"
  item-value="id"
  no-data-text="Nenhuma arma cadastrada"
>
      <!-- ✅ Coluna de ações -->
      <template #item.acoes="{ item }">
        <v-btn
          icon
          size="small"
          color="primary"
          variant="text"
          @click="editArma(item)"
        >
          <v-icon>mdi-pencil</v-icon>
        </v-btn>

        <v-btn
          icon
          size="small"
          color="error"
          variant="text"
          @click="confirmDelete(item)"
        >
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>

    <!-- 🧩 Modal de cadastro/edição -->
    <v-dialog v-model="dialog" persistent max-width="500px">
      <v-card>
        <v-card-title class="text-h6 font-weight-bold">
          {{ editMode ? 'Editar Arma' : 'Nova Arma' }}
        </v-card-title>

       <v-card-text>
  <v-form ref="formRef" v-model="valid" lazy-validation>

    <!-- MODELO -->
   <v-select
  v-model="form.modelo_id"
  :items="modelos"
  item-title="name"
  item-value="id"
  label="Modelo"
  :rules="[v => !!v || 'Campo obrigatório']"
  @update:model-value="onModeloSelected"
/>


    <!-- QUANTIDADE DE CARREGADORES -->
    <v-text-field
      v-model.number="form.quantidade_carregadores"
      type="number"
      label="Quantidade de Carregadores"
      :rules="[v => v > 0 || 'Informe uma quantidade válida']"
    />

    <!-- NÚMERO DE SÉRIE -->
    <v-text-field
      v-model="form.numero_serie"
      label="Número de Série"
      :rules="[v => !!v || 'Campo obrigatório']"
    />
  </v-form>
</v-card-text>



        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="closeDialog">Cancelar</v-btn>
          <v-btn color="primary" @click="saveArma">
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
          Tem certeza que deseja excluir a arma
          <strong>#{{ selectedArma?.id }}</strong>?
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="confirmDialog = false">Cancelar</v-btn>
          <v-btn color="error" @click="deleteArma">Excluir</v-btn>
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
const armas = ref([])
const modelos = ref([])
const municoes = ref([])
const carregadores = ref([])
const loading = ref(false)
const dialog = ref(false)
const editMode = ref(false)
const valid = ref(false)
const confirmDialog = ref(false)
const selectedArma = ref(null)

const formRef = ref(null)
const form = ref({
  id: null,
  modelo_id: '',
  quantidade_carregadores: 1,
  numero_serie: '',
})



// ================================
// 📋 COLUNAS DA TABELA
// ================================
const headers = [
  { title: 'ID', key: 'id', align: 'start' },
  { title: 'Modelo', key: 'modelo.name' },
  { title: 'Qtd. Carregadores', key: 'quantidade_carregadores' },
  { title: 'Número de Série', key: 'numero_serie' },
  { title: 'Ações', key: 'acoes', sortable: false, align: 'center' },
]




// ================================
// ⚙️ CONFIGURAÇÃO DO AXIOS
// ================================
// const api = axios.create({
//   baseURL: 'http://localhost:8000/api', // ajuste conforme seu backend
// })

// ================================
// 🚀 FUNÇÕES DE INICIALIZAÇÃO
// ================================
onMounted(() => {
  fetchArmas()
  fetchModelos()
  fetchMunicoes()
  fetchCarregadores()
})

// ================================
// 📡 REQUISIÇÕES À API
// ================================
const fetchArmas = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/armas')
    armas.value = data
  } catch (err) {
    console.error('Erro ao carregar armas', err)
  } finally {
    loading.value = false
  }
}

const fetchModelos = async () => {
  const { data } = await api.get('/modelo_armas')
  modelos.value = data
}

const fetchMunicoes = async () => {
  const { data } = await api.get('/municoes')
  municoes.value = data
}
const fetchCarregadores = async () => {
  const { data } = await api.get('/carregadores')
  carregadores.value = data
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
const saveArma = async () => {
  const isValid = await formRef.value.validate()
  if (!isValid) return

  try {
    if (editMode.value) {
      await api.put(`/armas/${form.value.id}`, form.value)
    } else {
      await api.post('/armas', form.value)
    }
    await fetchArmas()
    dialog.value = false
  } catch (err) {
    console.error('Erro ao salvar arma', err.response?.data || err)
  }
}

const editArma = (arma) => {
  form.value = {
    id: arma.id,
    modelo_id: arma.modelo_id,
    quantidade_carregadores: arma.quantidade_carregadores,
    numero_serie: arma.numero_serie,
  }
  editMode.value = true
  dialog.value = true
}



// Abrir diálogo de confirmação
const confirmDelete = (arma) => {
  selectedArma.value = arma
  confirmDialog.value = true
}

// Excluir arma
const deleteArma = async () => {
  try {
    await api.delete(`/armas/${selectedArma.value.id}`)
    confirmDialog.value = false
    await fetchArmas()
  } catch (err) {
    console.error('Erro ao excluir arma', err)
  }
}

// Resetar formulário
const resetForm = () => {
  form.value = { id: null, modelo_id: '', quantidade_carregadores: '', numero_serie: '' }
}

</script>
<style scoped>
.tabela-armas thead {
  background-color: #f2f2f2; /* 🔹 cinza clarinho */
}
.tabela-armas thead th {
  color: #333;              /* 🔹 texto escuro */
  font-weight: 600;
}
</style>
