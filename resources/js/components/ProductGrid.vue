<template>
  <section class="py-16 px-6 bg-white">
    <h2 class="text-3xl font-bold text-center mb-10 text-gray-800">Our Products</h2>

    <!-- Filter -->
    <div class="flex flex-wrap justify-center gap-3 mb-10">
      <button
        v-for="cat in categories"
        :key="cat"
        @click="selectedCategory = cat"
        class="px-5 py-2 rounded-full border text-sm font-medium transition-all duration-300"
        :class="selectedCategory === cat
          ? 'bg-pink-600 text-white border-pink-600'
          : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
      >
        {{ cat }}
      </button>
    </div>

    <!-- Product Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
      <div
        v-for="product in filteredProducts"
        :key="product.id"
        class="bg-white rounded-2xl shadow-md overflow-hidden flex flex-col items-center transition-transform duration-300 hover:-translate-y-2 hover:shadow-lg"
      >
        <!-- Image -->
        <div class="w-full h-60 bg-gray-100 flex items-center justify-center overflow-hidden">
          <img
            :src="product.image"
            :alt="product.name"
            class="object-cover w-full h-full transition-transform duration-500 hover:scale-105"
          />
        </div>

        <!-- Info -->
        <div class="flex flex-col flex-1 justify-between w-full p-5 text-center">
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1 truncate">{{ product.name }}</h3>
            <p class="text-sm text-gray-500 mb-2">{{ product.category }}</p>
          </div>

          <!-- Price -->
          <p class="text-lg font-bold text-pink-600 mb-4">Rp {{ product.price.toLocaleString() }}</p>

          <!-- Button -->
          <button
            class="bg-pink-600 text-white py-2 px-5 rounded-full text-sm font-medium hover:bg-pink-700 transition-all duration-300"
          >
            Add to Cart
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'

const categories = ['All', 'Album', 'Lightstick', 'Merch', 'Photocard']
const selectedCategory = ref('All')

const products = ref([
  {
    id: 1,
    name: 'SEVENTEEN – 17 Is Right Here',
    category: 'Album',
    price: 420000,
    image: '/images/Face The Sun-ep.1 Control.jpg'
  },
  {
    id: 2,
    name: 'BLACKPINK Official Lightstick Ver.2',
    category: 'Lightstick',
    price: 850000,
    image: 'https://i.pinimg.com/736x/55/df/38/55df38454d448ac33e2e2f1cd9c6fc26.jpg'
  },
  {
    id: 3,
    name: 'BTS Merch Hoodie',
    category: 'Merch',
    price: 500000,
    image: 'https://i.pinimg.com/736x/ef/09/73/ef09735dd71a5d9cbfe8a61ff79f22c1.jpg'
  },
  {
    id: 4,
    name: 'NCT Dream Photocard Set',
    category: 'Photocard',
    price: 150000,
    image: 'https://i.pinimg.com/736x/94/a3/63/94a363a79c8e73f10a9782c12a04a46c.jpg'
  },
  {
    id: 5,
    name: 'TWICE Ready To Be Album',
    category: 'Album',
    price: 410000,
    image: 'https://i.pinimg.com/736x/8f/f2/41/8ff24144f387bce61b24d31763f9eb1f.jpg'
  },
  {
    id: 6,
    name: 'ENHYPEN Official Lightstick',
    category: 'Lightstick',
    price: 780000,
    image: 'https://i.pinimg.com/736x/48/9e/b8/489eb8aef435b1db97b5f9ef7f5d4bcb.jpg'
  },
  {
    id: 7,
    name: 'Stray Kids Sweatshirt',
    category: 'Merch',
    price: 530000,
    image: 'https://i.pinimg.com/736x/4a/b1/03/4ab103e55b1ac5117b80d3e7e86f1cc2.jpg'
  },
  {
    id: 8,
    name: 'IVE Photocard Collection',
    category: 'Photocard',
    price: 160000,
    image: 'https://i.pinimg.com/736x/69/88/bf/6988bf84b5b4da178a099d0c1906c5c8.jpg'
  }
])

const filteredProducts = computed(() =>
  selectedCategory.value === 'All'
    ? products.value
    : products.value.filter(p => p.category === selectedCategory.value)
)
</script>
