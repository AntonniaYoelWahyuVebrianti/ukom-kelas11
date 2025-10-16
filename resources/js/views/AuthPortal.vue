<template>
  <div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-sky-100 via-white to-indigo-100 text-gray-900">
    <div class="pointer-events-none absolute -top-32 left-1/4 h-72 w-72 rounded-full bg-sky-200/40 blur-3xl"></div>
    <div class="pointer-events-none absolute bottom-0 right-0 h-96 w-96 rounded-full bg-indigo-200/40 blur-3xl"></div>
    <div class="relative z-10 flex min-h-screen flex-col">
      <header class="flex items-center justify-between px-6 py-6 sm:px-10">
        <a href="/" class="flex items-center gap-3 text-lg font-semibold text-sky-700">
          <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/80 shadow-sm">
            KW
          </span>
          <span>K-Wave</span>
        </a>
        <a href="/" class="hidden sm:inline-flex items-center gap-2 rounded-full border border-sky-200 bg-white/70 px-4 py-2 text-sm font-medium text-sky-700 shadow-sm transition hover:bg-white">
          <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400"></span>
          Back to homepage
        </a>
      </header>

      <main class="flex flex-1 items-center justify-center px-6 pb-12 sm:px-10">
        <div class="w-full max-w-6xl">
          <div class="grid gap-8 overflow-hidden rounded-3xl border border-white/60 bg-white/75 backdrop-blur shadow-2xl lg:grid-cols-[0.95fr_1.05fr]">
            <aside class="hidden flex-col justify-between border-r border-white/50 bg-gradient-to-br from-sky-500 via-indigo-500 to-blue-500 p-10 text-white lg:flex">
              <div class="space-y-8">
                <div>
                  <p class="text-sm font-medium uppercase tracking-[0.3em] text-white/70">K-Wave Access</p>
                  <h1 class="mt-4 text-4xl font-bold leading-tight">Your fandom passport just went premium.</h1>
                  <p class="mt-4 text-sm text-white/80">
                    Control every drop, reward, and membership perk from one modern dashboard. Switch between login and sign up without leaving the page.
                  </p>
                </div>
                <ul class="space-y-5 text-sm text-white/80">
                  <li v-for="(highlight, index) in highlights" :key="highlight.title" class="flex items-start gap-3">
                    <span class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/40 bg-white/10 text-xs font-semibold">
                      {{ index + 1 < 10 ? `0${index + 1}` : index + 1 }}
                    </span>
                    <div>
                      <p class="font-semibold text-white">{{ highlight.title }}</p>
                      <p class="text-white/70">{{ highlight.description }}</p>
                    </div>
                  </li>
                </ul>
              </div>
              <dl class="grid grid-cols-2 gap-4 rounded-2xl border border-white/30 bg-white/10 p-6 text-left text-white/80">
                <div>
                  <dt class="text-xs uppercase tracking-wide text-white/60">Active members</dt>
                  <dd class="mt-2 text-2xl font-semibold">48K+</dd>
                </div>
                <div>
                  <dt class="text-xs uppercase tracking-wide text-white/60">Avg. drop sell-out</dt>
                  <dd class="mt-2 text-2xl font-semibold">12 min</dd>
                </div>
              </dl>
            </aside>

            <section class="relative flex flex-col gap-8 p-8 sm:p-12" aria-live="polite">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs font-semibold uppercase tracking-[0.3em] text-sky-500">K-Wave Account</p>
                  <h2 class="mt-3 text-3xl font-bold text-gray-900">
                    {{ activeTab === 'login' ? 'Welcome back, star!' : 'Create your all-access pass' }}
                  </h2>
                </div>
                <a href="/auth/google" class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-wide text-sky-700 shadow-sm transition hover:bg-sky-50">
                  <svg class="h-4 w-4" viewBox="0 0 533.5 544.3" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M533.5 278.4c0-17.4-1.6-34.1-4.7-50.4H272v95.3h147.3c-6.4 34.6-25.7 63.9-54.7 83.6v69.4h88.4c51.7-47.6 80.5-117.8 80.5-198.2z" fill="#4285f4" />
                    <path d="M272 544.3c73.5 0 135.2-24.4 180.2-66.1l-88.4-69.4c-24.6 16.5-56.1 26.2-91.8 26.2-70.6 0-130.4-47.7-151.8-111.6h-92.9v70.4C72.5 490.6 165.1 544.3 272 544.3z" fill="#34a853" />
                    <path d="M120.2 323.4c-5.7-16.5-9-34.1-9-52.1s3.3-35.6 9-52.1v-70.4h-92.9C9.5 195 0 232.5 0 271.3s9.5 76.3 27.3 109.5l92.9-70.4z" fill="#fbbc04" />
                    <path d="M272 107.7c40 0 76 13.7 104.3 40.4l78.1-78.1C407 24.6 345.3 0 272 0 165.1 0 72.5 53.7 27.3 161.8l92.9 70.4C141.6 155.4 201.4 107.7 272 107.7z" fill="#ea4335" />
                  </svg>
                  Continue with Google
                </a>
              </div>

              <div class="flex items-center gap-2 rounded-full bg-sky-50 p-1 text-sm font-semibold text-sky-600">
                <button
                  type="button"
                  @click="setActive('login')"
                  :class="['flex-1 rounded-full px-4 py-2 text-center transition', activeTab === 'login' ? 'bg-white shadow-sm text-sky-700' : 'text-sky-500/70']"
                >
                  Login
                </button>
                <button
                  type="button"
                  @click="setActive('register')"
                  :class="['flex-1 rounded-full px-4 py-2 text-center transition', activeTab === 'register' ? 'bg-white shadow-sm text-sky-700' : 'text-sky-500/70']"
                >
                  Register
                </button>
              </div>

              <transition name="fade-slide" mode="out-in">
                <form
                  key="login"
                  v-if="activeTab === 'login'"
                  @submit.prevent="handleLogin"
                  class="space-y-6"
                >
                  <div v-if="loginError" class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ loginError }}
                  </div>

                  <div class="space-y-2">
                    <label for="portal-login-email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <div class="relative">
                      <input
                        id="portal-login-email"
                        v-model="loginForm.email"
                        type="email"
                        required
                        class="w-full rounded-2xl border border-sky-100 bg-white px-4 py-3 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200"
                        placeholder="you@example.com"
                        :disabled="loginLoading"
                        autocomplete="email"
                      >
                      <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sky-300">
                        <i class="fa-regular fa-envelope"></i>
                      </span>
                    </div>
                  </div>

                  <div class="space-y-2">
                    <label for="portal-login-password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative">
                      <input
                        id="portal-login-password"
                        v-model="loginForm.password"
                        type="password"
                        required
                        class="w-full rounded-2xl border border-sky-100 bg-white px-4 py-3 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200"
                        placeholder="Enter your password"
                        :disabled="loginLoading"
                        autocomplete="current-password"
                      >
                      <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sky-300">
                        <i class="fa-solid fa-lock"></i>
                      </span>
                    </div>
                  </div>

                  <div class="flex items-center justify-between text-sm text-gray-600">
                    <label class="flex items-center gap-2">
                      <input
                        v-model="loginForm.remember"
                        type="checkbox"
                        class="rounded border-sky-200 text-sky-600 focus:ring-sky-400"
                        :disabled="loginLoading"
                      >
                      Remember me
                    </label>
                    <a href="#" class="font-medium text-sky-600 hover:text-sky-700">Forgot password?</a>
                  </div>

                  <button
                    type="submit"
                    class="w-full rounded-2xl bg-sky-600 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/20 transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="loginLoading"
                  >
                    {{ loginLoading ? 'Signing in…' : 'Login to account' }}
                  </button>

                  <p class="text-center text-sm text-gray-500">
                    Need an account?
                    <button type="button" @click="setActive('register')" class="font-semibold text-sky-600 hover:text-sky-700">
                      Register now
                    </button>
                  </p>
                </form>

                <form
                  key="register"
                  v-else
                  @submit.prevent="handleRegister"
                  class="space-y-6"
                >
                  <div v-if="registerError" class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ registerError }}
                  </div>

                  <div v-if="registerSuccess" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ registerSuccess }}
                  </div>

                  <div class="space-y-2">
                    <label for="portal-register-name" class="block text-sm font-medium text-gray-700">Full name</label>
                    <input
                      id="portal-register-name"
                      v-model="registerForm.name"
                      type="text"
                      required
                      class="w-full rounded-2xl border border-sky-100 bg-white px-4 py-3 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200"
                      placeholder="Your name"
                      :disabled="registerLoading"
                      autocomplete="name"
                    >
                  </div>

                  <div class="space-y-2">
                    <label for="portal-register-email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <div class="relative">
                      <input
                        id="portal-register-email"
                        v-model="registerForm.email"
                        type="email"
                        required
                        class="w-full rounded-2xl border border-sky-100 bg-white px-4 py-3 pr-32 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200"
                        placeholder="you@example.com"
                        :disabled="registerLoading"
                        autocomplete="email"
                      >
                      <button
                        type="button"
                        class="absolute inset-y-1 right-1 rounded-2xl bg-sky-600 px-4 text-xs font-semibold uppercase tracking-wide text-white shadow transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-50"
                        @click="sendOtp"
                        :disabled="otpLoading || !registerForm.email || registerLoading"
                      >
                        {{ otpLoading ? 'Sending…' : otpSent ? 'Resend OTP' : 'Send OTP' }}
                      </button>
                    </div>
                    <p class="text-xs text-gray-500">
                      A 6-digit verification code will be emailed to you instantly.
                    </p>
                  </div>

                  <div class="space-y-2">
                    <label for="portal-register-otp" class="block text-sm font-medium text-gray-700">OTP verification code</label>
                    <input
                      id="portal-register-otp"
                      v-model="registerForm.otp"
                      type="text"
                      inputmode="numeric"
                      pattern="[0-9]{6}"
                      maxlength="6"
                      required
                      class="w-full rounded-2xl border border-sky-100 bg-white px-4 py-3 text-sm tracking-[0.6em] text-center focus:border-sky-400 focus:ring-2 focus:ring-sky-200"
                      placeholder="000000"
                      :disabled="registerLoading"
                    >
                  </div>

                  <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                      <label for="portal-register-password" class="block text-sm font-medium text-gray-700">Password</label>
                      <input
                        id="portal-register-password"
                        v-model="registerForm.password"
                        type="password"
                        required
                        class="w-full rounded-2xl border border-sky-100 bg-white px-4 py-3 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200"
                        placeholder="Create a secure password"
                        :disabled="registerLoading"
                        autocomplete="new-password"
                      >
                    </div>
                    <div class="space-y-2">
                      <label for="portal-register-password-confirm" class="block text-sm font-medium text-gray-700">Confirm password</label>
                      <input
                        id="portal-register-password-confirm"
                        v-model="registerForm.password_confirmation"
                        type="password"
                        required
                        class="w-full rounded-2xl border border-sky-100 bg-white px-4 py-3 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200"
                        placeholder="Repeat password"
                        :disabled="registerLoading"
                        autocomplete="new-password"
                      >
                    </div>
                  </div>

                  <label class="flex items-start gap-3 text-sm text-gray-600">
                    <input
                      v-model="registerForm.terms"
                      type="checkbox"
                      class="mt-1 rounded border-sky-200 text-sky-600 focus:ring-sky-400"
                      :disabled="registerLoading"
                    >
                    <span>
                      I agree to the
                      <a href="#" class="font-semibold text-sky-600 hover:text-sky-700">Terms of Service</a>
                      and
                      <a href="#" class="font-semibold text-sky-600 hover:text-sky-700">Privacy Policy</a>.
                    </span>
                  </label>

                  <button
                    type="submit"
                    class="w-full rounded-2xl bg-sky-600 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/20 transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="registerLoading"
                  >
                    {{ registerLoading ? 'Creating account…' : 'Create account' }}
                  </button>

                  <p class="text-center text-sm text-gray-500">
                    Already have an account?
                    <button type="button" @click="setActive('login')" class="font-semibold text-sky-600 hover:text-sky-700">
                      Login instead
                    </button>
                  </p>
                </form>
              </transition>
            </section>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  initialMode: {
    type: String,
    default: 'login'
  }
})

const activeTab = ref(props.initialMode === 'register' ? 'register' : 'login')
const redirectQuery = (() => {
  try {
    const params = new URLSearchParams(window.location.search)
    return params.get('redirect') || ''
  } catch (error) {
    return ''
  }
})()
const loginForm = reactive({
  email: '',
  password: '',
  remember: false
})
const registerForm = reactive({
  name: '',
  email: '',
  otp: '',
  password: '',
  password_confirmation: '',
  terms: false
})
const loginLoading = ref(false)
const registerLoading = ref(false)
const otpLoading = ref(false)
const otpSent = ref(false)
const loginError = ref(null)
const registerError = ref(null)
const registerSuccess = ref(null)

const highlights = [
  {
    title: 'Skip the queue for limited drops',
    description: 'Reserve merch and concert tickets with priority access tailored to your bias list.'
  },
  {
    title: 'Unlock member-only rewards',
    description: 'Complete missions, earn badges, and redeem exclusive experiences each comeback.'
  },
  {
    title: 'Keep everything in sync',
    description: 'Track orders, wishlists, and fandom stats seamlessly across every device.'
  }
]

document.title = activeTab.value === 'register' ? 'Register | K-Wave' : 'Login | K-Wave'

watch(activeTab, (mode) => {
  const basePath = mode === 'register' ? '/register' : '/login'
  const params = new URLSearchParams()
  if (redirectQuery) {
    params.set('redirect', redirectQuery)
  }

  const composedUrl = params.toString() ? `${basePath}?${params.toString()}` : basePath

  if (window.location.pathname + window.location.search !== composedUrl) {
    window.history.replaceState({}, '', composedUrl)
  }
  document.title = mode === 'register' ? 'Register | K-Wave' : 'Login | K-Wave'

  if (mode === 'login') {
    registerError.value = null
    registerSuccess.value = null
  } else {
    loginError.value = null
  }
})

watch(() => registerForm.email, () => {
  otpSent.value = false
  registerError.value = null
  registerSuccess.value = null
})

function setActive(mode) {
  activeTab.value = mode
}

function parseError(error, fallback) {
  if (error?.response?.data) {
    const data = error.response.data
    if (data.errors) {
      const flat = Object.values(data.errors).flat()
      if (flat.length) {
        return flat.join(' ')
      }
    }
    if (data.message) {
      return data.message
    }
  }
  return fallback
}

async function handleLogin() {
  if (loginLoading.value) {
    return
  }

  loginLoading.value = true
  loginError.value = null

  try {
    const response = await axios.post('/api/login', loginForm)

    if (response.data?.user) {
      window.location.href = '/'
    }
  } catch (error) {
    loginError.value = parseError(error, 'Login failed. Please check your credentials and try again.')
  } finally {
    loginLoading.value = false
  }
}

async function sendOtp() {
  if (otpLoading.value) {
    return
  }

  if (!registerForm.email) {
    registerError.value = 'Please enter your email address before requesting an OTP.'
    return
  }

  otpLoading.value = true
  registerError.value = null
  registerSuccess.value = null

  try {
    const response = await axios.post('/register/otp', { email: registerForm.email })
    otpSent.value = true
    registerSuccess.value = response.data?.message || 'Verification code sent! Check your inbox.'
  } catch (error) {
    registerError.value = parseError(error, 'Failed to send OTP. Please try again.')
  } finally {
    otpLoading.value = false
  }
}

async function handleRegister() {
  if (registerLoading.value) {
    return
  }

  registerLoading.value = true
  registerError.value = null
  registerSuccess.value = null

  try {
    const response = await axios.post('/api/register', registerForm)

    if (response.data?.user) {
      registerSuccess.value = 'Registration successful! Redirecting you to your dashboard…'
      setTimeout(() => {
        window.location.href = '/'
      }, 600)
    }
  } catch (error) {
    registerError.value = parseError(error, 'Registration failed. Please review your details and try again.')
  } finally {
    registerLoading.value = false
  }
}
</script>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.25s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(8px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
