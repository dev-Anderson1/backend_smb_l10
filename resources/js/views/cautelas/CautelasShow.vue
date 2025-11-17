<template>
  <v-container>
    <v-card>
      <v-toolbar flat>
        <v-toolbar-title class="text-h5">Cautela #{{ cautela?.id }}</v-toolbar-title>

        <v-spacer />

        <v-btn color="primary" class="text-white" @click="$router.push('/cautelas')">
          Voltar
        </v-btn>
      </v-toolbar>

      <v-card-text>
        <v-row>
          <v-col cols="12" md="6">
            <strong>Furriel (cautelou):</strong>
            {{ cautela?.admin?.apelido || "—" }}
          </v-col>

          <v-col cols="12" md="6">
            <strong>Autorizado por:</strong>
            {{ cautela?.user_confirm?.apelido || "—" }}
          </v-col>

          <v-col cols="12" md="6">
            <strong>Furriel (recebeu):</strong>
            {{ cautela?.recebido_por || "—" }}
          </v-col>

          <v-col cols="12" md="6">
            <strong>Status:</strong>
            <v-chip :color="statusColor" dark>
              {{ cautela?.status }}
            </v-chip>
          </v-col>
        </v-row>

        <v-divider class="my-4"></v-divider>

        <h3 class="mb-2">Itens</h3>

        <v-table>
          <thead>
            <tr>
              <th>Tipo</th>
              <th>Descrição</th>
              <th>Quantidade</th>
              <th style="width: 140px;">Ações</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="item in cautela?.itens" :key="item.id">
              <td>{{ tipoItem(item) }}</td>
              <td>{{ descricaoItem(item) }}</td>
              <td>{{ item.quantidade }}</td>
              <td>
                <v-btn
                  v-if="isAdmin"
                  color="red"
                  size="small"
                  class="text-white"
                  @click="removerItem(item.id)"
                >
                  Devolver
                </v-btn>
              </td>
            </tr>
          </tbody>
        </v-table>

        <v-btn
          v-if="isAdmin && cautela?.itens?.length"
          color="primary"
          class="mt-4 text-white"
          @click="abrirModalDevolucao"
        >
          Devolver Todos os Itens
        </v-btn>
      </v-card-text>
    </v-card>

    <!-- Modal de devolução -->
    <v-dialog v-model="authDevolucaoDialog" max-width="400">
      <v-card>
        <v-card-title class="text-h6">Confirmação de Devolução</v-card-title>
        <v-card-text>
          <p>Informe o e-mail e senha do <strong>administrador</strong> que está recebendo os materiais</p>

          <v-text-field v-model="authDevolucao.email" label="E-mail" type="email" required />
          <v-text-field v-model="authDevolucao.password" label="Senha" type="password" required />
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="authDevolucaoDialog = false">Cancelar</v-btn>
          <v-btn color="primary" text @click="confirmarDevolucaoTodos">Confirmar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import api from "@/services/api";

export default {
  name: "CautelasShow",

  data() {
    return {
      cautela: null,
      authDevolucaoDialog: false,
      authDevolucao: { email: "", password: "" },
    };
  },

  computed: {
    isAdmin() {
      return this.$store.state.auth.user?.is_admin == 1;
    },

    statusColor() {
      switch (this.cautela?.status) {
        case "pendente": return "orange";
        case "autorizada": return "blue";
        case "devolvido": return "green";
        case "atrasado": return "red";
        default: return "grey";
      }
    },
  },

  methods: {
    tipoItem(item) {
      if (item.arma) return "Arma";
      if (item.colete) return "Colete";
      if (item.algema) return "Algema";
      if (item.espada) return "Espada";
      if (item.outros_materiais) return "Outros";
      return "Material";
    },

    descricaoItem(item) {
      let partes = [];

      if (item.arma)
        partes.push(`${item.arma.modelo?.name || ""} - Série: ${item.arma.numero_serie}`);

      if (item.colete)
        partes.push(`${item.colete.tipo} - Nº ${item.colete.num_serie}`);

      if (item.algema)
        partes.push(`${item.algema.tipo} - Nº ${item.algema.num_serie}`);

      if (item.espada)
        partes.push(`${item.espada.tipo} - Nº ${item.espada.num_serie}`);

      if (item.outros_materiais)
        partes.push(item.outros_materiais);

      return partes.length ? partes.join(" | ") : "—";
    },

    removerItem(itemId) {
      api.post(`/cautelas/${this.cautela.id}/devolver-item`, { item_id: itemId })
        .then(() => {
          this.cautela.itens = this.cautela.itens.filter(i => i.id !== itemId);
        })
        .catch(err => console.error(err));
    },

    abrirModalDevolucao() {
      this.authDevolucaoDialog = true;
    },

  confirmarDevolucaoTodos() {
  api.post(`/cautelas/${this.cautela.id}/devolver-todos`, this.authDevolucao)
    .then(res => {
      this.authDevolucaoDialog = false;

      // Atualiza status localmente
      this.cautela.status = "devolvido";
      this.cautela.itens = [];

      // 🔥 Guardar quem recebeu a devolução
      this.cautela.recebido_por = res.data.recebido_por;
    })
    .catch(err => {
      alert("Erro na autenticação ou devolução.");
    });
}



  },
  mounted() {
    api.get(`/cautelas/${this.$route.params.id}`).then(res => {
      this.cautela = res.data;
    });
  },
};
</script>
