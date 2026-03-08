<template>
  <v-container fluid class="pa-4">
    <div class="cadastros-header mb-6">
      <div>
        <h2 class="text-h5 font-weight-bold mb-0">Cadastros</h2>
       
      </div>
    </div>

    <div v-for="group in groups" :key="group.title" class="cadastro-section mb-8">
      <div class="d-flex align-center justify-space-between mb-4">
        <div>
          <p class="text-subtitle-1 font-weight-medium mb-1">{{ group.title }}</p>
          <p class="caption text--secondary mb-0">{{ group.description }}</p>
        </div>
      </div>

     <v-row class="cadastro-row">
  <v-col
    v-for="card in group.cards"
    :key="card.title"
    cols="12"
    sm="6"
    md="4"
    lg="3"
    xl="3"
    class="cadastro-col"
  >
    <v-card
      class="cadastro-card"
      elevation="2"
      outlined
      @click="navigate(card.route)"
      role="button"
      :aria-label="`Abrir cadastro de ${card.title}`"
    >
      <div class="cadastro-card__icon" :style="{ backgroundImage: card.gradient }">
        <v-icon size="22" color="white">{{ card.icon }}</v-icon>
      </div>

      <div class="cadastro-card__content">
        <div class="cadastro-card__title">{{ card.title }}</div>
        <p class="cadastro-card__subtitle">{{ card.subtitle }}</p>
      </div>

      <v-card-actions class="cadastro-card__actions">
        <span class="cadastro-card__link">Abrir</span>
        <v-icon color="primary" size="22">mdi-arrow-right</v-icon>
      </v-card-actions>
    </v-card>
  </v-col>
</v-row>
    </div>
  </v-container>
</template>

<script>
export default {
  name: 'CadastrosIndex',
  data() {
    return {
      groups: [
        {
        //   title: 'Operacional',
        //   description: 'Equipamentos e materiais usados diariamente no treinamento.',
          cards: [
            {
              title: 'Turmas',
              subtitle: 'Organize aulas, instrutores e alunos.',
              route: '/turmas',
              icon: 'mdi-account-group',
              gradient: 'linear-gradient(135deg, #1E88E5, #42A5F5)'
            },
            {
              title: 'Tipos de Aula',
              subtitle: 'Classifique formatos e durações.',
              route: '/tipos_aula',
              icon: 'mdi-book-open-variant',
              gradient: 'linear-gradient(135deg, #7E57C2, #9575CD)'
            },
            {
              title: 'Calibres',
              subtitle: 'Mantenha o catálogo de dimensões.',
              route: '/calibres',
              icon: 'mdi-target',
              gradient: 'linear-gradient(135deg, #43A047, #66BB6A)'
            },
            {
              title: 'Armas',
              subtitle: 'Cadastre cada modelo e histórico de uso.',
              route: '/armas',
              icon: 'mdi-gun',
              gradient: 'linear-gradient(135deg, #EF5350, #F44336)'
            },
            {
              title: 'Coletes',
              subtitle: 'Controle tipos, tamanhos e proteções.',
              route: '/coletes',
              icon: 'mdi-shield-lock',
              gradient: 'linear-gradient(135deg, #FFB300, #FFC107)'
            },
            {
              title: 'Espadas',
              subtitle: 'Itens cerimoniais e de instrução.',
              route: '/espadas',
              icon: 'mdi-sword-cross',
              gradient: 'linear-gradient(135deg, #00897B, #26A69A)'
            },
            {
              title: 'Algemas',
              subtitle: 'Padronize os acessórios de contenção.',
              route: '/algemas',
              icon: 'mdi-handcuffs',
              gradient: 'linear-gradient(135deg, #6D4C41, #8D6E63)'
            },
            {
              title: 'Outros Materiais',
              subtitle: 'Registre ferramentas e insumos especiais.',
              route: '/outros',
              icon: 'mdi-hammer-wrench',
              gradient: 'linear-gradient(135deg, #FF7043, #FF8A65)'
            },
            {
              title: 'Munições',
              subtitle: 'Itens, lotes e quantidades disponíveis.',
              route: '/municoes',
              icon: 'mdi-bullet',
              gradient: 'linear-gradient(135deg, #5C6BC0, #7986CB)'
            }
          ]
        },
        {
          title: 'Pessoas & Unidades',
          description: 'Perfis, patentes e localidades administrativas.',
          cards: [
            {
              title: 'Usuários',
              subtitle: 'Gestiona acessos e responsabilidades.',
              route: '/usuarios',
              icon: 'mdi-account-cog',
              gradient: 'linear-gradient(135deg, #546E7A, #78909C)'
            },
            {
              title: 'Patentes',
              subtitle: 'Hierarquias e funções oficiais.',
              route: '/posto_graduacoes',
              icon: 'mdi-medal',
              gradient: 'linear-gradient(135deg, #FDD835, #FFE082)'
            },
            {
              title: 'Unidades',
              subtitle: 'Organização das OPMs e delegacias.',
              route: '/opms',
              icon: 'mdi-domain',
              gradient: 'linear-gradient(135deg, #3F51B5, #5C6BC0)'
            }
          ]
        }
      ]
    }
  },
  methods: {
    navigate(route) {
      this.$router.push(route)
    }
  }
}
</script>

<style scoped>
.cadastro-section:last-child {
  margin-bottom: 0;
}

.cadastro-row {
  margin-top: 4px;
}

.cadastro-col {
  display: flex;
}

.cadastro-card {
  cursor: pointer;
  width: 100%;
  min-height: 185px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 18px;
  border-radius: 18px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  background: #fff;
}

.cadastro-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
}

.cadastro-card__icon {
  width: 58px;
  height: 58px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 14px;
  background-size: cover;
  background-position: center;
  flex-shrink: 0;
}

.cadastro-card__content {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.cadastro-card__title {
  font-size: 1.05rem;
  font-weight: 600;
  line-height: 1.25;
  margin-bottom: 8px;
  color: #1f2937;
}

.cadastro-card__subtitle {
  font-size: 0.92rem;
  line-height: 1.45;
  color: #616161;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 2.7em;
}

.cadastro-card__actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0;
  margin-top: 14px;
}

.cadastro-card__link {
  font-size: 0.95rem;
  color: #424242;
}

@media (max-width: 960px) {
  .cadastro-card {
    min-height: 170px;
    padding: 16px;
  }

  .cadastro-card__icon {
    width: 52px;
    height: 52px;
    margin-bottom: 12px;
  }

  .cadastro-card__title {
    font-size: 1rem;
  }

  .cadastro-card__subtitle {
    font-size: 0.88rem;
  }
}

@media (max-width: 600px) {
  .cadastro-card {
    min-height: 160px;
    padding: 14px;
    border-radius: 16px;
  }

  .cadastro-card__icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
  }

  .cadastro-card__actions {
    margin-top: 12px;
  }
}
</style>