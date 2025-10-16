<template>
  <section class="text-center py-20 bg-gradient-to-br from-pink-100 to-purple-200">
    <h1 class="text-5xl font-bold mb-4 text-gray-900">
      Feel The <span class="text-pink-600">K-Wave</span> Energy 💖
    </h1>
    <p class="text-gray-600 mb-8 max-w-xl mx-auto">
      Discover exclusive K-Pop albums, lightsticks, and merchandise inspired by your favorite idols.
    </p>
    <button class="bg-pink-600 text-white px-6 py-3 rounded-full text-lg hover:bg-pink-700 transition">
      Shop Now
    </button>
  </section>

  <ProductGrid />
</template>

<script setup>
import ProductGrid from '../js/components/ProductGrid.vue'
</script>
