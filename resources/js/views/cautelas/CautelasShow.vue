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
            {{ cautela?.devolvido_por?.apelido || "—" }}
          </v-col>

          <v-col cols="12" md="6">
            <strong>Status:</strong>
            <v-chip :color="statusColor" dark>
              {{ statusLabel }}
            </v-chip>
          </v-col>
          <v-col cols="12" md="6" v-if="showRecebidoPor">
            <strong>Furriel (recebeu):</strong>
            {{ cautela?.devolvido_por?.apelido || "—" }}
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
                <th style="width: 240px;">Recebimento</th>
                <th style="width: 140px;">Ações</th>
              </tr>
            </thead>

          <tbody>
            <tr v-for="item in cautela?.itens" :key="item.id">
              <td>{{ tipoItem(item) }}</td>
              <td>{{ descricaoItem(item) }}</td>
              <td>{{ item.quantidade }}</td>

              <td>
                <div v-if="itemDevolvido(item)" class="recebimento-coluna">
                  <div class="recebimento-linha">
                    <span class="recebimento-label">Furriel:</span>
                    <span class="recebimento-valor">{{ itemReceivedBy(item) || '—' }}</span>
                  </div>

                  <div class="recebimento-linha">
                    <span class="recebimento-label">Data:</span>
                    <span class="recebimento-valor">{{ itemReturnDate(item) || '—' }}</span>
                  </div>
                </div>

                <div v-else class="recebimento-vazio">—</div>
              </td>

              <td>
                <div class="acoes-coluna">
                  <v-btn
                    v-if="isAdmin"
                    :color="itemDevolvido(item) ? 'green' : 'red'"
                    size="small"
                    class="text-white"
                    :disabled="itemDevolvido(item)"
                    @click="devolverItem(item)"
                  >
                    {{ itemDevolvido(item) ? 'Devolvido' : 'Devolver' }}
                  </v-btn>

                  <v-chip
                    v-else
                    :color="itemDevolvido(item) ? 'green' : 'orange'"
                    small
                    class="ma-0"
                  >
                    {{ itemStatusLabel(item) }}
                  </v-chip>
                </div>
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

    allItemsDevolvidos() {
      const items = this.cautela?.itens ?? [];
      return items.length > 0 && items.every(item => this.itemDevolvido(item));
    },

    statusLabel() {
      if (!this.cautela) return "—";

      if (this.allItemsDevolvidos) {
        return "devolvido";
      }

      if (this.cautela.status === "atrasado") {
        return "atrasado";
      }

      if (this.cautela.status === "autorizada") {
        return "autorizada";
      }

      return "pendente";
    },

    devolvidoPorName() {
      return (
        this.cautela?.devolvidoPor?.apelido ||
        this.cautela?.devolvidoPor?.name ||
        this.cautela?.recebido_por ||
        null
      );
    },

      showRecebidoPor() {
        return !!(this.cautela?.recebido_por || this.cautela?.devolvidoPor);
      },

    statusColor() {
      switch (this.statusLabel) {
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

    devolverItem(item) {
      if (this.itemDevolvido(item)) return;

      api.post(`/cautelas/${this.cautela.id}/devolver-item`, { item_id: item.id })
        .then(res => {
          const updated = res.data.item;
          item.devolvido = true;
          item.status = "devolvido";
          if (updated.devolvido_em) {
            item.devolvido_em = updated.devolvido_em;
          }
          if (updated.devolvido_por) {
            item.devolvido_por = updated.devolvido_por;
          }
        })
        .catch(err => console.error(err));
    },

    itemDevolvido(item) {
      return !!item.devolvido || item.status === "devolvido";
    },

    itemStatusLabel(item) {
      return this.itemDevolvido(item) ? "Desacautelado" : "Cautelado";
    },

    itemReceivedBy(item) {
      return (
        item.devolvido_por?.apelido ||
        item.devolvido_por?.name ||
        this.devolvidoPorName ||
        null
      );
    },

    itemReturnDate(item) {
      if (!item.devolvido_em) {
        return null;
      }

      const parsed = new Date(item.devolvido_em);

      if (Number.isNaN(parsed.getTime())) {
        return item.devolvido_em;
      }

      return parsed.toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    },

    abrirModalDevolucao() {
      this.authDevolucaoDialog = true;
    },

    confirmarDevolucaoTodos() {
      api.post(`/cautelas/${this.cautela.id}/devolver-todos`, this.authDevolucao)
        .then(res => {
          this.authDevolucaoDialog = false;
          this.carregarCautela();
        })
        .catch(() => {
          alert("Erro na autenticação ou devolução.");
        });
    },

    carregarCautela() {
      api.get(`/cautelas/${this.$route.params.id}`).then(res => {
        this.cautela = res.data;
      });
    },
  },

  mounted() {
    this.carregarCautela();
  },
};
</script>

<style scoped>
.recebimento-coluna {
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 220px;
}

.recebimento-linha {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  line-height: 1.4;
}

.recebimento-label {
  font-weight: 600;
  color: #424242;
  min-width: 56px;
}

.recebimento-valor {
  color: #616161;
  word-break: break-word;
}

.recebimento-vazio {
  color: #9e9e9e;
}

.acoes-coluna {
  display: flex;
  align-items: center;
  min-height: 56px;
}
</style>
