<script setup>
import HelloForma from "./HelloForma.vue";
import {store} from "../../assets/almacen";
import FormPersonalidades from "./FormPersonalidades.vue";
import Formation from "./Formation.vue";

const responseData = store.responseData
</script>

<template>
  <v-container fluid class="fill-height w-auto">
    <HelloForma/>
    <div v-if="responseData" class="text-center v-container v-container--fluid">
      <!--    <FormPersonalidades/>-->
      <Formation/>

    </div>
  </v-container>

</template>

<style scoped>

</style>