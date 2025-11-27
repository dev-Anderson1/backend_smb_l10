<template>
  <v-container class="pa-6">
    <v-row class="align-center justify-space-between mb-4">
      <h1 class="text-h5 font-weight-bold">Instrutores</h1>

      <v-btn color="primary" prepend-icon="mdi-plus" @click="openDialog">
        Tornar Instrutor
      </v-btn>
    </v-row>

    <v-data-table
      :headers="headers"
      :items="instrutores"
      :loading="loading"
      class="elevation-2"
      item-value="id"
      no-data-text="Nenhum instrutor encontrado"
    >
      <template #item.acoes="{ item }">
        <v-btn icon color="blue" @click="verSaldos(item.id)">
          <v-icon>mdi-eye</v-icon>
        </v-btn>

        <v-btn icon color="red" @click="removerInstrutor(item)">
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </template>
    </v-data-table>

    <!-- Dialog ativar instrutor -->
    <v-dialog v-model="dialog" persistent max-width="400px">
      <v-card>
        <v-card-title class="text-h6 font-weight-bold">
          Ativar Usuário como Instrutor
        </v-card-title>

        <v-card-text>
          <v-select
            v-model="userId"
            :items="usuarios"
            item-title="name"
            item-value="id"
            label="Selecione um usuário"
            :rules="[v => !!v || 'Campo obrigatório']"
          />
        </v-card-text>

        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="dialog = false">Cancelar</v-btn>
          <v-btn color="primary" @click="ativarInstrutor">Confirmar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import { useRouter } from "vue-router";

const router = useRouter();

const instrutores = ref([]);
const usuarios = ref([]);
const loading = ref(false);
const dialog = ref(false);
const userId = ref(null);

const headers = [
  { title: "ID", key: "id" },
  { title: "Nome", key: "name" },
  { title: "Email", key: "email" },
  { title: "Apelido", key: "apelido" },
  { title: "Ações", key: "acoes", sortable: false, align: "center" },
];

onMounted(() => {
  fetchInstrutores();
  fetchUsuarios();
});

const fetchInstrutores = async () => {
  loading.value = true;
  try {
    const { data } = await api.get("/instrutores");
    instrutores.value = data;
  } finally {
    loading.value = false;
  }
};

const fetchUsuarios = async () => {
  const { data } = await api.get("/users");
  usuarios.value = data;
};

const openDialog = () => (dialog.value = true);

const ativarInstrutor = async () => {
  await api.post("/instrutores", { user_id: userId.value });
  dialog.value = false;
  fetchInstrutores();
};

const removerInstrutor = async (item) => {
  await api.delete(`/instrutores/${item.id}`);
  fetchInstrutores();
};

const verSaldos = (id) => {
  router.push({ name: "InstrutorSaldos", params: { id } });
};
</script>
