<template>
  <v-container class="pa-6">
    <v-row class="align-center justify-space-between mb-4">
      <h1 class="text-h5 font-weight-bold">Cadastro de Patentes</h1>
      <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog">Nova Patente</v-btn>
    </v-row>

    <v-data-table :headers="headers" :items="items" :loading="loading" class="elevation-2" item-value="id" no-data-text="Nenhuma patente cadastrada">
      <template #item.acoes="{ item }">
        <v-btn icon size="small" color="primary" variant="text" @click="editItem(item)"><v-icon>mdi-pencil</v-icon></v-btn>
        <v-btn icon size="small" color="error" variant="text" @click="confirmDelete(item)"><v-icon>mdi-delete</v-icon></v-btn>
      </template>
    </v-data-table>

    <v-dialog v-model="dialog" persistent max-width="500px">
      <v-card>
        <v-card-title class="text-h6 font-weight-bold">{{ editMode ? 'Editar Patente' : 'Nova Patente' }}</v-card-title>
        <v-card-text>
          <v-form ref="formRef" v-model="valid" lazy-validation>
            <v-text-field v-model="form.nome" label="Nome" :rules="[v => !!v || 'Campo obrigatório']" />
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="closeDialog">Cancelar</v-btn>
          <v-btn color="primary" @click="saveItem">{{ editMode ? 'Salvar Alterações' : 'Cadastrar' }}</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="confirmDialog" max-width="400px">
      <v-card>
        <v-card-title class="text-h6 font-weight-bold">Confirmar exclusão</v-card-title>
        <v-card-text>Tem certeza que deseja excluir a patente <strong>#{{ selected?.id }}</strong>?</v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="confirmDialog = false">Cancelar</v-btn>
          <v-btn color="error" @click="deleteItem">Excluir</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const items = ref([])
const loading = ref(false)
const dialog = ref(false)
const editMode = ref(false)
const valid = ref(false)
const confirmDialog = ref(false)
const selected = ref(null)
const formRef = ref(null)
const form = ref({ id: null, nome: '' })

const headers = [
  { title: 'ID', key: 'id', align: 'start' },
  { title: 'Nome', key: 'nome' },
  { title: 'Ações', key: 'acoes', sortable: false, align: 'center' },
]

onMounted(() => fetchItems())

const fetchItems = async () => {
  loading.value = true
  try { const { data } = await api.get('/posto_graduacoes'); items.value = data } catch (err) { console.error('Erro ao carregar patentes', err) } finally { loading.value = false }
}

const openDialog = () => { editMode.value = false; resetForm(); dialog.value = true }
const closeDialog = () => { dialog.value = false }

const saveItem = async () => {
  const isValid = await formRef.value.validate()
  if (!isValid) return
  try {
    if (editMode.value) await api.put(`/posto_graduacoes/${form.value.id}`, form.value)
    else await api.post('/posto_graduacoes', form.value)
    await fetchItems(); dialog.value = false
  } catch (err) { console.error('Erro ao salvar patente', err.response?.data || err) }
}

const editItem = (it) => { form.value = { id: it.id, nome: it.nome }; editMode.value = true; dialog.value = true }
const confirmDelete = (it) => { selected.value = it; confirmDialog.value = true }
const deleteItem = async () => { try { await api.delete(`/posto_graduacoes/${selected.value.id}`); confirmDialog.value = false; await fetchItems() } catch (err) { console.error('Erro ao excluir patente', err) } }
const resetForm = () => { form.value = { id: null, nome: '' } }
</script>

<style scoped>
.tabela thead { background-color: #f2f2f2 }
</style>
