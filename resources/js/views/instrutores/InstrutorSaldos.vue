<template>
  <v-container class="pa-6">

    <v-row class="align-center justify-space-between mb-4">
      <h1 class="text-h5 font-weight-bold">Saldos do Instrutor</h1>

      <!-- BOTÃO APENAS PARA ADMIN -->
      <v-btn
        v-if="isAdmin"
        color="primary"
        prepend-icon="mdi-plus"
        @click="mostrarGlobal = true"
      >
        Adicionar Saldo
      </v-btn>
    </v-row>

    <!-- MODAL GLOBAL -->
    <v-dialog v-model="mostrarGlobal" max-width="600">
      <AdicionarSaldoGlobal @close="fecharGlobal" @saved="onSaldoAdicionado" />
    </v-dialog>

    <!-- TABELA -->
    <v-data-table
      :headers="headersFiltrados"
      :items="saldos"
      :loading="loading"
      class="elevation-2"
      no-data-text="Nenhum saldo encontrado"
    >
      <template #item.municao="{ item }">
        {{ item.municao?.tipo ?? '-' }}
      </template>

      <template #item.turma="{ item }">
        {{ item.turma?.nome ?? '-' }}
      </template>

      <template #item.tipo_aula="{ item }">
        {{ item.tipo_aulas?.nome ?? '-' }}
      </template>

      <!-- AÇÕES APENAS PARA ADMIN -->
      <template #item.acoes="{ item }" v-if="isAdmin">
        <v-btn icon @click="abrirEditar(item)">
          <v-icon>mdi-pencil</v-icon>
        </v-btn>

        <v-btn icon @click="excluirSaldo(item.id)" color="red">
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>

    <!-- MODAL EDITAR (só admin usa) -->
    <v-dialog v-model="abrirEditarDialog" max-width="500">
      <v-card>
        <v-card-title>Editar Saldo</v-card-title>

        <v-card-text>
          <v-select
            label="Munição"
            :items="municoes"
            item-title="tipo"
            item-value="id"
            v-model="editForm.municao_id"
          />

          <v-select
            label="Turma"
            :items="turmas"
            item-title="nome"
            item-value="id"
            v-model="editForm.turma_id"
          />

          <v-select
            label="Tipo de Aula"
            :items="tiposAula"
            item-title="nome"
            item-value="id"
            v-model="editForm.tipo_aula_id"
          />

          <v-text-field
            v-model="editForm.quantidade"
            type="number"
            label="Quantidade"
          />
        </v-card-text>

        <v-card-actions>
          <v-btn text @click="abrirEditarDialog = false">Cancelar</v-btn>
          <v-btn color="primary" @click="atualizarSaldo">Salvar</v-btn>
        </v-card-actions>

      </v-card>
    </v-dialog>

  </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import api from "@/services/api";
import { useRoute } from "vue-router";
import { useStore } from "vuex";

// IMPORTAR O MODAL GLOBAL
import AdicionarSaldoGlobal from "./AdicionarSaldoGlobal.vue";

// store (pegar user e role)
const store = useStore();
const isAdmin = computed(() => store.state.auth.user?.is_admin == 1);

// route
const route = useRoute();
const id = route.params.id;

// dados
const saldos = ref([]);
const loading = ref(false);

const municoes = ref([]);
const turmas = ref([]);
const tiposAula = ref([]);

// controle modal edição
const abrirEditarDialog = ref(false);
const editForm = ref({});
const editId = ref(null);

// controle modal GLOBAL
const mostrarGlobal = ref(false);

// para fechar modal global
function fecharGlobal() {
  mostrarGlobal.value = false;
}

// recarregar tabela após adicionar globalmente
function onSaldoAdicionado() {
  mostrarGlobal.value = false;
  fetchSaldos();
}

// HEADERS COMPLETOS
const headers = [
  { title: "ID", key: "id" },
  { title: "Tipo de Munição", key: "municao" },
  { title: "Turma", key: "turma" },
  { title: "Tipo de Aula", key: "tipo_aulas" },
  { title: "Quantidade", key: "quantidade" },
  { title: "Ações", key: "acoes", sortable: false },
];

// HEADERS PARA USER (remove ações)
const headersFiltrados = computed(() => {
  if (isAdmin.value) return headers;
  return headers.filter(h => h.key !== "acoes");
});

// =============================
// FUNÇÕES
// =============================
async function fetchSaldos() {
  loading.value = true;
  const { data } = await api.get(`/instrutores/${id}/saldos`);
  saldos.value = data;
  loading.value = false;
}

function abrirEditar(item) {
  editId.value = item.id;
  editForm.value = {
    municao_id: item.municao_id,
    turma_id: item.turma_id,
    tipo_aula_id: item.tipo_aula_id,
    quantidade: item.quantidade,
  };
  abrirEditarDialog.value = true;
}

async function atualizarSaldo() {
  await api.put(`/instrutores/saldos/${editId.value}`, editForm.value);
  abrirEditarDialog.value = false;
  fetchSaldos();
}

async function excluirSaldo(idSaldo) {
  await api.delete(`/instrutores/saldos/${idSaldo}`);
  fetchSaldos();
}

// =============================
// MOUNT
// =============================
onMounted(async () => {
  fetchSaldos();

  municoes.value = (await api.get("/municoes")).data;
  turmas.value = (await api.get("/turmas")).data;
  tiposAula.value = (await api.get("/tipo_aulas")).data;
});
</script>
