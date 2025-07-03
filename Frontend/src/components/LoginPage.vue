<template>
  <div class="login-wrapper">
    <div class="login-card">
      <h2>Welcome Back</h2>
      <p>Login to access your vault</p>

      <form @submit.prevent="handleLogin">
        <input
          v-model="email"
          type="email"
          placeholder="Email"
          required
        />
        <input
          v-model="password"
          type="password"
          placeholder="Password"
          required
        />

        <button type="submit" :disabled="loading">
          {{ loading ? 'Logging in...' : 'Login' }}
        </button>

        <p class="signup-link">
          Don't have an account?
          <router-link to="/signup">Sign Up</router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const router = useRouter()

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

const handleLogin = async () => {
  error.value = ''
  loading.value = true

  try {
    const response = await axios.post('http://localhost:8000/api/login', {
      email: email.value,
      password: password.value
    })

    const token = response.data.access_token
    localStorage.setItem('auth_token', token)

    toast.success('Login successful!')

    router.push('/dashboard')
  } catch (err) {
    const message = err.response?.data?.message || 'Login failed'
    toast.error(message)
    error.value = message
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-wrapper {
  height: 100vh;
  background: linear-gradient(145deg, #0d1117, #0b0f16);
  display: flex;
  justify-content: center;
  align-items: center;
  color: #e2e8f0;
  padding: 20px;
}

.login-card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(0, 255, 255, 0.1);
  backdrop-filter: blur(12px);
  border-radius: 18px;
  padding: 40px 35px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 0 30px rgba(0, 255, 255, 0.08);
}

h2 {
  font-size: 2rem;
  margin-bottom: 10px;
  color: #38bdf8;
}

p {
  font-size: 1rem;
  margin-bottom: 20px;
  color: #cbd5e1;
}

input {
  width: 100%;
  padding: 12px;
  margin-bottom: 15px;
  border-radius: 8px;
  border: none;
  background: #1e293b;
  color: white;
  font-size: 0.95rem;
}

button {
  width: 100%;
  padding: 12px;
  background-color: #00bcd4;
  border: none;
  color: #0d1117;
  font-weight: bold;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s;
}

button:hover {
  background-color: #03a9f4;
}

.error {
  color: #f87171;
  margin-top: 10px;
}

.signup-link {
  text-align: center;
  margin-top: 20px;
  color: #94a3b8;
}

.signup-link a {
  color: #38bdf8;
  text-decoration: underline;
}
</style>
