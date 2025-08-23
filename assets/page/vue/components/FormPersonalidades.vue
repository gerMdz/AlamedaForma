<template>
  <v-container fluid class="fill-height w-auto">
    <VForm @submit="handleSubmit" class="large-form">
      <VRow v-for="(question, index) in questions" :key="question.id" class="text-center">
                  <VCol cols="12" sm="12" md="6" lg="3" xl="3">
<!--        <v-col cols="12" class="d-flex justify-center align-items-center">-->
          <v-sheet class="pa-2 ma-2">
            <span> {{ question.d }}</span>
            <v-select
                :items="uniqueChoices(index, 'd').value"
                v-model="answers[index].d"
                required
                clearable

            ></v-select>
          </v-sheet>
<!--        </v-col>-->
                  </VCol>
        <VCol cols="12" sm="12" md="6" lg="3" xl="3">
          <v-sheet class="pa-2 ma-2">
          <span> {{ question.i }}</span>
          <v-select
              :items="uniqueChoices(index, 'i').value"
              v-model="answers[index].i"
              required
              clearable
          ></v-select>
          </v-sheet>
        </VCol>
        <VCol cols="12" sm="12" md="6" lg="3" xl="3">
          <v-sheet class="pa-2 ma-2">
          <span> {{ question.s }}</span>
          <v-select
              :items="uniqueChoices(index, 's').value"
              v-model="answers[index].s"
              required
              clearable
          ></v-select>
          </v-sheet>
        </VCol>
        <VCol cols="12" sm="12" md="6" lg="3" xl="3">
          <v-sheet class="pa-2 ma-2">
          <span> {{ question.c }}</span>
          <v-select
              :items="uniqueChoices(index, 'c').value"
              v-model="answers[index].c"
              required
              clearable
          ></v-select>
            </v-sheet>
        </VCol>
      </VRow>
      <VBtn type="submit">Submit</VBtn>
    </VForm>
  </v-container>
</template>

<script setup>
import {ref, computed} from 'vue';
import axios from 'axios';

const questions = ref([]);
const answers = ref([]);

const fetchData = async () => {
  try {
    const response = await axios.get('api/personalidad');
    questions.value = response.data['member'];
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
  const allChoices = [1, 2, 3, 4, ''];
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