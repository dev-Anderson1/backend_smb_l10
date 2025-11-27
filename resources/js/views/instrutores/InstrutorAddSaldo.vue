<template>
  <v-container class="pa-6" style="max-width: 500px;">
    <h1 class="text-h5 mb-4 font-weight-bold">Adicionar Saldo ao Instrutor</h1>

    <v-form ref="formRef" v-model="valid">
      <v-select
        v-model="form.municao_id"
        :items="municoes"
        item-title="tipo"
        item-value="id"
        label="Tipo de Munição"
        :rules="[v => !!v || 'Obrigatório']"
      />

      <v-text-field
        v-model.number="form.quantidade"
        label="Quantidade"
        type="number"
        :rules="[v => v > 0 || 'Informe um valor válido']"
      />

      <v-btn class="mt-4" color="primary" @click="salvarSaldo">
        Confirmar
      </v-btn>
    </v-form>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const id = route.params.id;

const formRef = ref(null);
const valid = ref(false);

const form = ref({
  municao_id: null,
  quantidade: 1,
});

const municoes = ref([]);

onMounted(async () => {
  const { data } = await api.get("/municoes");
  municoes.value = data;
});

async function salvarSaldo() {
  const ok = await formRef.value.validate();
  if (!ok) return;

  await api.post(`/instrutores/${id}/saldos`, form.value);

  router.push({ name: "InstrutorSaldos", params: { id } });
}
</script>
