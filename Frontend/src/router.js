// src/router.js
import { createRouter, createWebHistory } from 'vue-router'
import LandingPage from './components/HelloWorld.vue'
import LoginPage from './components/LoginPage.vue'
import SignupPage from './components/SignupPage.vue'
import DashboardPage from './components/DashboardPage.vue'
import AddPasswordPage from './components/AddPasswordPage.vue'

const routes = [
  { path: '/', component: LandingPage },
  { path: '/login', component: LoginPage },
  { path: '/signup', component: SignupPage },
  { path: '/dashboard', component: DashboardPage },
  { path: '/add-password', component: AddPasswordPage }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
