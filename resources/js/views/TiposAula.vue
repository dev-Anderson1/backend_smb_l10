<template>
  <v-container class="pa-6">
    <v-row class="align-center justify-space-between mb-4">
      <h1 class="text-h5 font-weight-bold">Cadastro de Tipos de Aula</h1>

      <v-btn color="primary" prepend-icon="mdi-plus" @click="openCreateDialog">
        Novo Tipo de Aula
      </v-btn>
    </v-row>

    <v-data-table
      :headers="headers"
      :items="tiposAula"
      :loading="loading"
      class="elevation-2"
      no-data-text="Nenhum tipo de aula cadastrado"
    >
      <template #item.actions="{ item }">
        <v-btn icon color="primary" size="small" @click="openEditDialog(item)">
          <v-icon>mdi-pencil</v-icon>
        </v-btn>

        <v-btn icon color="red" size="small" @click="confirmDelete(item.id)">
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>

    <!-- DIALOG CADASTRAR -->
    <v-dialog v-model="dialogCreate" max-width="400">
      <v-card>
        <v-card-title class="text-h6">Cadastrar Tipo de Aula</v-card-title>

        <v-card-text>
          <v-text-field
            label="Nome do Tipo de Aula"
            v-model="form.nome"
            dense outlined
          />
        </v-card-text>

        <v-card-actions>
          <v-btn text @click="dialogCreate = false">Cancelar</v-btn>
          <v-btn color="primary" @click="salvarTipo">Salvar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- DIALOG EDITAR -->
    <v-dialog v-model="dialogEdit" max-width="400">
      <v-card>
        <v-card-title class="text-h6">Editar Tipo de Aula</v-card-title>

        <v-card-text>
          <v-text-field
            label="Nome do Tipo de Aula"
            v-model="editForm.nome"
            dense outlined
          />
        </v-card-text>

        <v-card-actions>
          <v-btn text @click="dialogEdit = false">Cancelar</v-btn>
          <v-btn color="primary" @click="atualizarTipo">Atualizar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- DIALOG DELETAR -->
    <v-dialog v-model="dialogDelete" max-width="380">
      <v-card>
        <v-card-title class="text-h6">Confirmação</v-card-title>

        <v-card-text>
          Tem certeza que deseja excluir este tipo de aula?
        </v-card-text>

        <v-card-actions>
          <v-btn text @click="dialogDelete = false">Cancelar</v-btn>
          <v-btn color="red" @click="deletarTipo">Excluir</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";

const tiposAula = ref([]);
const loading = ref(false);

// dialogs
const dialogCreate = ref(false);
const dialogEdit = ref(false);
const dialogDelete = ref(false);

// forms
const form = ref({ nome: "" });
const editForm = ref({ id: null, nome: "" });
const deleteId = ref(null);

const headers = [
  { title: "ID", key: "id" },
  { title: "Nome do Tipo de Aula", key: "nome" },
  { title: "Ações", key: "actions", sortable: false },
];

async function fetchTipos() {
  loading.value = true;
  const { data } = await api.get("/tipo_aulas");
  tiposAula.value = data;
  loading.value = false;
}

function openCreateDialog() {
  form.value = { nome: "" };
  dialogCreate.value = true;
}

async function salvarTipo() {
  await api.post("/tipo_aulas", form.value);
  dialogCreate.value = false;
  fetchTipos();
}

function openEditDialog(item) {
  editForm.value = { ...item };
  dialogEdit.value = true;
}

async function atualizarTipo() {
  await api.put(`/tipo_aulas/${editForm.value.id}`, editForm.value);
  dialogEdit.value = false;
  fetchTipos();
}

function confirmDelete(id) {
  deleteId.value = id;
  dialogDelete.value = true;
}

async function deletarTipo() {
  await api.delete(`/tipo_aulas/${deleteId.value}`);
  dialogDelete.value = false;
  fetchTipos();
}

onMounted(fetchTipos);
</script>
