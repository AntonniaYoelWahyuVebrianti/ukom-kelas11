<template>
  <transition name="modal">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex min-h-full items-center justify-center overflow-y-auto px-4 py-12 sm:py-16 bg-black/50 backdrop-blur-sm"
      @click.self="close"
    >
      <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 animate-modal-in">
        <button
          @click="close"
          class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 transition"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <div class="mb-6">
          <h2 class="text-2xl font-bold text-gray-900">Create Account</h2>
          <p class="text-sm text-gray-500 mt-1">Join the K-Wave community</p>
        </div>

        <div v-if="error" class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          {{ error }}
        </div>

        <div v-if="success" class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
          {{ success }}
        </div>

        <!-- Google Sign Up Button -->
        <a
          href="/auth/google"
          class="w-full flex items-center justify-center gap-3 border border-sky-200 rounded-2xl py-3 text-sm font-semibold text-sky-700 bg-white hover:bg-sky-50 transition mb-4"
        >
          <svg class="h-5 w-5" viewBox="0 0 533.5 544.3" xmlns="http://www.w3.org/2000/svg">
            <path d="M533.5 278.4c0-17.4-1.6-34.1-4.7-50.4H272v95.3h147.3c-6.4 34.6-25.7 63.9-54.7 83.6v69.4h88.4c51.7-47.6 80.5-117.8 80.5-198.2z" fill="#4285f4"/>
            <path d="M272 544.3c73.5 0 135.2-24.4 180.2-66.1l-88.4-69.4c-24.6 16.5-56.1 26.2-91.8 26.2-70.6 0-130.4-47.7-151.8-111.6h-92.9v70.4C72.5 490.6 165.1 544.3 272 544.3z" fill="#34a853"/>
            <path d="M120.2 323.4c-5.7-16.5-9-34.1-9-52.1s3.3-35.6 9-52.1v-70.4h-92.9C9.5 195 0 232.5 0 271.3s9.5 76.3 27.3 109.5l92.9-70.4z" fill="#fbbc04"/>
            <path d="M272 107.7c40 0 76 13.7 104.3 40.4l78.1-78.1C407 24.6 345.3 0 272 0 165.1 0 72.5 53.7 27.3 161.8l92.9 70.4C141.6 155.4 201.4 107.7 272 107.7z" fill="#ea4335"/>
          </svg>
          Sign up with Google
        </a>

        <div class="flex items-center gap-3 mb-4">
          <div class="flex-1 h-px bg-gray-200"></div>
          <span class="text-xs text-gray-400 uppercase">Or sign up with email</span>
          <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        <form @submit.prevent="handleRegister" class="space-y-4">
          <div class="space-y-2">
            <label for="modal-name" class="block text-sm font-medium text-gray-700">Full Name</label>
            <input
              id="modal-name"
              v-model="form.name"
              type="text"
              required
              class="w-full rounded-2xl border border-sky-100 bg-white px-4 py-3 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200"
              placeholder="Your name"
              :disabled="loading"
            >
          </div>

          <div class="space-y-2">
            <label for="modal-register-email" class="block text-sm font-medium text-gray-700">Email</label>
            <div class="relative">
              <input
                id="modal-register-email"
                v-model="form.email"
                type="email"
                required
                class="w-full rounded-2xl border border-sky-100 bg-white px-4 py-3 pr-28 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200"
                placeholder="you@example.com"
                :disabled="loading || otpSent"
              >
              <button
                type="button"
                @click="sendOtp"
                :disabled="loading || otpSent || !form.email"
                class="absolute inset-y-1 right-1 rounded-2xl bg-sky-600 px-3 text-xs font-semibold uppercase tracking-wide text-white hover:bg-sky-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ otpSent ? 'Sent' : 'Send OTP' }}
              </button>
            </div>
            <p class="text-xs text-gray-500">A 6-digit code will be sent to your email</p>
          </div>

          <div class="space-y-2">
            <label for="modal-otp" class="block text-sm font-medium text-gray-700">OTP Code</label>
            <input
              id="modal-otp"
              v-model="form.otp"
              type="text"
              inputmode="numeric"
              pattern="[0-9]{6}"
              maxlength="6"
              required
              class="w-full rounded-2xl border border-sky-100 bg-white px-4 py-3 text-sm tracking-[0.6em] text-center focus:border-sky-400 focus:ring-2 focus:ring-sky-200"
              placeholder="000000"
              :disabled="loading"
            >
          </div>

          <div class="space-y-2">
            <label for="modal-reg-password" class="block text-sm font-medium text-gray-700">Password</label>
            <input
              id="modal-reg-password"
              v-model="form.password"
              type="password"
              required
              class="w-full rounded-2xl border border-sky-100 bg-white px-4 py-3 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200"
              placeholder="Create a secure password"
              :disabled="loading"
            >
          </div>

          <div class="space-y-2">
            <label for="modal-password-confirm" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input
              id="modal-password-confirm"
              v-model="form.password_confirmation"
              type="password"
              required
              class="w-full rounded-2xl border border-sky-100 bg-white px-4 py-3 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200"
              placeholder="Repeat password"
              :disabled="loading"
            >
          </div>

          <div class="flex items-start gap-3 text-sm text-gray-600">
            <input
              id="modal-terms"
              v-model="form.terms"
              type="checkbox"
              required
              class="mt-1 rounded border-sky-200 text-sky-600 focus:ring-sky-400"
            >
            <label for="modal-terms">
              I agree to the <a href="#" class="font-semibold text-sky-600 hover:text-sky-700">Terms of Service</a> and 
              <a href="#" class="font-semibold text-sky-600 hover:text-sky-700">Privacy Policy</a>.
            </label>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full rounded-2xl bg-sky-600 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/20 hover:bg-sky-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? 'Creating account...' : 'Create Account' }}
          </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
          Already have an account?
          <button @click="switchToLogin" class="font-semibold text-sky-600 hover:text-sky-700">Login here</button>
        </p>
      </div>
    </div>
  </transition>
</template>

<script>
import axios from 'axios';

export default {
  props: {
    isOpen: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      form: {
        name: '',
        email: '',
        otp: '',
        password: '',
        password_confirmation: '',
        terms: false
      },
      loading: false,
      otpSent: false,
      error: null,
      success: null
    }
  },
  methods: {
    close() {
      this.$emit('close');
      this.resetForm();
    },
    resetForm() {
      this.form = {
        name: '',
        email: '',
        otp: '',
        password: '',
        password_confirmation: '',
        terms: false
      };
      this.otpSent = false;
      this.error = null;
      this.success = null;
    },
    switchToLogin() {
      this.$emit('switch-to-login');
      this.close();
    },
    async sendOtp() {
      if (!this.form.email) {
        this.error = 'Please enter your email address';
        return;
      }

      this.loading = true;
      this.error = null;

      try {
        await axios.post('/register/otp', { email: this.form.email });
        this.otpSent = true;
        this.success = 'OTP sent to your email!';
        setTimeout(() => {
          this.success = null;
        }, 3000);
      } catch (err) {
        if (err.response && err.response.data) {
          this.error = err.response.data.message || 'Failed to send OTP. Please try again.';
        } else {
          this.error = 'An error occurred. Please try again.';
        }
      } finally {
        this.loading = false;
      }
    },
    async handleRegister() {
      this.loading = true;
      this.error = null;

      try {
        const response = await axios.post('/api/register', this.form);
        
        if (response.data.user) {
          // Update global user state
          window.Laravel.user = response.data.user;
          // Emit success event
          this.$emit('register-success', response.data.user);
          // Close modal
          this.close();
          // Reload page to update navbar
          window.location.reload();
        }
      } catch (err) {
        if (err.response && err.response.data) {
          this.error = err.response.data.message || 'Registration failed. Please try again.';
          if (err.response.data.errors) {
            const errors = Object.values(err.response.data.errors).flat();
            this.error = errors.join(' ');
          }
        } else {
          this.error = 'An error occurred. Please try again.';
        }
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

@keyframes modal-in {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(10px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.animate-modal-in {
  animation: modal-in 0.3s ease-out;
}
</style>
