<script setup>
import {ref} from 'vue';
import axios from "../../../vendor/axios/axios.index";

const headers = ref([]); // tus headers aquí
const items = ref([]); // tus items aquí
const items2 = ref([]); // tus items aquí
const items3 = ref([]); // tus items aquí
const items4 = ref([]); // tus items aquí
const scores = ref([
  {label: 'Casi nunca', value: 0},
  {label: 'Un poco', value: 1},
  {label: 'Moderadamente', value: 2},
  {label: 'Considerablemente', value: 3},
  {label: 'Mucho', value: 4}
])

const fetchDataFormFormation = async () => {
  try {
    const response = await axios.get('api/formation?page=1');
    items.value = response.data['hydra:member'];
    console.log(items.value);

    const response2 = await axios.get('api/formation?page=2');
    items2.value = response2.data['hydra:member'];
    console.log(items2.value);

    const response3 = await axios.get('api/formation?page=3');
    items3.value = response3.data['hydra:member'];
    console.log(items3.value);

    const response4 = await axios.get('api/formation?page=4');
    items4.value = response4.data['hydra:member'];
    console.log(items4.value);
  } catch (err) {
    console.error(err);
  }
}

fetchDataFormFormation();
</script>

<template>
  <v-container fluid class="fill-height">
    <v-expansion-panels class="my-1" variant="accordion">
      <v-expansion-panel title="Preguntas 1 - 18">
        <v-expansion-panel-text>
          <v-data-table
              class="elevation-1"
              :headers="headers"
              :items="items"
              item-key="identifier"
              :items-per-page="0"
              :hide-default-header="true"
              :hide-default-footer="true"
          >
            <template v-slot:default="{ items }">
              <tbody>
              <tr v-for="item in items" :key="item.orden">
                <td class="text-start">
                  {{ item.orden }} - {{ item.description }}
                  <div class="text-right py-2">
                    <v-row class="radios col-sm-12 ">
                      <v-col v-for="score in scores"
                             :key="score.value"
                             class="d-inline-flex align-center justify-space-between col-sm-12 col-md-3 col-lg-3 col-xl-3"
                      >
                        <v-spacer></v-spacer>
                        <span>{{ score.label }}</span>

                        <v-radio
                            :name="score.label"
                            :id="item.identifier + '-' + score.value"
                            :value="score.value"
                        ></v-radio>
                      </v-col>
                    </v-row>
                  </div>
                </td>
              </tr>
              </tbody>
            </template>
          </v-data-table>
        </v-expansion-panel-text>
      </v-expansion-panel>
      <v-expansion-panel title="Preguntas 19 - 36">
        <v-expansion-panel-text>
          <v-data-table
              class="elevation-1"
              :headers="headers"
              :items="items2"
              item-key="identifier"
              :items-per-page="0"
              :hide-default-header="true"
              :hide-default-footer="true"
          >
            <template v-slot:default="{ items }">
              <tbody>
              <tr v-for="item in items" :key="item.orden">
                <td class="text-start">
                  {{ item.orden }} - {{ item.description }}
                  <div class="text-right py-2">
                    <v-row class="radios col-sm-12 ">
                      <v-col v-for="score in scores"
                             :key="score.value"
                             class="d-inline-flex align-center justify-space-between col-sm-12 col-md-3 col-lg-3 col-xl-3"
                      >
                        <v-spacer></v-spacer>
                        <span>{{ score.label }}</span>

                        <v-radio
                            :name="score.label"
                            :id="item.identifier + '-' + score.value"
                            :value="score.value"
                        ></v-radio>
                      </v-col>
                    </v-row>
                  </div>
                </td>
              </tr>
              </tbody>
            </template>
          </v-data-table>
        </v-expansion-panel-text>
      </v-expansion-panel>
      <v-expansion-panel title="Preguntas 37 - 54">
        <v-expansion-panel-text>
          <v-data-table
              class="elevation-1"
              :headers="headers"
              :items="items3"
              item-key="identifier"
              :items-per-page="0"
              :hide-default-header="true"
              :hide-default-footer="true"
          >
            <template v-slot:default="{ items }">
              <tbody>
              <tr v-for="item in items" :key="item.orden">
                <td class="text-start">
                  {{ item.orden }} - {{ item.description }}
                  <div class="text-right py-2">
                    <v-row class="radios col-sm-12 ">
                      <v-col v-for="score in scores"
                             :key="score.value"
                             class="d-inline-flex align-center justify-space-between col-sm-12 col-md-3 col-lg-3 col-xl-3"
                      >
                        <v-spacer></v-spacer>
                        <span>{{ score.label }}</span>

                        <v-radio
                            :name="score.label"
                            :id="item.identifier + '-' + score.value"
                            :value="score.value"
                        ></v-radio>
                      </v-col>
                    </v-row>
                  </div>
                </td>
              </tr>
              </tbody>
            </template>
          </v-data-table>
        </v-expansion-panel-text>
      </v-expansion-panel>
      <v-expansion-panel title="Preguntas 55 - 72">
        <v-expansion-panel-text>
          <v-data-table
              class="elevation-1"
              :headers="headers"
              :items="items4"
              item-key="identifier"
              :items-per-page="0"
              :hide-default-header="true"
              :hide-default-footer="true"
          >
            <template v-slot:default="{ items }">
              <tbody>
              <tr v-for="item in items" :key="item.orden">
                <td class="text-start">
                  {{ item.orden }} - {{ item.description }}
                  <div class="text-right py-2">
                    <v-row class="radios col-sm-12 ">
                      <v-col v-for="score in scores"
                             :key="score.value"
                             class="d-inline-flex align-center justify-space-between col-sm-12 col-md-3 col-lg-3 col-xl-3"
                      >
                        <v-spacer></v-spacer>
                        <span>{{ score.label }}</span>

                        <v-radio
                            :name="score.label"
                            :id="item.identifier + '-' + score.value"
                            :value="score.value"
                        ></v-radio>
                      </v-col>
                    </v-row>
                  </div>
                </td>
              </tr>
              </tbody>
            </template>
          </v-data-table>
        </v-expansion-panel-text>
      </v-expansion-panel>
    </v-expansion-panels>
  </v-container>
</template>

<style scoped>

</style>