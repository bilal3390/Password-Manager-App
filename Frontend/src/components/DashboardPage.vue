<template>
  <div class="dashboard">
    <nav class="navbar">
      <div class="logo">🔐 BitVault</div>
      <div class="nav-links">
        <span class="profile">👤 {{ userName }}</span>
        <button @click="logout">Logout</button>
      </div>
    </nav>

    <div class="content">
      <h2>Your Passwords</h2>

      <div class="password-list">
        <div v-for="entry in passwords" :key="entry.id" class="password-card">
          <div
            style="
              display: flex;
              justify-content: space-between;
              align-items: center;
            "
          >
            <h3 style="margin: 0">{{ entry.title }}</h3>
            <button
              @click="confirmDelete(entry.id)"
              style="
                background: none;
                border: none;
                cursor: pointer;
                color: #f87171;
                font-size: 1.2rem;
              "
              title="Delete Password"
            >
              🗑️
            </button>
          </div>

          <p><strong>Username:</strong> {{ entry.username }}</p>
          <p>
            <strong>Password: </strong>
            <span>
              {{ visiblePasswords[entry.id] ? entry.password : "••••••••" }}
              <button
                @click="togglePassword(entry.id)"
                style="
                  margin-left: 10px;
                  background: none;
                  border: none;
                  cursor: pointer;
                  color: #38bdf8;
                "
                title="Show/Hide Password"
              >
                {{ visiblePasswords[entry.id] ? "🙈" : "👁️" }}
              </button>
            </span>
          </p>
        </div>
      </div>

      <button class="add-btn" @click="openAddPassword">➕ Add Password</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { useToast } from "vue-toastification";
import Swal from "sweetalert2";

const router = useRouter();
const passwords = ref([]);
const userName = ref("User"); // You can load this from backend later
const Toast = useToast();

const fetchPasswords = async () => {
  try {
    const token = localStorage.getItem("auth_token");
    const response = await axios.get(
      "http://localhost:8000/api/password-entries",
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    );
    passwords.value = response.data;
    console.log(response.data);
  } catch (err) {
    console.error("Failed to fetch passwords", err);
    if (err.response?.status === 401) router.push("/login");
  }
};

const logout = () => {
  localStorage.removeItem("auth_token");
  router.push("/login");
  Toast.success("Logout successful!");
};

onMounted(() => {
  fetchPasswords();
});

const visiblePasswords = ref({});

const togglePassword = (id) => {
  visiblePasswords.value[id] = !visiblePasswords.value[id];
};

const openAddPassword = () => {
  router.push("/add-password");
};

const confirmDelete = async (id) => {
  const result = await Swal.fire({
    title: "Are you sure?",
    text: "This password will be permanently deleted.",
    icon: "warning",
    background: "#0b0f16",
    color: "#e2e8f0", // Your app text color
    showCancelButton: true,
    confirmButtonColor: "#e11d48", // Red confirm
    cancelButtonColor: "#38bdf8", // Cyan cancel
    confirmButtonText: "Yes, delete it!",
    cancelButtonText: "Cancel",
    customClass: {
      popup: "bitvault-swal",
      title: "bitvault-swal-title",
      confirmButton: "bitvault-swal-confirm",
      cancelButton: "bitvault-swal-cancel",
    },
  });

  if (result.isConfirmed) {
    try {
      const token = localStorage.getItem("auth_token");
      await axios.delete(`http://localhost:8000/api/password-entries/${id}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });
      Toast.success("Password deleted successfully!");
      fetchPasswords();
    } catch (err) {
      console.error("Failed to delete password", err);
      Toast.error("Failed to delete password.");
    }
  }
};
</script>

<style scoped>
.dashboard {
  background: #0b0f16;
  color: #e2e8f0;
  min-height: 100vh;
  padding-bottom: 80px;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: #111827;
  padding: 15px 30px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
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
  transition: 0.2s ease;
}

.nav-links button:hover {
  background-color: #03a9f4;
}

.content {
  padding: 30px;
}

h2 {
  font-size: 1.8rem;
  color: #38bdf8;
  margin-bottom: 20px;
}

.password-list {
  display: grid;
  gap: 16px;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
}

.password-card {
  background: rgba(255, 255, 255, 0.04);
  padding: 20px;
  border-radius: 12px;
  border: 1px solid rgba(0, 255, 255, 0.06);
  box-shadow: 0 0 10px rgba(0, 255, 255, 0.04);
}

.password-card h3 {
  margin-bottom: 8px;
  color: #7dd3fc;
}

.add-btn {
  margin-top: 30px;
  background-color: #0ea5e9;
  color: #0f172a;
  padding: 12px 20px;
  border: none;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: 0.2s ease;
}

.add-btn:hover {
  background-color: #38bdf8;
}
</style>
