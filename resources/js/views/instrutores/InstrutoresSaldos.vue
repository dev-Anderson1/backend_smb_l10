<template>
  <v-container class="pa-6">
    <v-row class="align-center justify-space-between mb-4">
      <h1 class="text-h5 font-weight-bold">Saldos do Instrutor</h1>

      <v-btn color="primary" prepend-icon="mdi-plus" @click="abrirAdicionarSaldo">
        Adicionar Saldo
      </v-btn>
    </v-row>

    <v-data-table
      :headers="headers"
      :items="saldos"
      :loading="loading"
      class="elevation-2"
      no-data-text="Nenhum saldo registrado"
    >
      <template #item.municao="{ item }">
        {{ item.municao.tipo }}
      </template>
    </v-data-table>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const id = route.params.id;

const saldos = ref([]);
const loading = ref(false);

const headers = [
  { title: "ID", key: "id" },
  { title: "Tipo de Munição", key: "municao.tipo" },
  { title: "Quantidade", key: "quantidade" },
];

onMounted(fetchSaldos);

async function fetchSaldos() {
  loading.value = true;
  const { data } = await api.get(`/instrutores/${id}/saldos`);
  saldos.value = data;
  loading.value = false;
}

function abrirAdicionarSaldo() {
  router.push({ name: "InstrutorAddSaldo", params: { id } });
}
</script>
