<template>
  <section class="text-center py-20 bg-gradient-to-br from-sky-100 to-indigo-200">
    <h1 class="text-5xl font-bold mb-4 text-gray-900">
      Feel The <span class="text-sky-600">K-Wave</span> Energy 💖
    </h1>
    <p class="text-gray-600 mb-8 max-w-xl mx-auto">
      Discover exclusive K-Pop albums, lightsticks, and merchandise inspired by your favorite idols.
    </p>
    <button class="bg-sky-600 text-white px-6 py-3 rounded-full text-lg hover:bg-sky-700 transition">
      Shop Now
    </button>
  </section>

  <ProductGrid />

  <ReviewSection />
</template>

<script setup>
import ProductGrid from '../js/components/ProductGrid.vue'
import ReviewSection from '../js/components/ReviewSection.vue'
</script>
