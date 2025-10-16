<template>
  <nav class="flex justify-between items-center px-6 md:px-10 py-4 bg-white/95 backdrop-blur shadow-sm sticky top-0 z-50">
    <h1 class="text-2xl font-bold text-sky-600 tracking-wide">K-Wave</h1>
    <ul class="flex items-center space-x-4 md:space-x-6 text-gray-600 font-medium">
  <li><router-link to="/" class="hover:text-sky-500 transition-colors">Home</router-link></li>
      <li><a href="#" class="hover:text-sky-500 transition-colors">Shop</a></li>
      <li><a href="#" class="hover:text-sky-500 transition-colors">About</a></li>
      <li><a href="#" class="hover:text-sky-500 transition-colors">Contact</a></li>
      <li v-if="isAdmin">
        <router-link
          to="/admin"
          class="rounded-full bg-gradient-to-r from-sky-500/90 to-emerald-500/90 px-4 py-2 text-sm font-semibold text-white shadow transition hover:from-sky-400 hover:to-emerald-400"
        >
          Admin
        </router-link>
      </li>

      <li class="relative">
        <button
          type="button"
          class="relative flex items-center justify-center rounded-full border border-sky-100 bg-white/80 p-2 text-sky-600 shadow-sm transition hover:border-sky-200 hover:text-sky-700 focus:outline-none"
          @click="openCart"
          title="View cart"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l3-8H5.4M7 13l-1.293 3.879A1 1 0 006.675 18h10.65a1 1 0 00.967-.707L20 13M7 13l1.5-4.5M5 21a1 1 0 102 0M17 21a1 1 0 102 0" />
          </svg>
          <span
            v-if="itemCount"
            class="absolute -top-1 -right-1 flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-semibold text-white shadow"
          >
            {{ itemCount }}
          </span>
        </button>
      </li>

      <!-- Profile icon for both logged in and not logged in -->
      <li class="relative">
        <button
          @click.stop="toggleDropdown"
          class="group flex items-center gap-2 focus:outline-none"
          :title="user ? 'Account menu' : 'Login or Register'"
        >
          <div
            class="flex items-center gap-3 px-3 py-2 rounded-full border border-sky-100 bg-white/80 shadow-sm transition-all duration-200 group-hover:shadow-md group-hover:border-sky-200"
          >
            <div class="relative">
              <!-- Profile photo when available -->
              <span
                v-if="userAvatar"
                class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full border border-white/70 bg-white shadow-sm"
              >
                <img :src="userAvatar" alt="User avatar" class="h-full w-full object-cover" referrerpolicy="no-referrer">
              </span>
              <!-- Profile icon when logged in without avatar -->
              <span
                v-else-if="user"
                class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-sky-500 to-blue-500 text-sm font-semibold text-white uppercase shadow-inner"
              >
                {{ userInitials }}
              </span>
              <!-- Generic user icon when not logged in -->
              <span
                v-else
                class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-gray-400 to-gray-500 text-white shadow-inner"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </span>
              <span
                v-if="user"
                class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full bg-emerald-400 border-2 border-white"
              ></span>
            </div>
            <div class="hidden sm:flex flex-col text-left">
              <span v-if="user" class="text-sm font-semibold text-gray-800">{{ userName }}</span>
              <span v-else class="text-sm font-semibold text-gray-800">Account</span>
              <span class="text-xs text-gray-500">{{ user ? 'View profile' : 'Login or Register' }}</span>
            </div>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 text-gray-400 transition-transform duration-200"
              :class="{ 'rotate-180 text-sky-500': dropdownOpen }"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </div>
        </button>

        <!-- Dropdown for not logged in users -->
        <transition name="fade-scale">
          <ul
            v-if="dropdownOpen && !user"
            class="absolute right-0 mt-3 w-64 overflow-hidden rounded-2xl bg-white text-sm shadow-xl ring-1 ring-black/5 z-50"
          >
            <li class="px-4 py-3 bg-gray-50/70 border-b border-gray-100">
              <p class="text-sm font-semibold text-gray-800">Welcome to K-Wave</p>
              <p class="text-xs text-gray-500 mt-1">Login or create an account</p>
            </li>
            
            <!-- Google Login -->
            <li class="px-3 py-2">
              <a
                href="/auth/google"
                class="w-full flex items-center justify-center gap-2 border border-gray-200 rounded-xl py-2.5 px-4 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
              >
                <svg class="h-5 w-5" viewBox="0 0 533.5 544.3" xmlns="http://www.w3.org/2000/svg">
                  <path d="M533.5 278.4c0-17.4-1.6-34.1-4.7-50.4H272v95.3h147.3c-6.4 34.6-25.7 63.9-54.7 83.6v69.4h88.4c51.7-47.6 80.5-117.8 80.5-198.2z" fill="#4285f4"/>
                  <path d="M272 544.3c73.5 0 135.2-24.4 180.2-66.1l-88.4-69.4c-24.6 16.5-56.1 26.2-91.8 26.2-70.6 0-130.4-47.7-151.8-111.6h-92.9v70.4C72.5 490.6 165.1 544.3 272 544.3z" fill="#34a853"/>
                  <path d="M120.2 323.4c-5.7-16.5-9-34.1-9-52.1s3.3-35.6 9-52.1v-70.4h-92.9C9.5 195 0 232.5 0 271.3s9.5 76.3 27.3 109.5l92.9-70.4z" fill="#fbbc04"/>
                  <path d="M272 107.7c40 0 76 13.7 104.3 40.4l78.1-78.1C407 24.6 345.3 0 272 0 165.1 0 72.5 53.7 27.3 161.8l92.9 70.4C141.6 155.4 201.4 107.7 272 107.7z" fill="#ea4335"/>
                </svg>
                Continue with Google
              </a>
            </li>

            <li class="px-4 py-2">
              <div class="flex items-center gap-3">
                <div class="flex-1 h-px bg-gray-200"></div>
                <span class="text-xs text-gray-400 uppercase">Or</span>
                <div class="flex-1 h-px bg-gray-200"></div>
              </div>
            </li>
            
            <li>
              <a
                href="/login"
                class="flex items-center gap-2 px-4 py-2.5 text-gray-700 transition-colors hover:bg-sky-50 hover:text-sky-600"
                @click="dropdownOpen = false"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                Login with email
              </a>
            </li>
            <li>
              <a
                href="/register"
                class="flex items-center gap-2 px-4 py-2.5 text-gray-700 transition-colors hover:bg-sky-50 hover:text-sky-600"
                @click="dropdownOpen = false"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Create new account
              </a>
            </li>
            <li class="border-t border-gray-100 px-4 py-3 bg-sky-50/30">
              <p class="text-xs text-gray-600">
                <strong>New to K-Wave?</strong> Get exclusive access to merch drops and fan events!
              </p>
            </li>
          </ul>
        </transition>

        <!-- Dropdown for logged in users -->
        <transition name="fade-scale">
          <ul
            v-if="dropdownOpen && user"
            class="absolute right-0 mt-3 w-56 overflow-hidden rounded-2xl bg-white text-sm shadow-xl ring-1 ring-black/5 z-50"
          >
            <li class="px-4 py-3 bg-gray-50/70 border-b border-gray-100">
              <p class="text-xs uppercase tracking-wide text-gray-500">Signed in as</p>
              <p class="mt-1 text-sm font-semibold text-gray-800">{{ userName }}</p>
            </li>
            <li>
              <router-link to="/profile/addresses" class="block px-4 py-2.5 text-gray-600 hover:bg-sky-50 hover:text-sky-600 transition-colors" @click="dropdownOpen = false">Profile</router-link>
            </li>
            <li>
              <router-link to="/cart" class="block px-4 py-2.5 text-gray-600 hover:bg-sky-50 hover:text-sky-600 transition-colors" @click="dropdownOpen = false">Cart</router-link>
            </li>
            <li>
              <router-link to="/checkout" class="block px-4 py-2.5 text-gray-600 hover:bg-sky-50 hover:text-sky-600 transition-colors" @click="dropdownOpen = false">Checkout</router-link>
            </li>
            <li v-if="isAdmin">
              <router-link to="/admin" class="block px-4 py-2.5 text-gray-600 hover:bg-sky-50 hover:text-sky-600 transition-colors" @click="dropdownOpen = false">Admin dashboard</router-link>
            </li>
            <li class="border-t border-gray-100">
              <button
                @click="logout"
                class="w-full text-left px-4 py-2.5 text-red-600 hover:bg-red-50 transition-colors"
              >
                Logout
              </button>
            </li>
          </ul>
        </transition>
      </li>
    </ul>
  </nav>

</template>

<script>
import axios from 'axios';
import { useCart } from '../composables/useCart';
import { useRouter } from 'vue-router';

export default {
  setup() {
    const router = useRouter();
    const { itemCount, loadCart, claimCart } = useCart();

    return {
      router,
      itemCount,
      loadCart,
      claimCart,
    };
  },
  data() {
    return {
      user: window.Laravel?.user || null,
      dropdownOpen: false,
      clickOutsideHandler: null,
    }
  },
  computed: {
    userName() {
      return this.user && this.user.name ? this.user.name : '';
    },
    userAvatar() {
      return this.user && this.user.avatar ? this.user.avatar : '';
    },
    userInitials() {
      if (this.user && this.user.name) {
        return this.user.name
          .split(' ')
          .filter(Boolean)
          .map((namePart) => namePart[0])
          .join('')
          .slice(0, 2)
          .toUpperCase();
      }
      return '';
    },
    isAdmin() {
      return Boolean(this.user?.is_admin);
    }
  },
  methods: {
    toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen;
    },
    openCart() {
      this.dropdownOpen = false;
      this.router.push({ name: 'cart' });
    },
    async logout() {
      try {
        await axios.post('/api/logout');
        this.user = null;
        window.location.reload();
      } catch (error) {
        console.error('Logout failed:', error);
        // Fallback to regular logout
        window.location.href = '/login';
      }
    }
  },
  mounted() {
    // Delay adding click outside handler to avoid immediate close
    this.$nextTick(() => {
      this.clickOutsideHandler = (e) => {
        // Check if click is outside the nav element
        const navElement = this.$el.querySelector('nav');
        if (navElement && !navElement.contains(e.target)) {
          this.dropdownOpen = false;
        }
      };
      document.addEventListener('click', this.clickOutsideHandler);
    });

    this.loadCart(true);

    if (this.user) {
      Promise.resolve(this.claimCart())
        .finally(() => {
          this.loadCart(true);
        });
    }
  },
  beforeUnmount() {
    if (this.clickOutsideHandler) {
      document.removeEventListener('click', this.clickOutsideHandler);
    }
  }
}
</script>

<style scoped>
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: opacity 0.18s ease, transform 0.18s ease;
}

.fade-scale-enter-from,
.fade-scale-leave-to {
  opacity: 0;
  transform: translateY(6px) scale(0.98);
}
</style>
