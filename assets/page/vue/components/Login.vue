<template>
  <div class="login-wrapper">
    <h1 class="title">Ingresar</h1>

    <form @submit.prevent="onSubmit" class="login-form">
      <div class="form-group">
        <label for="email">Email</label>
        <input id="email" v-model="email" type="email" autocomplete="email" required placeholder="email@dominio.com" />
      </div>

      <div class="form-group">
        <label for="password">Contraseña</label>
        <input id="password" v-model="password" type="password" autocomplete="current-password" required placeholder="••••••" />
      </div>

      <div class="form-actions">
        <button type="submit" :disabled="loading">{{ loading ? 'Ingresando…' : 'Ingresar' }}</button>
      </div>

      <p v-if="error" class="error">{{ error }}</p>
      <p v-if="success" class="success">Ingreso exitoso. Redirigiendo…</p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')
const success = ref(false)

async function onSubmit() {
  error.value = ''
  success.value = false

  const emailVal = (email.value || '').trim()
  const passVal = password.value || ''

  if (!emailVal || !passVal) {
    error.value = 'Complete email y contraseña.'
    return
  }

  loading.value = true
  try {
    const resp = await fetch('/login_check', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'same-origin',
      body: JSON.stringify({ email: emailVal, password: passVal })
    })

    if (resp.status === 204 || resp.ok) {
      success.value = true
      // optional: verify session by pinging /api/me
      try {
        await fetch('/api/me', { credentials: 'same-origin' })
      } catch (e) {
        // ignore
      }
      // redirect to a protected area
      setTimeout(() => {
        window.location.href = '/admin'
      }, 600)
    } else {
      // Fallback: sometimes the POST may not return 2xx even though the session was created.
      // Verify authentication by pinging /api/me; if 200, proceed to redirect.
      try {
        const meResp = await fetch('/api/me', { credentials: 'same-origin' })
        if (meResp.ok) {
          success.value = true
          setTimeout(() => {
            window.location.href = '/admin'
          }, 300)
          return
        }
      } catch (e) {
        // ignore and continue to show error
      }

      let msg = 'No se pudo iniciar sesión.'
      try {
        const data = await resp.json()
        if (data && data.message) msg = data.message
        if (data && data.error) msg = data.error
      } catch (e) {}
      error.value = msg
    }
  } catch (e) {
    error.value = 'Error de red. Intente nuevamente.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-wrapper {
  max-width: 420px;
  margin: 3rem auto;
  padding: 2rem;
  border: 1px solid #eee;
  border-radius: 8px;
  background: #fff;
}
.title {
  margin: 0 0 1rem 0;
  font-size: 1.6rem;
}
.login-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
.form-group {
  display: flex;
  flex-direction: column;
}
.form-group label {
  font-weight: 600;
  margin-bottom: 0.25rem;
}
.form-group input {
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  border: 1px solid #ccc;
}
.form-actions {
  display: flex;
  justify-content: flex-end;
}
button[type="submit"] {
  padding: 0.6rem 1.25rem;
  border: none;
  border-radius: 6px;
  background: #2f6feb;
  color: white;
  cursor: pointer;
}
button[disabled] {
  opacity: 0.7;
  cursor: not-allowed;
}
.error { color: #b00020; }
.success { color: #1b8a3a; }
</style>
