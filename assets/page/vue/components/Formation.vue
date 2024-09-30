<script setup>
import {computed, nextTick, onMounted, ref, watchEffect} from 'vue';
import axios from "../../../vendor/axios/axios.index";

const headers = ref([]);
const items = ref([]);
const items2 = ref([]);
const items3 = ref([]);
const items4 = ref([]);
let help = ref(0);
let leadership = ref(0);
let hospitality = ref(0);
let service = ref(0);
let administration = ref(0);
let discernment = ref(0);
let faith = ref(0);
let give = ref(0);
let mercy = ref(0);
let wisdom = ref(0);
let exhortation = ref(0);
let teaching = ref(0);
let pastor = ref(0);
let apostle = ref(0);
let missionary = ref(0);
let prophetic = ref(0);
let evangelism = ref(0);
let intercession = ref(0);
let dones = ref([]);
let getComputedValue = ref(() => 0);
let don = ref(null)


let service_loading = ref(false);
let selection = 1;

const indexSums = ref({});
const selectedValues = ref({});
const scoreValues = ref({});


const isPanel1Complete = ref(false);
const isPanel2Complete = ref(false);
const isPanel3Complete = ref(false);
const isPanel4Complete = ref(false);
const isAllPanelsComplete = ref(false);

const percent = 6.25;

const scores = ref([
  {label: 'Casi nunca', value: 0},
  {label: 'Un poco', value: 1},
  {label: 'Moderadamente', value: 2},
  {label: 'Considerablemente', value: 3},
  {label: 'Mucho', value: 4}
])

const fetchDataFormFormation = async () => {
  try {
    const response = await Promise.all([
      axios.get('api/formation?page=1'),
      axios.get('api/formation?page=2'),
      axios.get('api/formation?page=3'),
      axios.get('api/formation?page=4'),
      axios.get('api/dones')
    ]);

    items.value = response[0].data['hydra:member'];
    items2.value = response[1].data['hydra:member'];
    items3.value = response[2].data['hydra:member'];
    items4.value = response[3].data['hydra:member'];
    dones.value = response[4].data['hydra:member'];

    dones.value.sort((a, b) => {
      const aValue = getComputedValue.value[a.identifier] || 0;
      const bValue = getComputedValue.value[b.identifier] || 0;

      if (aValue > bValue) return -1;
      if (bValue > aValue) return 1;

      return 0;
    });
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
  console.log(isPanel1Complete.value, isPanel2Complete.value, isPanel3Complete.value, isPanel4Complete.value);
  isAllPanelsComplete.value = isPanel1Complete.value && isPanel2Complete.value && isPanel3Complete.value && isPanel4Complete.value;
});

const reserve = (() => {
  service_loading.value = !service_loading.value

  setTimeout(() => {
    service_loading.value = !service_loading.value

  }, 5000)
})


const onCargar = () => {
  isAllPanelsComplete.value = false;
}
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

};
getComputedValue = computed(() => {
  const values = {
    help: help.value = (indexSums.value['help'] ?? 0) * percent,
    leadership: leadership.value = (indexSums.value['leadership'] ?? 0) * percent,
    hospitality: hospitality.value = (indexSums.value['hospitality'] ?? 0) * percent,
    service: service.value = (indexSums.value['service'] ?? 0) * percent,
    administration: administration.value = (indexSums.value['administration'] ?? 0) * percent,
    discernment: discernment.value = (indexSums.value['discernment'] ?? 0) * percent,
    faith: faith.value = (indexSums.value['faith'] ?? 0) * percent,
    give: give.value = (indexSums.value['give'] ?? 0) * percent,
    mercy: mercy.value = (indexSums.value['mercy'] ?? 0) * percent,
    wisdom: wisdom.value = (indexSums.value['wisdom'] ?? 0) * percent,
    exhortation: exhortation.value = (indexSums.value['exhortation'] ?? 0) * percent,
    teaching: teaching.value = (indexSums.value['teaching'] ?? 0) * percent,
    pastor: pastor.value = (indexSums.value['pastor'] ?? 0) * percent,
    apostle: apostle.value = (indexSums.value['apostle'] ?? 0) * percent,
    missionary: missionary.value = (indexSums.value['missionary'] ?? 0) * percent,
    prophetic: prophetic.value = (indexSums.value['prophetic'] ?? 0) * percent,
    evangelism: evangelism.value = (indexSums.value['evangelism'] ?? 0) * percent,
    intercession: intercession.value = (indexSums.value['intercession'] ?? 0) * percent
  };
  return (identifier) => {
    return values[identifier] || 0;
  };


});

const orderedDones = computed(() => {
  return [...dones.value].sort((a, b) => {
    const aValue = getComputedValue.value(a.identifier);
    const bValue = getComputedValue.value(b.identifier);

    if (aValue > bValue) return -1;
    if (bValue > aValue) return 1;

    return 0;
  });
});

const dialog = ref(false);
let openDialog = (donData) => {
  don.value = donData;
  dialog.value = true;
}

onMounted(async () => {
  await fetchDataFormFormation(); // Usa 'await' aquí para asegurarte de que los datos ya se llenaron antes de llamar a 'checkComplete'
  checkComplete();
  onCargar()
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
                            :data-index="item.don"
                            :data-valor="score.value"
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
                            :data-index="item.don"
                            :data-valor="score.value"
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
                            :data-index="item.don"
                            :data-valor="score.value"
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
                            :data-index="item.don"
                            :data-valor="score.value"
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

    <v-row>
      <v-col cols="12">


        <v-alert type="info" style="width: 100%;">
          A continuación verás la clasificación de tus respuestas, en 18 dones
          espirituales y su porcentaje de mayor a menor.
        </v-alert>

      </v-col>
    </v-row>
<!--    <v-row>-->
<!--      <v-col cols="12" sm="6" md="4" v-for="don in orderedDones.slice(0, 3)" :key="don.id">-->
<!--        <v-card class="mx-auto my-12 bg-light-blue-accent-3 d-flex-->
<!--                flex-column justify-space-between"-->
<!--                max-width="374"-->
<!--        >-->
<!--          <v-card-item class="bg-blue-accent-3">-->
<!--            <v-card-title>{{ don.name }}</v-card-title>-->
<!--          </v-card-item>-->
<!--          <div class="d-flex flex-column align-center">-->
<!--            <v-card-text>-->
<!--              {{ don.description }}-->
<!--            </v-card-text>-->
<!--          </div>-->

<!--          <div class="d-flex justify-center align-center mb-0 bg-blue-accent-3">-->
<!--            <v-chip-group v-model="selection">-->
<!--              {{ getComputedValue(don.identifier) }} %-->
<!--            </v-chip-group>-->
<!--          </div>-->
<!--          <v-btn outlined color="primary" @click="openDialog(don)">-->
<!--            <v-icon small class="mr-1">$info</v-icon>-->
<!--            Ver descripción del don-->
<!--          </v-btn>-->
<!--        </v-card>-->
<!--      </v-col>-->
<!--    </v-row>-->

    <v-row>

      <v-col cols="12" sm="6" md="3" v-for="donElement in orderedDones" :key="donElement.id"
             class="mx-auto"
      >

        <v-card class="mx-auto my-12 bg-blue-accent-3 d-flex flex-column justify-space-between"
                max-width="374">
          <v-card-item class="bg-light-blue-accent-3">
            <v-card-title>{{ donElement.name }}</v-card-title>
          </v-card-item>
          <div class="d-flex justify-center align-center mb-0 bg-light-blue-accent-3">
            <v-chip-group v-model="selection"> {{ getComputedValue(donElement.identifier) }} %</v-chip-group>
          </div>
          <v-btn outlined color="primary" @click="openDialog(donElement)">
            <v-icon small class="mr-1">mdi-comment-eye-outline</v-icon>
            Ver descripción del don
          </v-btn>

        </v-card>
      </v-col>

    </v-row>
    <v-dialog v-model="dialog" max-width="500">
      <v-card>
        <v-card-title class="headline">{{ don.name }}</v-card-title>
        <v-card-text>{{ don.description }}</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="primary" @click="dialog = false">Cerrar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <div v-if="isAllPanelsComplete" class="mx-auto">
      <v-alert type="success" style="width: 100%;" class="text-center">
        ¡Hemos terminado las preguntas para formación espiritual, felicidades porque vamos avanzando!
      </v-alert>
      <p>
        A continuación encontrarás los 3 dones espírituales con los que más te identificas:
      </p>
      <v-row>
        <v-col cols="12" sm="6" md="4" v-for="don in orderedDones.slice(0, 3)" :key="don.id">
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
              <v-chip-group v-model="selection">
                {{ getComputedValue(don.identifier) }} %
              </v-chip-group>
            </div>
          </v-card>
        </v-col>

      </v-row>
    </div>
    <div v-else class="mx-auto">
      <v-alert type="warning" style="width: 100%;" class="text-center">
        Aún no has completado todas las preguntas para formación espiritual
      </v-alert>
    </div>
  </v-container>
</template>

<style scoped>

.cardStyle {
  flex-direction: column;
  min-height: 270px;
}

</style>