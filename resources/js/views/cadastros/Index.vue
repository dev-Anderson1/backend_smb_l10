<template>
  <v-container>
    <h2 class="text-h5 mb-4">Cadastros</h2>

    <v-list rounded>

      <v-list-item @click="$router.push('/armas')">
        <v-list-item-title>Armas</v-list-item-title>
      </v-list-item>

      <v-list-item @click="$router.push('/coletes')">
        <v-list-item-title>Coletes</v-list-item-title>
      </v-list-item>

      <v-list-item @click="$router.push('/algemas')">
        <v-list-item-title>Algemas</v-list-item-title>
      </v-list-item>

      <v-list-item @click="$router.push('/espadas')">
        <v-list-item-title>Espadas</v-list-item-title>
      </v-list-item>

      <v-list-item @click="$router.push('/outros')">
        <v-list-item-title>Outros Materiais</v-list-item-title>
      </v-list-item>

      <v-list-item @click="$router.push('/municoes')">
        <v-list-item-title>Munições</v-list-item-title>
      </v-list-item>

      <v-divider class="my-3"></v-divider>

      <v-list-item @click="$router.push('/usuarios')">
        <v-list-item-title>Usuários</v-list-item-title>
      </v-list-item>

      <v-list-item @click="$router.push('/patentes')">
        <v-list-item-title>Patentes</v-list-item-title>
      </v-list-item>

      <v-list-item @click="$router.push('/unidades')">
        <v-list-item-title>Unidades</v-list-item-title>
      </v-list-item>

    </v-list>

  </v-container>
</template>

<script>
export default {
  name: 'CadastrosIndex'
}
</script>
