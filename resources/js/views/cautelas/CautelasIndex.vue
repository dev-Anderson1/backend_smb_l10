<template>
  <v-container>
    <v-card>
      <v-toolbar color="white" flat>
        <v-toolbar-title class="text-h5">Cautelas</v-toolbar-title>

        <v-spacer></v-spacer>

      <v-btn 
        v-if="isAdmin"
        color="primary" 
        variant="elevated" 
        class="text-white" 
        prepend-icon="mdi-plus"
        @click="novaCautela"
      >
        Nova Cautela
      </v-btn>

      </v-toolbar>

      <v-data-table
        :headers="headers"
        :items="cautelas"
        :items-per-page="10"
        class="elevation-1"
      >

        <!-- Usuário -->
        <template v-slot:item.usuario="{ item }">
          {{ item.usuario?.apelido || item.usuario?.name || "—" }}
        </template>

        <!-- Status Colorido -->
        <template v-slot:item.status="{ item }">
          <v-chip :color="chipColor(item.status)" dark>
            {{ item.status }}
          </v-chip>
        </template>

        <!-- Data formatada -->
        <template v-slot:item.created_at="{ item }">
          {{ formatDate(item.created_at) }}
        </template>

        <!-- Botões -->
        <template v-slot:item.actions="{ item }">
          <v-btn icon size="small" @click="ver(item.id)">
            <v-icon color="blue">mdi-eye</v-icon>
          </v-btn>
        </template>

      </v-data-table>
    </v-card>
  </v-container>
</template>


<script>
import axios from "axios";
import api from "@/services/api";

export default {
  name: "CautelasIndex",

  data() {
    return {
      cautelas: [],
      headers: [
        { title: "ID", key: "id", sortable: true },
        { title: "Usuário", key: "usuario" },
        { title: "Status", key: "status" },
        { title: "Criada", key: "created_at" },
        { title: "Ações", key: "actions", sortable: false },
      ],
    };
  },

  mounted() {
    api.get("/cautelas").then((r) => {
      this.cautelas = r.data;
    });
  },

  computed: {
  isAdmin() {
  return this.$store.state.auth.user?.is_admin == 1;
}

},


  methods: {
    novaCautela() {
      this.$router.push({ name: "CautelasCreate" });
    },

    ver(id) {
      this.$router.push({ name: "CautelasShow", params: { id } });
    },

   formatDate(date) {
  if (!date) return "—";

  // Se vier no formato BRA -> 11/11/2025 18:47
  if (date.includes("/")) {
    const [dia, mes, anoHora] = date.split("/");
    const [ano, hora] = anoHora.split(" ");
    return `${dia}/${mes}/${ano} ${hora}`;
  }

  // Se vier no formato MySQL -> 2025-11-12 02:23:53
  if (date.includes(" ")) {
    date = date.replace(" ", "T"); // vira 2025-11-12T02:23:53
  }

  const d = new Date(date);
  if (isNaN(d)) return "—";

  return d.toLocaleString("pt-BR", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
},


    chipColor(status) {
      switch (status) {
        case "pendente": return "orange";
        case "atrasado": return "red";
        case "autorizada": return "blue";
        case "devolvido": return "green";
        default: return "grey";
      }
    },
  },
};
</script>

