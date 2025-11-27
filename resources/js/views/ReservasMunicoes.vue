<template>
  <v-container class="pa-6">
    <v-row class="align-center justify-space-between mb-4">
      <h1 class="text-h5 font-weight-bold">Reserva de Munições</h1>
      <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog">Nova Reserva</v-btn>
    </v-row>

    <v-data-table
      :headers="headers"
      :items="reservas"
      :loading="loading"
      class="elevation-2"
      item-value="id"
      no-data-text="Nenhuma reserva encontrada"
    >
      <template #item.total_municoes="{ item }">
        {{ item.total_municoes || (item.alunos * item.municoes_por_aluno) }}
      </template>

      <template #item.acoes="{ item }">
        <v-btn
          v-if="item.status === 'pending' && isInstructor"
          color="primary"
          variant="text"
          @click="cancelarReserva(item)"
        >Cancelar</v-btn>

        <v-btn
          v-if="item.status === 'pending' && isApprover"
          color="success"
          variant="text"
          @click="aprovarReserva(item)"
        >Aprovar</v-btn>

        <v-btn
          v-if="item.status === 'approved' && isInstructor"
          color="secondary"
          variant="text"
          @click="openDevolucao(item)"
        >Registrar Devolução</v-btn>
      </template>
    </v-data-table>

    <!-- Dialog para criar reserva -->
    <v-dialog v-model="dialog" persistent max-width="600px">
      <v-card>
        <v-card-title class="text-h6 font-weight-bold">Nova Reserva</v-card-title>
        <v-card-text>
          <v-form ref="formRef" v-model="valid" lazy-validation>
            <v-select
              v-model="form.turma_id"
              :items="turmas"
              item-title="nome"
              item-value="id"
              label="Turma"
              :rules="[v => !!v || 'Campo obrigatório']"
            />

            <v-select
              v-model="form.tipo_aula_id"
              :items="tipos"
              item-title="nome"
              item-value="id"
              label="Tipo de Aula"
              :rules="[v => !!v || 'Campo obrigatório']"
            />

          <v-select
  v-model="form.municao_id"
  :items="municoes"
  item-title="tipo"
  item-value="id"
  label="Munição"
/>

 

  


            <v-text-field v-model.number="form.alunos" type="number" label="Quantidade de Alunos" :rules="[v => v > 0 || 'Informe um valor válido']" />
            <v-text-field v-model.number="form.municoes_por_aluno" type="number" label="Munições por Aluno" :rules="[v => v > 0 || 'Informe um valor válido']" />

            <v-text-field :value="total" label="Total a reservar" readonly />
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="closeDialog">Cancelar</v-btn>
          <v-btn color="primary" @click="submitReserva">Enviar Solicitação</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Dialog devolução -->
    <v-dialog v-model="devolucaoDialog" persistent max-width="400px">
      <v-card>
        <v-card-title class="text-h6 font-weight-bold">Registrar Devolução</v-card-title>
        <v-card-text>
          <v-form ref="devFormRef" v-model="devValid" lazy-validation>
            <v-text-field v-model.number="devolucao.quantidade" type="number" label="Quantidade Devolvida" :rules="[v => v >= 0 || 'Informe um valor válido']" />
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="devolucaoDialog = false">Cancelar</v-btn>
          <v-btn color="primary" @click="registrarDevolucao">Registrar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'
import store from '@/store'

const reservas = ref([])
const municoes = ref([])
const turmas = ref([])
const tipos = ref([])
const loading = ref(false)
const dialog = ref(false)
const devolucaoDialog = ref(false)
const valid = ref(false)
const devValid = ref(false)
const formRef = ref(null)
const devFormRef = ref(null)
const selectedReserva = ref(null)

const form = ref({ turma_id: null, tipo_aula_id: null, municao_id: null, alunos: 1, municoes_por_aluno: 1 })
const devolucao = ref({ quantidade: 0 })

const headers = [
  { title: 'ID', key: 'id', align: 'start' },
  { title: 'Turma', key: 'turma.nome' },
  { title: 'Tipo Aula', key: 'tipoAula.nome' },
  { title: 'Munição', key: 'municao.tipo' },
  { title: 'Alunos', key: 'alunos' },
  { title: 'Munições/Aluno', key: 'municoes_por_aluno' },
  { title: 'Total', key: 'total_municoes' },
  { title: 'Status', key: 'status' },
  { title: 'Ações', key: 'acoes', sortable: false, align: 'center' },
]

onMounted(() => { fetchReservas(); fetchMunicoes(); fetchTurmas(); fetchTipos() })

const fetchReservas = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/reservas_municoes?mine=1')
    reservas.value = data
  } catch (err) { console.error('Erro ao carregar reservas', err) } finally { loading.value = false }
}

const fetchMunicoes = async () => {
  try { const { data } = await api.get('/municoes'); municoes.value = data } catch (err) { console.error('Erro ao carregar munições', err) }
}

const fetchTurmas = async () => {
  try { const { data } = await api.get('/turmas'); turmas.value = data } catch (err) { console.error('Erro ao carregar turmas', err) }
}

const fetchTipos = async () => {
  try { const { data } = await api.get('/tipo_aulas'); tipos.value = data } catch (err) { console.error('Erro ao carregar tipos de aula', err) }
}

const openDialog = () => { resetForm(); dialog.value = true }
const closeDialog = () => { dialog.value = false }

const total = computed(() => {
  return (Number(form.value.alunos || 0) * Number(form.value.municoes_por_aluno || 0))
})

const submitReserva = async () => {
  const ok = await formRef.value.validate()
  if (!ok) return
  try {
    const payload = { ...form.value, total_municoes: total.value }
    await api.post('/reservas_municoes', payload)
    await fetchReservas()
    dialog.value = false
  } catch (err) { console.error('Erro ao criar reserva', err.response?.data || err) }
}

const cancelarReserva = async (r) => {
  try { await api.post(`/reservas_municoes/${r.id}/cancel`); await fetchReservas() } catch (err) { console.error(err) }
}

const aprovarReserva = async (r) => {
  try { await api.post(`/reservas_municoes/${r.id}/approve`); await fetchReservas() } catch (err) { console.error(err) }
}

const openDevolucao = (r) => { selectedReserva.value = r; devolucao.value = { quantidade: 0 }; devolucaoDialog.value = true }

const registrarDevolucao = async () => {
  const ok = await devFormRef.value.validate()
  if (!ok) return
  try {
    await api.post(`/reservas_municoes/${selectedReserva.value.id}/devolucao`, { quantidade: devolucao.value.quantidade })
    devolucaoDialog.value = false
    await fetchReservas()
  } catch (err) { console.error('Erro ao registrar devolução', err) }
}

const resetForm = () => { form.value = { turma_id: null, tipo_aula_id: null, municao_id: null, alunos: 1, municoes_por_aluno: 1 } }

const isInstructor = computed(() => {
  const u = store.state.auth.user || {}
  return u.is_instrutor == 1 || u.is_instrutor === true || u.is_instrutor === '1'
})

const isApprover = computed(() => {
  const u = store.state.auth.user || {}
  return u.is_admin == 1 || u.is_admin === true || u.is_admin === '1'
})

</script>

<style scoped>
.tabela thead { background-color: #f2f2f2 }
</style>
