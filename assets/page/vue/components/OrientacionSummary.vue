<template>
  <v-row class="mt-2">
    <v-col cols="12" md="12" class="mx-auto">
      <v-sheet color="primary" class="text-white py-3 px-4">
        <div class="d-flex align-center" style="gap:8px;">
          <v-icon icon="mdi-compass-outline" size="20" class="me-1"></v-icon>
          <span class="font-weight-medium">Resumen O (Orientación)</span>
        </div>
      </v-sheet>

      <v-card>
        <v-card-text>
          <v-row>
            <v-col v-if="summary?.action_1" cols="12" md="4">
              <strong>Mis acciones:</strong>
              <div v-html="formatParagraphs(summary.action_1)"></div>
            </v-col>
            <v-col v-if="summary?.action_2" cols="12" md="4">
              <strong>Acción 2:</strong>
              <div v-html="formatParagraphs(summary.action_2)"></div>
            </v-col>
            <v-col v-if="summary?.action_3" cols="12" md="4">
              <strong>Acción 3:</strong>
              <div v-html="formatParagraphs(summary.action_3)"></div>
            </v-col>

            <v-col v-if="summary?.trabajar" cols="12" md="6" class="mt-2">
              <strong>Con quién me gusta trabajar:</strong>
              <div v-html="formatParagraphs(summary.trabajar)"></div>
            </v-col>
            <v-col v-if="summary?.resolver" cols="12" md="6" class="mt-2">
              <strong>Problemas que me apasiona resolver:</strong>
              <div v-html="formatParagraphs(summary.resolver)"></div>
            </v-col>

            <v-col v-if="Array.isArray(summary?.selectedLabels) && summary.selectedLabels.length" cols="12" class="mt-4">
              <strong>Mis 3 pasiones principales:</strong>
              <v-list density="compact" class="passion-list">
                <v-list-item v-for="(label, i) in summary.selectedLabels" :key="i" class="passion-item">
                  <v-list-item-title class="wrap-title">{{ i + 1 }}. {{ label }}</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script setup>
const props = defineProps({
  summary: { type: Object, default: () => ({}) },
})

function escapeHtml(str) {
  try {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
  } catch (_) {
    return String(str || '')
  }
}

function formatParagraphs(text) {
  if (!text) return ''
  const safe = escapeHtml(text)
  return safe.replace(/\n/g, '<br/>')
}
</script>

<style scoped>
.wrap-title {
  white-space: normal;
  overflow: visible;
  text-overflow: clip;
  word-break: break-word;
}

.passion-list :deep(.v-list-item) {
  margin-bottom: 10px;
  padding: 10px 12px;
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 10px;
  background-color: rgba(0, 0, 0, 0.02);
}
.passion-list :deep(.v-list-item:last-child) { margin-bottom: 0; }
</style>
