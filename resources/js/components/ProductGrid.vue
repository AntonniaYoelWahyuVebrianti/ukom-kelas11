<template>
  <section class="py-16 px-6 bg-white">
    <div class="flex items-center justify-between flex-wrap gap-4 mb-10">
      <div>
        <h2 class="text-3xl font-bold text-gray-800">Merch Store</h2>
        <p class="text-sm text-gray-500 mt-1">Pick your favorites and save them to the cart.</p>
      </div>
      <div v-if="feedback" class="rounded-full bg-emerald-100 px-4 py-1 text-sm text-emerald-700">
        {{ feedback }}
      </div>
    </div>

    <div class="flex flex-wrap justify-center gap-3 mb-10">
      <button
        v-for="cat in categories"
        :key="cat"
        @click="selectedCategory = cat"
        class="px-5 py-2 rounded-full border text-sm font-medium transition-all duration-300"
        :class="selectedCategory === cat
          ? 'bg-sky-600 text-white border-sky-600'
          : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
      >
        {{ cat }}
      </button>
    </div>

    <div v-if="loading" class="flex justify-center py-20">
      <span class="text-sm text-gray-500 animate-pulse">Loading products…</span>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
      <div
        v-for="product in filteredProducts"
        :key="product.id"
        class="bg-white rounded-2xl shadow-md overflow-hidden flex flex-col items-center transition-transform duration-300 hover:-translate-y-2 hover:shadow-lg"
      >
        <div class="w-full h-60 bg-gray-100 flex items-center justify-center overflow-hidden">
          <img
            :src="product.image_url || product.image"
            :alt="product.name"
            class="object-cover w-full h-full transition-transform duration-500 hover:scale-105"
          />
        </div>

        <div class="flex flex-col flex-1 justify-between w-full p-5 text-center">
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1 truncate">{{ product.name }}</h3>
            <p class="text-sm text-gray-500 mb-2">{{ displayCategory(product) }}</p>
          </div>

          <p class="text-lg font-bold text-sky-600 mb-4">{{ formatPrice(product.price) }}</p>

          <button
            @click="handleAdd(product)"
            :disabled="cartLoading || (product.stock !== undefined && product.stock < 1)"
            class="bg-sky-600 text-white py-2 px-5 rounded-full text-sm font-medium transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed"
          >
            {{ product.stock !== undefined && product.stock < 1 ? 'Out of stock' : 'Add to Cart' }}
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import axios from 'axios'
import { useCart } from '../composables/useCart'

const selectedCategory = ref('All')
const products = ref([])
const loading = ref(false)
const feedback = ref('')
const { addToCart, loading: cartLoading, loadCart } = useCart()

const categories = computed(() => {
  const unique = new Set(products.value.map(product => product.category || 'Merch'))
  return ['All', ...Array.from(unique)]
})

const filteredProducts = computed(() => {
  if (selectedCategory.value === 'All') {
    return products.value
  }

  return products.value.filter(product => (product.category || 'Merch') === selectedCategory.value)
})

function formatPrice(value) {
  const amount = Number.parseInt(value ?? 0, 10) / 100
  return amount.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
}

async function fetchProducts() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/products')
    products.value = data?.data ?? data ?? []
  } catch (error) {
    console.error('Failed to load products', error)
  } finally {
    loading.value = false
  }
}

function displayCategory(product) {
  return product.category_name || product.category || 'Merch'
}

async function handleAdd(product) {
  if (product.stock !== undefined && product.stock < 1) {
    feedback.value = 'Produk sedang habis. Coba lagi nanti ya!'
    return
  }

  try {
    await addToCart(product.id, 1)
    feedback.value = `${product.name} masuk ke cart!`
    setTimeout(() => {
      feedback.value = ''
    }, 3000)
  } catch (error) {
    const message = error?.response?.data?.message || 'Gagal menambahkan ke cart.'
    feedback.value = message
  }
}

onMounted(async () => {
  await Promise.all([fetchProducts(), loadCart()])
})
</script>
