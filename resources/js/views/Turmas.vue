<template>
  <v-container class="pa-6">
    <v-row class="align-center justify-space-between mb-4">
      <h1 class="text-h5 font-weight-bold">Cadastro de Turmas</h1>

      <v-btn color="primary" prepend-icon="mdi-plus" @click="openCreateDialog">
        Nova Turma
      </v-btn>
    </v-row>

    <v-data-table
      :headers="headers"
      :items="turmas"
      :loading="loading"
      class="elevation-2"
      no-data-text="Nenhuma turma cadastrada"
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

    <!-- DIALOG DE CRIAÇÃO -->
    <v-dialog v-model="dialogCreate" max-width="400">
      <v-card>
        <v-card-title class="text-h6">Cadastrar Turma</v-card-title>

        <v-card-text>
          <v-text-field label="Tipo do Curso" v-model="form.tipo_curso" dense outlined />
          <v-text-field label="Nome da Turma" v-model="form.nome" dense outlined />
        </v-card-text>

        <v-card-actions>
          <v-btn text @click="dialogCreate = false">Cancelar</v-btn>
          <v-btn color="primary" @click="salvarTurma">Salvar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- DIALOG DE EDIÇÃO -->
    <v-dialog v-model="dialogEdit" max-width="400">
      <v-card>
        <v-card-title class="text-h6">Editar Turma</v-card-title>

        <v-card-text>
          <v-text-field label="Tipo do Curso" v-model="editForm.tipo_curso" dense outlined />
          <v-text-field label="Nome da Turma" v-model="editForm.nome" dense outlined />
        </v-card-text>

        <v-card-actions>
          <v-btn text @click="dialogEdit = false">Cancelar</v-btn>
          <v-btn color="primary" @click="atualizarTurma">Atualizar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- DIALOG CONFIRMAR EXCLUSÃO -->
    <v-dialog v-model="dialogDelete" max-width="380">
      <v-card>
        <v-card-title class="text-h6">Confirmar Exclusão</v-card-title>

        <v-card-text>
          Tem certeza que deseja excluir esta turma?
        </v-card-text>

        <v-card-actions>
          <v-btn text @click="dialogDelete = false">Cancelar</v-btn>
          <v-btn color="red" @click="deletarTurma">Excluir</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";

const turmas = ref([]);
const loading = ref(false);

// dialogs
const dialogCreate = ref(false);
const dialogEdit = ref(false);
const dialogDelete = ref(false);

// forms
const form = ref({ nome: "", tipo_curso: "" });
const editForm = ref({ id: null, nome: "", tipo_curso: "" });
const deleteId = ref(null);

const headers = [
  { title: "ID", key: "id" },
  { title: "Curso", key: "tipo_curso" },
  { title: "Nome da Turma", key: "nome" },
  { title: "Ações", key: "actions", sortable: false },
];

async function fetchTurmas() {
  loading.value = true;
  const { data } = await api.get("/turmas");
  turmas.value = data;
  loading.value = false;
}

function openCreateDialog() {
  form.value = { nome: "", tipo_curso: "" };
  dialogCreate.value = true;
}

async function salvarTurma() {
  await api.post("/turmas", form.value);
  dialogCreate.value = false;
  fetchTurmas();
}

function openEditDialog(item) {
  editForm.value = { ...item };
  dialogEdit.value = true;
}

async function atualizarTurma() {
  await api.put(`/turmas/${editForm.value.id}`, editForm.value);
  dialogEdit.value = false;
  fetchTurmas();
}

function confirmDelete(id) {
  deleteId.value = id;
  dialogDelete.value = true;
}

async function deletarTurma() {
  await api.delete(`/turmas/${deleteId.value}`);
  dialogDelete.value = false;
  fetchTurmas();
}

onMounted(fetchTurmas);
</script>
