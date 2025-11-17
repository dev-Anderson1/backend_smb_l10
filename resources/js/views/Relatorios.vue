<template>
  <v-container>
    <v-card>
      <v-toolbar flat>
        <v-toolbar-title>Relatório Diário</v-toolbar-title>
      </v-toolbar>

      <v-card-text>
        <!-- Cabeçalho -->
        <v-text-field v-model="instituicao" label="Instituição" />
        <v-text-field v-model="data" type="date" label="Data" />

        <!-- Cargos principais -->
        <v-select
          v-model="oficial_dia_id"
          :items="usuarios"
          item-title="apelido"
          item-value="id"
          label="Oficial de Dia"
        />
        <v-select
          v-model="respondente_id"
          :items="usuarios"
          item-title="apelido"
          item-value="id"
          label="Respondente"
        />
        <v-select
          v-model="adjunto_id"
          :items="usuarios"
          item-title="apelido"
          item-value="id"
          label="Adjunto"
        />
        <v-select
          v-model="dia_smb_id"
          :items="usuarios"
          item-title="apelido"
          item-value="id"
          label="Dia SMB Responsável"
        />

        <!-- Campo de acontecimentos -->
        <v-textarea
          v-model="acontecimentos"
          label="Acontecimentos do dia"
          rows="6"
        />

        <!-- Troca de serviço -->
        <h3 class="mt-6">Troca de Serviço (Dia SMB)</h3>
        <v-row>
          <v-col cols="6">
            <v-select
              v-model="dia_smb_entrada_id"
              :items="usuarios"
              item-title="apelido"
              item-value="id"
              label="Dia SMB que entra"
            />
          </v-col>

          <v-col cols="6">
            <v-select
              v-model="dia_smb_saida_id"
              :items="usuarios"
              item-title="apelido"
              item-value="id"
              label="Dia SMB que sai"
            />
          </v-col>
        </v-row>

        <!-- Botão de ação -->
        <v-btn color="primary" class="mt-4" @click="salvarRelatorio">
          Salvar Relatório
        </v-btn>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script>
import axios from "axios";
import api from "@/services/api";

export default {
  name: "RelatorioDiarioCreate",
  data() {
    return {
      instituicao: "",
      data: new Date().toISOString().substring(0, 10),
      oficial_dia_id: null,
      respondente_id: null,
      adjunto_id: null,
      dia_smb_id: null,
      dia_smb_entrada_id: null,
      dia_smb_saida_id: null,
      acontecimentos: "",
      usuarios: [],
    };
  },
  mounted() {
    api.get("/users").then((res) => (this.usuarios = res.data));
  },
  methods: {
    salvarRelatorio() {
      const payload = {
        data: this.data,
        instituicao: this.instituicao,
        oficial_dia_id: this.oficial_dia_id,
        respondente_id: this.respondente_id,
        adjunto_id: this.adjunto_id,
        dia_smb_id: this.dia_smb_id,
        dia_smb_entrada_id: this.dia_smb_entrada_id,
        dia_smb_saida_id: this.dia_smb_saida_id,

        acontecimentos: this.acontecimentos,
      };

      api
        .post("/relatorios_diarios", payload)
        .then(() => alert("✅ Relatório salvo com sucesso!"))
        .catch((err) => {
          console.error(err);
          alert("❌ Erro ao salvar relatório: " + (err.response?.data?.message || 'Erro desconhecido'));
        });
    },
  },
};
</script>
