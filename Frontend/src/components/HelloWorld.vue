<template>
  <div class="landing-wrapper">
    <div class="glass-card">
      <h1>Password Vault</h1>
      <p>Protect your digital life with next-gen encryption 🔐</p>
      <button @click="goToAuth" class="cta-button">Get Started</button>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const goToAuth = () => {
  router.push('/login')
}

onMounted(() => {
  setTimeout(() => {
    router.push('/login')
  }, 2000)
})
</script>

<style scoped>
.landing-wrapper {
  height: 100vh;
  background: linear-gradient(145deg, #0d1117, #0b0f16);
  display: flex;
  justify-content: center;
  align-items: center;
  color: #e2e8f0;
  padding: 20px;
}

.glass-card {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(0, 255, 255, 0.1);
  backdrop-filter: blur(12px);
  border-radius: 18px;
  padding: 50px 40px;
  text-align: center;
  box-shadow: 0 0 30px rgba(0, 255, 255, 0.05);
  transition: all 0.3s ease-in-out;
}

h1 {
  font-size: 2.8rem;
  margin-bottom: 16px;
  background: linear-gradient(to right, #38bdf8, #00ffff);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

p {
  font-size: 1.2rem;
  color: #cbd5e1;
  margin-bottom: 30px;
}

.cta-button {
  background-color: #00bcd4;
  color: #0d1117;
  padding: 14px 32px;
  font-size: 1rem;
  font-weight: bold;
  border-radius: 10px;
  cursor: pointer;
  border: none;
  transition: 0.3s;
}

.cta-button:hover {
  background-color: #03a9f4;
}
</style>
