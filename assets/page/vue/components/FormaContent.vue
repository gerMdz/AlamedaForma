<script setup>
import HelloForma from "./HelloForma.vue";
import {store} from "../../assets/almacen";
import FormPersonalidades from "./FormPersonalidades.vue";

const responseData = store.responseData
</script>

<template>
  <HelloForma/>
  <div v-if="responseData">
    <FormPersonalidades/>
  </div>


</template>

<style scoped>

</style>