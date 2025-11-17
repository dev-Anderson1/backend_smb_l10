<template>
  <v-container>
    <v-card>
      <v-toolbar flat>
        <v-toolbar-title class="text-h5">Criar Cautela</v-toolbar-title>
      </v-toolbar>

      <v-card-text>
        <!-- Seleção de Usuário -->
        <v-row>
          <v-col cols="12" md="6">
            <v-select
              v-model="user_id"
              :items="users"
              item-title="apelido"
              item-value="id"
              label="Usuário"
              required
            />
          </v-col>
        </v-row>

        <!-- Armas -->
        <h3 class="mt-6">Armas</h3>
        <v-btn size="small" color="secondary" @click="addArma">+ Adicionar Arma</v-btn>

        <v-row v-for="(a, index) in armasSelecionadas" :key="'a'+index" class="mt-2">
          <v-col cols="6">
            <v-select
              v-model="a.arma_id"
              :items="armas"
              item-title="numero_serie"
              item-value="id"
              label="Selecione a arma"
            />
          </v-col>
          <v-col cols="3">
            <v-text-field
              v-model="a.quantidade"
              label="Qtd"
              type="number"
              min="1"
            />
          </v-col>
          <v-col cols="3">
            <v-btn color="error" @click="armasSelecionadas.splice(index, 1)">
              Remover
            </v-btn>
          </v-col>
        </v-row>

        <!-- Coletes -->
        <h3 class="mt-6">Coletes</h3>
        <v-btn size="small" color="secondary" @click="addColete">+ Adicionar Colete</v-btn>

        <v-row v-for="(c, index) in coletesSelecionados" :key="'c'+index" class="mt-2">
          <v-col cols="6">
            <v-select
              v-model="c.colete_id"
              :items="coletes"
              item-title="num_serie"
              item-value="id"
              label="Selecione o colete"
            />
          </v-col>
          <v-col cols="3">
            <v-text-field
              v-model="c.quantidade"
              label="Qtd"
              type="number"
              min="1"
            />
          </v-col>
          <v-col cols="3">
            <v-btn color="error" @click="coletesSelecionados.splice(index,1)">
              Remover
            </v-btn>
          </v-col>
        </v-row>

        <!-- Algemas -->
        <h3 class="mt-6">Algemas</h3>
        <v-btn size="small" color="secondary" @click="addAlgema">+ Adicionar Algema</v-btn>

        <v-row v-for="(ag, index) in algemasSelecionadas" :key="'ag'+index" class="mt-2">
          <v-col cols="6">
            <v-select
              v-model="ag.algema_id"
              :items="algemas"
              item-title="num_serie"
              item-value="id"
              label="Selecione a algema"
            />
          </v-col>
          <v-col cols="3">
            <v-text-field
              v-model="ag.quantidade"
              label="Qtd"
              type="number"
              min="1"
            />
          </v-col>
          <v-col cols="3">
            <v-btn color="error" @click="algemasSelecionadas.splice(index,1)">
              Remover
            </v-btn>
          </v-col>
        </v-row>

        <!-- Espadas -->
        <h3 class="mt-6">Espadas</h3>
        <v-btn size="small" color="secondary" @click="addEspada">+ Adicionar Espada</v-btn>

        <v-row v-for="(es, index) in espadasSelecionadas" :key="'es'+index" class="mt-2">
          <v-col cols="6">
            <v-select
              v-model="es.espada_id"
              :items="espadas"
              item-title="num_serie"
              item-value="id"
              label="Selecione a espada"
            />
          </v-col>
          <v-col cols="3">
            <v-text-field
              v-model="es.quantidade"
              label="Qtd"
              type="number"
              min="1"
            />
          </v-col>
          <v-col cols="3">
            <v-btn color="error" @click="espadasSelecionadas.splice(index,1)">
              Remover
            </v-btn>
          </v-col>
        </v-row>

        <!-- Outros Materiais -->
        <h3 class="mt-6">Outros Materiais</h3>
        <v-btn size="small" color="secondary" @click="addOutro">+ Adicionar Material</v-btn>

        <v-row v-for="(o, index) in outrosSelecionados" :key="'o'+index" class="mt-2">
          <v-col cols="6">
            <v-text-field
              v-model="o.outros_materiais"
              label="Descrição do material"
            />
          </v-col>
          <v-col cols="3">
            <v-text-field
              v-model="o.quantidade"
              label="Qtd"
              type="number"
              min="1"
            />
          </v-col>
          <v-col cols="3">
            <v-btn color="error" @click="outrosSelecionados.splice(index,1)">
              Remover
            </v-btn>
          </v-col>
        </v-row>
      </v-card-text>

      <v-card-actions>
        <v-btn color="primary" @click="abrirAutenticacao">Salvar Cautela</v-btn>
      </v-card-actions>
    </v-card>

    <!-- Modal de Autorização -->
    <v-dialog v-model="authDialog" max-width="400">
      <v-card>
        <v-card-title class="text-h6">Autorização do Usuário</v-card-title>
        <v-card-text>
          <v-text-field v-model="auth.email" label="E-mail do usuário" type="email" required />
          <v-text-field v-model="auth.password" label="Senha do usuário" type="password" required />
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text @click="authDialog = false">Cancelar</v-btn>
          <v-btn color="primary" text @click="confirmarAuth">Confirmar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script>
import axios from "axios";
import api from "@/services/api";

export default {
  name: "CautelasCreate",

  data() {
    return {
      user_id: "",
      users: [],
      armas: [],
      coletes: [],
      algemas: [],
      espadas: [],
      armasSelecionadas: [],
      coletesSelecionados: [],
      algemasSelecionadas: [],
      espadasSelecionadas: [],
      outrosSelecionados: [],
      authDialog: false,
      auth: { email: "", password: "" },
    };
  },

 mounted() {
  api.get("/users").then(r => (this.users = r.data));
  api.get("/armas").then(r => (this.armas = r.data));
  api.get("/coletes").then(r => (this.coletes = r.data));
  api.get("/algemas").then(r => (this.algemas = r.data));
  api.get("/espadas").then(r => (this.espadas = r.data));
},

  methods: {
    addArma() { this.armasSelecionadas.push({ arma_id: "", quantidade: 1 }); },
    addColete() { this.coletesSelecionados.push({ colete_id: "", quantidade: 1 }); },
    addAlgema() { this.algemasSelecionadas.push({ algema_id: "", quantidade: 1 }); },
    addEspada() { this.espadasSelecionadas.push({ espada_id: "", quantidade: 1 }); },
    addOutro() { this.outrosSelecionados.push({ outros_materiais: "", quantidade: 1 }); },

    abrirAutenticacao() {
      if (!this.user_id) {
        alert("Selecione um usuário antes de salvar.");
        return;
      }
      this.authDialog = true;
    },

  confirmarAuth() {
  this.authDialog = false;

  const payload = {
    admin_id: this.$store.state.auth.user.id,
    user_id: this.user_id,
    email: this.auth.email,
    password: this.auth.password,
    itens: {
      armas: this.armasSelecionadas,
      coletes: this.coletesSelecionados,
      algemas: this.algemasSelecionadas,
      espadas: this.espadasSelecionadas,
      outros: this.outrosSelecionados,
    },
  };

     api.post("/cautela/store", payload)
    .then(res => {
      alert("Cautela criada com sucesso!");
      this.$router.push(`/cautelas/${res.data.cautela_id}`);
    })
    .catch(err => {
      console.error(err);
      alert("Erro ao autenticar ou salvar a cautela.");
    });
}
  },
};
</script>
