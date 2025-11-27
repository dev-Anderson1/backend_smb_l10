<template>
  <v-container class="pa-6" style="max-width: 500px;">
    <h1 class="text-h5 mb-4 font-weight-bold">Adicionar Saldo</h1>

    <v-form ref="formRef" v-model="valid">

      <v-select
        label="Instrutor"
        :items="instrutores"
        item-title="apelido"
        item-value="id"
        v-model="form.user_id"
        :rules="[v => !!v || 'Obrigatório']"
      />

      <v-select
        label="Munição"
        :items="municoes"
        item-title="tipo"
        item-value="id"
        v-model="form.municao_id"
        :rules="[v => !!v || 'Obrigatório']"
      />

      <v-select
        label="Turma"
        :items="turmas"
        item-title="nome"
        item-value="id"
        v-model="form.turma_id"
      />

      <v-select
        label="Tipo de Aula"
        :items="tiposAula"
        item-title="nome"
        item-value="id"
        v-model="form.tipo_aula_id"
      />

      <v-text-field
        label="Quantidade"
        type="number"
        v-model.number="form.quantidade"
        :rules="[v => v > 0 || 'Informe valor válido']"
      />

      <v-btn class="mt-4" color="primary" @click="salvar">
        Confirmar
      </v-btn>

    </v-form>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";

const instrutores = ref([]);
const municoes = ref([]);
const turmas = ref([]);
const tiposAula = ref([]);

const formRef = ref(null);
const valid = ref(false);

const form = ref({
  user_id: null,
  municao_id: null,
  turma_id: null,
  tipo_aula_id: null,
  quantidade: 1
});

onMounted(async () => {
  instrutores.value = (await api.get("/instrutores")).data;
  municoes.value = (await api.get("/municoes")).data;
  turmas.value = (await api.get("/turmas")).data;
  tiposAula.value = (await api.get("/tipo_aulas")).data;
});

async function salvar() {
  const ok = await formRef.value.validate();
  if (!ok) return;

  await api.post("/instrutores/saldos", form.value);

  alert("Saldo adicionado!");
}
</script>
