<template>
  <div class="signup-wrapper">
    <div class="signup-card">
      <h2>Create Account</h2>
      <p>Start protecting your passwords today!</p>

      <form @submit.prevent="handleSignup">
        <input
          v-model="name"
          type="text"
          placeholder="Full Name"
          required
        />
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
        <input
          v-model="password_confirmation"
          type="password"
          placeholder="Confirm Password"
          required
        />

        <button type="submit" :disabled="loading">
          {{ loading ? 'Signing up...' : 'Sign Up' }}
        </button>

        <!-- <p class="error" v-if="error">{{ error }}</p> -->

        <p class="login-link">
          Already have an account?
          <router-link to="/login">Login</router-link>
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

const router = useRouter()

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const error = ref('')
const loading = ref(false)
const toast = useToast()

const handleSignup = async () => {
  error.value = ''
  loading.value = true

  try {
    const response = await axios.post('http://localhost:8000/api/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value
    })

    const token = response.data.token
    localStorage.setItem('auth_token', token)
    toast.success('Sign Up successful!')
    router.push('/dashboard')
  } catch (err) {
    const message = err.response?.data?.message || 'Signup failed'
    toast.error(message)
    error.value = message
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.signup-wrapper {
  height: 100vh;
  background: linear-gradient(145deg, #0d1117, #0b0f16);
  display: flex;
  justify-content: center;
  align-items: center;
  color: #e2e8f0;
  padding: 20px;
}

.signup-card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(0, 255, 255, 0.1);
  backdrop-filter: blur(12px);
  border-radius: 18px;
  padding: 40px 35px;
  width: 100%;
  max-width: 420px;
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

.login-link {
  text-align: center;
  margin-top: 20px;
  color: #94a3b8;
}

.login-link a {
  color: #38bdf8;
  text-decoration: underline;
}
</style>
