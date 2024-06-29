<template>
  <div class="vue-app">

    <Inicio />
    <Instructions />
    <FormaContent />


  </div>
</template>

<script setup>
import Inicio from "./components/Inicio.vue";
import Instructions from "./components/Instructions.vue";
import FormaContent from "./components/FormaContent.vue";

import { inject } from 'vue'
const responseData = inject('responseData');

</script>

<style>
.vue-app {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.logo {
  height: 10em;
  padding: 1.5em;
  will-change: filter;
  transition: filter 300ms;
}

.logo:hover {
  filter: drop-shadow(0 0 2em #646cffaa);
}

.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}
</style>