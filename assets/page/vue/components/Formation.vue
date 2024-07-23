<script setup>
import {nextTick, onMounted, ref, watchEffect} from 'vue';
import axios from "../../../vendor/axios/axios.index";
import {th} from "vuetify/locale";

const headers = ref([]);
const items = ref([]);
const items2 = ref([]);
const items3 = ref([]);
const items4 = ref([]);
let ayuda = ref(0);
let liderazgo = ref(0);
let hospitalidad = ref(0);
let servicio = ref(0);
let administración = ref(0);
let discernimiento = ref(0);
let fe = ref(0);
let dar = ref(0);
let misericordia = ref(0);
let sabiduría = ref(0);
let exhortación = ref(0);
let enseñanza = ref(0);
let pastor = ref(0);
let apóstol = ref(0);
let misionero = ref(0);
let profecía = ref(0);
let evangelismo = ref(0);
let intercesión = ref(0);

const indexSums = ref({});
const selectedValues = ref({});
const scoreValues = ref({});


const isPanel1Complete = ref(false);
const isPanel2Complete = ref(false);
const isPanel3Complete = ref(false);
const isPanel4Complete = ref(false);
const isAllPanelsComplete = ref(false);

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

    const response2 = await axios.get('api/formation?page=2');
    items2.value = response2.data['hydra:member'];

    const response3 = await axios.get('api/formation?page=3');
    items3.value = response3.data['hydra:member'];

    const response4 = await axios.get('api/formation?page=4');
    items4.value = response4.data['hydra:member'];
  } catch (err) {
    console.error(err);
  }
}

const selectItem = (item, value) => {
  item.selected = value;
  sumaDonPorcentaje(item, value);
  checkComplete();

};

const checkComplete = (() => {
  isPanel1Complete.value = items.value.every(item => item.selected !== undefined);
  isPanel2Complete.value = items2.value.every(item => item.selected !== undefined);
  isPanel3Complete.value = items3.value.every(item => item.selected !== undefined);
  isPanel4Complete.value = items4.value.every(item => item.selected !== undefined);
  isAllPanelsComplete.value = isPanel1Complete.value && isPanel2Complete.value && isPanel3Complete.value && isPanel4Complete.value;


});
const sumaDonPorcentaje = (item, valor) => {

  const key = `${item.identifier}-${item.don}`;
  scoreValues.value[key] = valor;


  const sum = Object.entries(scoreValues.value)
      // Filtramos por las entradas que tengan este índice.
      .filter(([k, v]) => k.endsWith(`-${item.don}`))
      // Sumamos los puntajes.
      .reduce((sum, [k, v]) => sum + v, 0);

  // Almacenamos la suma en indexSums.
  indexSums.value[item.don] = sum;


  ayuda = indexSums.value['ayuda'];
  liderazgo = indexSums.value['liderazgo'];
  hospitalidad = indexSums.value['hospitalidad'];
  servicio = indexSums.value['servicio'];
  administración = indexSums.value['administración'];
  discernimiento = indexSums.value['discernimiento'];
  fe = indexSums.value['fe'];
  dar = indexSums.value['dar'];
  misericordia = indexSums.value['misericordia'];
  sabiduría = indexSums.value['sabiduría'];
  exhortación = indexSums.value['exhortación'];
  enseñanza = indexSums.value['enseñanza'];
  pastor = indexSums.value['pastor'];
  apóstol = indexSums.value['apóstol'];
  misionero = indexSums.value['misionero'];
  profecía = indexSums.value['profecía'];
  evangelismo = indexSums.value['evangelismo'];
  intercesión = indexSums.value['intercesión'];

  console.log(ayuda)
  console.log(liderazgo)
  console.log(hospitalidad)
  console.log(servicio)
  console.log(administración)
  console.log(discernimiento)
  console.log(fe)
  console.log(dar)
  console.log(misericordia)
  console.log(sabiduría)
  console.log(exhortación)
  console.log(enseñanza)
  console.log(pastor)
  console.log(apóstol)
  console.log(misionero)
  console.log(profecía)
  console.log(evangelismo)
  console.log(intercesión)


};

onMounted(() => {
  fetchDataFormFormation();
  checkComplete(); // Verifica el estado de finalización después de obtener los datos
});
</script>

<template>
  <v-container fluid class="fill-height">
    <v-expansion-panels class="my-1" variant="accordion">
      <v-expansion-panel title="Preguntas 1 - 18" class="bg-primary my-1">
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
                            :name="'group-' + item.identifier"
                            :id="item.identifier + '-' + score.value"
                            :value="score.value"
                            v-model="item.selected"
                            :data-index = "item.don"
                            :data-valor = "score.value"
                            @change="selectItem(item, score.value)"
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
      <v-expansion-panel title="Preguntas 19 - 36" class="bg-primary my-1">
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
                            :name="'group-' + item.identifier"
                            :id="item.identifier + '-' + score.value"
                            :value="score.value"
                            v-model="item.selected"
                            :data-index = "item.don"
                            :data-valor = "score.value"
                            @change="selectItem(item, score.value)"
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
      <v-expansion-panel title="Preguntas 37 - 54" class="bg-primary my-1">
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
                            :name="'group-' + item.identifier"
                            :id="item.identifier + '-' + score.value"
                            :value="score.value"
                            v-model="item.selected"
                            :data-index = "item.don"
                            :data-valor = "score.value"
                            @change="selectItem(item, score.value)"
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
      <v-expansion-panel title="Preguntas 55 - 72" class="bg-primary my-1">
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
                            :name="'group-' + item.identifier"
                            :id="item.identifier + '-' + score.value"
                            :value="score.value"
                            v-model="item.selected"
                            :data-index = "item.don"
                            :data-valor = "score.value"
                            @change="selectItem(item, score.value)"
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

    <div v-if="isAllPanelsComplete">
      ¡Hemos terminado las preguntas para formación espiritual, felicidades porque vamos avanzando!
      <h4>Te presentamos un resumen de tus resultados obtenidos:</h4>
    </div>
  </v-container>
</template>

<style scoped>

</style>