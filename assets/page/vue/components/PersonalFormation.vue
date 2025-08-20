<template>
  <v-container>
    <v-row>
      <v-col cols="12" sm="6" md="4" v-for="don in dones" :key="don.id">
        <v-card class="cardStyle mx-auto my-12 bg-light-blue-accent-3 d-flex
                flex-column justify-space-between"
                max-width="374"
        >
          <v-card-item class="bg-blue-accent-3">
            <v-card-title>{{ don.name }}</v-card-title>
          </v-card-item>
          <div class="d-flex flex-column align-center">
            <v-card-text>
              {{ don.description }}
            </v-card-text>
          </div>

          <div class="d-flex justify-center align-center mb-0 bg-blue-accent-3">
            <v-chip-group >
              {{ getComputedValue(don.identifier) }} %
            </v-chip-group>
          </div>
        </v-card>
      </v-col>

    </v-row>

    <v-form ref="form" @submit.prevent="submitForm">
      <v-row>
        <v-col :cols="12" :sm="6" :md="4" v-for="don in dones" :key="don.id">
          <v-card class="cardStyle mx-auto my-12 bg-light-blue-accent-3 d-flex flex-column justify-space-between"
                  max-width="374">
            <v-card-item class="bg-blue-accent-3">
              <v-card-title>{{ don.name }}</v-card-title>
            </v-card-item>
            <v-card-text>
              {{ don.description }}
            </v-card-text>
            <v-textarea v-model="form.donComments[don.id]" label="commentDon" outlined dense/>
            <v-chip-group v-model="selection">
              {{ getComputedValue(don.identifier) }} %
            </v-chip-group>
          </v-card>
        </v-col>
      </v-row>
      <v-btn color="primary" @click="submitForm">
        Submit
      </v-btn>
    </v-form>
  </v-container>

</template>
<script setup>
import {ref, reactive} from 'vue';
import {defineProps} from 'vue';
import axios from 'axios';

console.log("Datos que pasarán a PersonalFormation: ", dones);

const props = defineProps({
  dones: Array,
  getComputedValue: Function,
});


let form = reactive({
  percentDon: '', // Deberías completar estos valores dependiendo de tu lógica
  don: '', // Deberías completar estos valores dependiendo de tu lógica
  person: '', // Deberías completar estos valores dependiendo de tu lógica
  donComments: {},
});

const submitForm = async () => {
  const {percentDon, don, person, donComments} = form;

  for (const [donId, commentDon] of Object.entries(donComments)) {
    if (commentDon) {
      const response = await axios.post('api/personal-formation', {
        percentDon, don, person, commentDon,
      });

      if (response.data.success) {
        form = reactive({
          percentDon: '', // Deberías completar estos valores dependiendo de tu lógica
          don: '', // Deberías completar estos valores dependiendo de tu lógica
          person: '', // Deberías completar estos valores dependiendo de tu lógica
          donComments: {},
        });
        // Mostrar algún mensaje de éxito o tomar alguna acción
      }
    }
  }
};
</script>
<style scoped>

.cardStyle {
  flex-direction: column;
  min-height: 270px;
}

</style>