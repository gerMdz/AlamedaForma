<template>
  <VForm @submit="handleSubmit" class="large-form">
    <VRow v-for="(question, index) in questions" :key="question.id">
      <VCol cols="3">
        <span> {{ question.d }}</span>
        <v-select
            :items="uniqueChoices(index, 'd').value"
            v-model="answers[index].d"
            required
            clearable
        ></v-select>
      </VCol>
      <VCol cols="3">
        <span> {{ question.i }}</span>
        <v-select
            :items="uniqueChoices(index, 'i').value"
            v-model="answers[index].i"
            required
            clearable
        ></v-select>
      </VCol>
      <VCol cols="3">
        <span> {{ question.s }}</span>
        <v-select
            :items="uniqueChoices(index, 's').value"
            v-model="answers[index].s"
            required
            clearable
        ></v-select>
      </VCol>
      <VCol cols="3">
        <span> {{ question.c }}</span>
        <v-select
            :items="uniqueChoices(index, 'c').value"
            v-model="answers[index].c"
            required
            clearable
        ></v-select>
      </VCol>
    </VRow>
    <VBtn type="submit">Submit</VBtn>
  </VForm>
</template>

<script setup>
import {ref, computed} from 'vue';
import axios from 'axios';

const questions = ref([]);
const answers = ref([]);

const fetchData = async () => {
  try {
    const response = await axios.get('api/personalidad');
    questions.value = response.data['hydra:member'];
    answers.value = questions.value.map(_ => ({d: null, i: null, s: null, c: null}));
  } catch (err) {
    console.error(err);
  }
}

fetchData();

const uniqueChoices = (index, exceptKey) => computed(() => {
  const answer = answers.value[index];
  const excludeValue = answer[exceptKey];
  const otherValues = Object.values(answer).filter(val => val !== excludeValue && val !== null);
  const allChoices = [1,2,3,4, ''];
  return allChoices.filter(choice => !otherValues.includes(choice));
});

const handleSubmit = (event) => {
  // prevent default form submission
  event.preventDefault();

  // make an API call to submit the answers
  axios.post('/api/submit', answers.value)
      .then(response => console.log(response.data));
}
</script>

<style scoped>
.large-form {
  width: 800px;
  /* o cualquier ancho que prefieras, también puedes usar porcentajes ej. 100% */
}
</style>