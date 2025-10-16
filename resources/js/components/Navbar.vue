<template>
  <nav class="flex justify-between items-center px-8 py-4 bg-white shadow-sm sticky top-0 z-50">
    <h1 class="text-2xl font-bold text-pink-600 tracking-wide">K-Wave</h1>
    <ul class="flex space-x-6 text-gray-600 font-medium items-center">
      <li><a href="#" class="hover:text-pink-500">Home</a></li>
      <li><a href="#" class="hover:text-pink-500">Shop</a></li>
      <li><a href="#" class="hover:text-pink-500">About</a></li>
      <li><a href="#" class="hover:text-pink-500">Contact</a></li>

      <!-- User dropdown -->
      <li class="relative">
        <button @click="toggleDropdown" class="flex items-center space-x-2 hover:text-pink-500 focus:outline-none">
          <!-- Heroicons user icon -->
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M5.121 17.804A9 9 0 1118.878 6.196 9 9 0 015.12 17.804zM15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
          <span v-if="user">{{ user.name }}</span>
        </button>

        <ul v-show="dropdownOpen" class="absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg py-1 z-50">
          <template v-if="!user">
            <li>
              <a href="/login" class="block px-4 py-2 hover:bg-pink-100">Login</a>
            </li>
            <li>
              <a href="/register" class="block px-4 py-2 hover:bg-pink-100">Register</a>
            </li>
          </template>
          <template v-else>
            <li>
              <button @click="logout" class="w-full text-left px-4 py-2 hover:bg-pink-100">Logout</button>
            </li>
          </template>
        </ul>
      </li>
    </ul>
  </nav>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      user: window.Laravel?.user || null,
      dropdownOpen: false,
    }
  },
  methods: {
    toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen;
    },
    async logout() {
      await axios.post('/logout');
      this.user = null;
      window.location.href = '/login';
    }
  },
  mounted() {
    document.addEventListener('click', (e) => {
      if (!this.$el.contains(e.target)) {
        this.dropdownOpen = false;
      }
    });
  }
}
</script>
