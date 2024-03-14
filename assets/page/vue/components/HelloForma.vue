<template>
  <v-app>
    <v-btn color="primary">Botón</v-btn>

    <h1>{{ msg }}</h1>
    <VBtn color="primary" @click="incrementCount">{{ countMessage }}</VBtn>
    <p>
      <code>assets/page/vue/components/HelloForma.vue</code>
    </p>
    <p>Edit component to test hot module replacement.</p>
  </v-app>
</template>

<script setup>
import {ref} from 'vue'

// Prop definition
const props = defineProps({
  msg: String,
})

const clickCount = ref(0)
const countMessage = ref(`llevamos: ${clickCount.value}`)

const incrementCount = () => {
  clickCount.value++
  countMessage.value = `llevamos: ${clickCount.value}`
}
</script>

<style scoped>


</style>