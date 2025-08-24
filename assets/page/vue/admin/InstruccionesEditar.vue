<template>
  <v-container class="admin-instrucciones-editar" fluid>
    <v-row class="justify-center">
      <v-col cols="12" md="11" lg="10">
        <v-card class="pa-2 pa-sm-4">
          <v-card-title class="d-flex align-center justify-space-between flex-wrap" style="gap: 8px;">
            <span class="text-h6">Editar Instrucción</span>
            <div class="d-flex align-center" style="gap: 8px;">
              <v-btn variant="text" @click="volver">
                <v-icon icon="mdi-arrow-left" class="mr-1" /> Volver
              </v-btn>
              <v-btn color="primary" :loading="saving" @click="guardar">
                <v-icon icon="mdi-content-save" class="mr-1" /> Guardar
              </v-btn>
            </div>
          </v-card-title>
          <v-card-text>
            <v-alert v-if="error" type="error" density="comfortable" class="mb-3">{{ error }}</v-alert>
            <v-alert v-if="editorWarning" type="warning" density="comfortable" class="mb-3">{{ editorWarning }}</v-alert>

            <v-skeleton-loader v-if="loading" type="article" class="mb-3" />

            <div v-else>
              <v-row>
                <v-col cols="12" md="8">
                  <v-text-field v-model="form.title" label="Título" required />
                </v-col>
                <v-col cols="12" md="4" class="d-flex align-center">
                  <v-switch v-model="form.enabled" inset color="primary" label="Habilitado" />
                </v-col>
              </v-row>

              <div class="mb-2"><strong>Contenido</strong></div>
              <template v-if="!useTextarea">
                <textarea ref="editorEl" class="tinymce-textarea"></textarea>
              </template>
              <template v-else>
                <v-textarea v-model="form.content" rows="12" auto-grow label="Contenido (editor básico)" />
              </template>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, computed } from 'vue'
import axios from 'axios'
// Local TinyMCE (bundled via Vite)
import tinymce from 'tinymce/tinymce'
import 'tinymce/icons/default'
import 'tinymce/themes/silver'
import 'tinymce/models/dom'
import 'tinymce/plugins/link'
import 'tinymce/plugins/lists'
import 'tinymce/plugins/table'
import 'tinymce/plugins/code'
import 'tinymce/plugins/fullscreen'
// TinyMCE skins and content CSS bundled via Vite (prevents runtime HTTP fetches)
import 'tinymce/skins/ui/oxide/skin.css'
import 'tinymce/skins/content/default/content.css'

const props = defineProps({ id: { type: String, required: true } })

const loading = ref(true)
const saving = ref(false)
const error = ref('')
const editorWarning = ref('')
const form = ref({ title: '', enabled: false, content: '' })

const editorEl = ref(null)
let editorInstance = null
const editorFailed = ref(false)
const useTextarea = computed(() => editorFailed.value)

function volver() {
  if (typeof window !== 'undefined') window.location.hash = '#instrucciones'
}


async function initEditor() {
  try {
    if (!editorEl.value) return
    // Re-init cleanly if already exists
    try { if (editorInstance) tinymce.remove(editorInstance) } catch(e) {}
    editorInstance = (await tinymce.init({
      target: editorEl.value,
      menubar: false,
      height: 400,
      plugins: 'lists link code table fullscreen',
      toolbar: 'undo redo | blocks | bold italic | bullist numlist | fontsize | alignleft aligncenter alignright | link | code fullscreen',
      fontsize_formats: '10px 12px 14px 16px 18px 24px 36px',
      // prevent TinyMCE from attempting to load skin/content CSS over HTTP; we import them via Vite
      skin: false,
      content_css: false,
      setup: (ed) => {
        ed.on('init', () => { ed.setContent(form.value.content || '') })
        ed.on('change keyup input', () => { form.value.content = ed.getContent() })
      }
    }))[0]
  } catch (e) {
    console.warn('Fallo al inicializar TinyMCE, se usará textarea:', e)
    editorFailed.value = true
    editorWarning.value = 'No se pudo cargar el editor avanzado. Usando editor básico.'
  }
}

async function cargar() {
  loading.value = true
  error.value = ''
  editorWarning.value = ''
  try {
    const res = await axios.get(`/api/instructions/${props.id}`)
    const m = res.data || {}
    form.value = {
      title: m.title ?? m.Title ?? '',
      enabled: m.enabled ?? m.Enabled ?? false,
      content: m.content ?? m.Content ?? ''
    }
    // Ensure the editor host is rendered before initializing CKEditor
    loading.value = false
    await nextTick()
    await initEditor()
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo cargar la instrucción.'
    loading.value = false
  }
}

async function guardar() {
  error.value = ''
  saving.value = true
  try {
    const payload = {
      title: form.value.title,
      enabled: !!form.value.enabled,
      content: form.value.content
    }
    await axios.put(`/admin/instrucciones/${props.id}/update`, payload, {
      headers: { 'Content-Type': 'application/json' }
    })
    volver()
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo guardar los cambios.'
  } finally {
    saving.value = false
  }
}

onMounted(cargar)

onBeforeUnmount(async () => {
  try { if (editorInstance) tinymce.remove(editorInstance) } catch (e) {}
})
</script>

<style scoped>
.admin-instrucciones-editar { padding-top: 16px; padding-bottom: 16px; }
.ck-editor-host, .tinymce-textarea { min-height: 280px; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px; background: #fff; }
.mb-3 { margin-bottom: 1rem; }
.mb-2 { margin-bottom: 0.5rem; }
</style>
