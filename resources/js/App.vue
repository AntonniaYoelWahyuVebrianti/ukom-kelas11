<template>
  <div class="bg-gray-50 min-h-screen text-gray-900">
    <Navbar />
    <HomeView />
    <ReviewSection />
    <FooterSection />
  </div>
</template>

<script setup>
import Navbar from './components/Navbar.vue'
import HomeView from '../views/HomeView.vue'
import ReviewSection from './components/ReviewSection.vue'
import FooterSection from './components/FooterSection.vue'
</script>
