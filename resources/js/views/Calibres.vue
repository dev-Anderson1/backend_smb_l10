<template>
  <v-container class="pa-6">

    <v-row class="align-center justify-space-between mb-4">
      <h1 class="text-h5 font-weight-bold">Cadastro de Calibres</h1>

      <v-btn color="primary" prepend-icon="mdi-plus" @click="abrirDialogCriar = true">
        Novo Calibre
      </v-btn>
    </v-row>

    <!-- TABELA -->
    <v-data-table
      :headers="headers"
      :items="calibres"
      :loading="loading"
      class="elevation-2"
      no-data-text="Nenhum calibre cadastrado"
    >

      <template #item.acoes="{ item }">
        <v-btn icon @click="abrirEdicao(item)">
          <v-icon>mdi-pencil</v-icon>
        </v-btn>

        <v-btn icon color="red" @click="excluir(item.id)">
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </template>

    </v-data-table>

    <!-- MODAL CRIAR -->
    <v-dialog v-model="abrirDialogCriar" max-width="450">
      <v-card>
        <v-card-title>Novo Calibre</v-card-title>

        <v-card-text>
          <v-text-field
            label="Nome do Calibre (ex: 9)"
            v-model="form.nome"
            outlined
          />

          <v-text-field
            label="Medidas (ex: mm)"
            v-model="form.medidas"
            outlined
          />
        </v-card-text>

        <v-card-actions>
          <v-btn text @click="abrirDialogCriar = false">Cancelar</v-btn>
          <v-btn color="primary" @click="salvar">Salvar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- MODAL EDITAR -->
    <v-dialog v-model="abrirDialogEditar" max-width="450">
      <v-card>
        <v-card-title>Editar Calibre</v-card-title>

        <v-card-text>
          <v-text-field
            label="Nome do Calibre"
            v-model="editForm.nome"
            outlined
          />

          <v-text-field
            label="Medidas"
            v-model="editForm.medidas"
            outlined
          />
        </v-card-text>

        <v-card-actions>
          <v-btn text @click="abrirDialogEditar = false">Cancelar</v-btn>
          <v-btn color="primary" @click="atualizar">Salvar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";

const calibres = ref([]);
const loading = ref(false);

const abrirDialogCriar = ref(false);
const abrirDialogEditar = ref(false);

const form = ref({
  nome: "",
  medidas: "",
});

const editForm = ref({});
const editId = ref(null);

const headers = [
  { title: "ID", key: "id" },
  { title: "Nome", key: "nome" },
  { title: "Medidas", key: "medidas" },
  { title: "Ações", key: "acoes", sortable: false },
];

async function fetchCalibres() {
  loading.value = true;
  const { data } = await api.get("/calibres");
  calibres.value = data;
  loading.value = false;
}

async function salvar() {
  await api.post("/calibres", form.value);
  abrirDialogCriar.value = false;
  form.value = { nome: "", medidas: "" };
  fetchCalibres();
}

function abrirEdicao(item) {
  editId.value = item.id;
  editForm.value = { nome: item.nome, medidas: item.medidas };
  abrirDialogEditar.value = true;
}

async function atualizar() {
  await api.put(`/calibres/${editId.value}`, editForm.value);
  abrirDialogEditar.value = false;
  fetchCalibres();
}

async function excluir(id) {
  await api.delete(`/calibres/${id}`);
  fetchCalibres();
}

onMounted(fetchCalibres);
</script>
