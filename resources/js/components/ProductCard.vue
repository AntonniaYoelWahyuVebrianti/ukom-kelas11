<template>
  <div class="group bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
    <img
      :src="product.image"
      :alt="product.name"
      class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300"
    />
    <div class="p-4 text-center">
      <h3 class="font-semibold text-lg mb-1">{{ product.name }}</h3>
      <p class="text-sm text-gray-500 mb-2">{{ product.category }}</p>
      <p class="font-bold text-sky-600 mb-3">Rp {{ product.price.toLocaleString() }}</p>
      <button class="bg-sky-600 text-white px-4 py-2 rounded-full hover:bg-sky-700 transition">
        Add to Cart
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  product: Object
})
</script>
