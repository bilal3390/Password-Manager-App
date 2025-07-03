<template>
  <div class="add-password-page">
    <nav class="navbar">
      <div class="logo">🔐 BitVault</div>
      <div class="nav-links">
        <span class="profile">👤 {{ userName }}</span>
        <button @click="logout">Logout</button>
      </div>
    </nav>

    <div class="content">
      <h2>Add New Password</h2>

      <form @submit.prevent="addPassword" class="password-form">
        <label>
          Title:
          <input type="text" v-model="form.title" placeholder="e.g. GitHub" required />
        </label>

        <label>
          Username:
          <input type="text" v-model="form.username" placeholder="e.g. user@example.com" required />
        </label>

        <label>
          Password:
          <input type="text" v-model="form.password" placeholder="Enter password" required @input="checkStrength" />
        </label>

        <!-- Strength bar -->
        <div class="strength-bar-container">
          <div class="strength-bar" :style="{ width: strength + '%', backgroundColor: barColor }"></div>
        </div>
        <small>Password Strength: {{ strength }}/100</small>

        <!-- Generate password -->
        <button type="button" @click="generatePassword">Generate Password</button>

        <button type="submit">Save Password</button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { useToast } from "vue-toastification";

const router = useRouter();
const Toast = useToast();
const userName = ref("User");

const form = ref({
  title: "",
  username: "",
  password: "",
});

const strength = ref(0);
const barColor = ref("#f87171"); // red by default

const addPassword = async () => {
  try {
    const token = localStorage.getItem("auth_token");
    await axios.post("http://localhost:8000/api/password-entries", form.value, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    Toast.success("Password added successfully!");
    router.push("/dashboard");
  } catch (err) {
    console.error("Failed to add password", err);
    Toast.error("Failed to add password.");
    if (err.response?.status === 401) router.push("/login");
  }
};

const checkStrength = async () => {
  const pwd = form.value.password;

  if (pwd.length < 8) {
    strength.value = 0;
    barColor.value = "#f87171"; // red
    return;
  }

  try {
    const token = localStorage.getItem("auth_token");
    const res = await axios.post(
      "http://localhost:8000/api/check-password-strength",
      { password: pwd },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    );
    const apiScore = res.data.strength ?? 0;
    strength.value = Math.min(100, Math.max(0, apiScore * 10));

    if (strength.value < 40) {
      barColor.value = "#f87171"; // red
    } else if (strength.value < 70) {
      barColor.value = "#facc15"; // yellow
    } else {
      barColor.value = "#4ade80"; // green
    }
  } catch (err) {
    console.error("Strength check failed", err);
    strength.value = 0;
    barColor.value = "#f87171";
  }
};

const generatePassword = async () => {
  try {
    const token = localStorage.getItem("auth_token");
    const res = await axios.post(
      "http://localhost:8000/api/generate-password",
      {},
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    );
    form.value.password = res.data.password;
    checkStrength();
  } catch (err) {
    console.error("Password generation failed", err);
    Toast.error("Failed to generate password.");
  }
};

const logout = () => {
  localStorage.removeItem("auth_token");
  router.push("/login");
  Toast.success("Logout successful!");
};
</script>

<style scoped>
.add-password-page {
  background: #0b0f16;
  color: #e2e8f0;
  min-height: 100vh;
}
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #111827;
  padding: 15px 30px;
}
.logo {
  font-size: 1.5rem;
  color: #38bdf8;
  font-weight: bold;
}
.nav-links {
  display: flex;
  align-items: center;
  gap: 20px;
}
.nav-links .profile {
  color: #94a3b8;
}
.nav-links button {
  padding: 8px 14px;
  background-color: #00bcd4;
  border: none;
  border-radius: 6px;
  color: #0f172a;
  cursor: pointer;
}
.content {
  padding: 30px;
}
h2 {
  font-size: 1.8rem;
  color: #38bdf8;
  margin-bottom: 20px;
}
.password-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
  max-width: 400px;
}
.password-form label {
  display: flex;
  flex-direction: column;
}
.password-form input {
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #334155;
  background: #1e293b;
  color: #f1f5f9;
}
.strength-bar-container {
  width: 100%;
  height: 8px;
  background: #334155;
  border-radius: 4px;
  overflow: hidden;
}
.strength-bar {
  height: 100%;
  transition: width 0.3s ease;
}
.password-form button {
  padding: 12px;
  background-color: #0ea5e9;
  color: #0f172a;
  border: none;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
}
.password-form button:hover {
  background-color: #38bdf8;
}
</style>
