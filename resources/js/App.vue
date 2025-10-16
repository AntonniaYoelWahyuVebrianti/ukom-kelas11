<template>
  <div class="bg-gray-50 min-h-screen text-gray-900 flex flex-col">
    <Navbar />
    <main class="flex-1">
      <RouterView />
    </main>
    <FooterSection />
  </div>
</template>

<script setup>
import { RouterView } from 'vue-router'
import Navbar from './components/Navbar.vue'
import FooterSection from './components/FooterSection.vue'
</script>
