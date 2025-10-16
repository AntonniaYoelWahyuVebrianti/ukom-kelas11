<template>
  <section class="bg-white py-20 px-6 text-center overflow-hidden">
    <h2 class="text-4xl font-bold mb-6 text-gray-900">
      What Our <span class="text-sky-600">Fans</span> Say 💬
    </h2>
    <p class="text-gray-600 mb-12 max-w-2xl mx-auto">
      Real reviews from real K-Pop lovers around the world 🌍
    </p>

    <!-- Carousel Container -->
    <div class="relative max-w-6xl mx-auto">
      <div
        ref="carousel"
        class="flex gap-6 overflow-x-auto scroll-smooth no-scrollbar transition-all duration-700"
      >
        <div
          v-for="(review, index) in reviews"
          :key="index"
          class="flex-shrink-0 w-80 bg-gradient-to-br from-sky-50 to-indigo-50 p-6 rounded-2xl shadow-md"
        >
          <div class="flex justify-center mb-4">
            <img
              :src="review.avatar"
              alt="User avatar"
              class="w-20 h-20 rounded-full border-2 border-sky-400 object-cover"
            />
          </div>
          <h3 class="font-semibold text-lg text-gray-800 mb-2">
            {{ review.name }}
          </h3>
          <p class="text-sky-600 text-sm mb-2">⭐ {{ review.rating }}/5</p>
          <p class="text-gray-700 text-sm italic">"{{ review.comment }}"</p>
        </div>
      </div>

      <!-- Navigation Buttons -->
      <button
        @click="scrollLeft"
        class="absolute left-0 top-1/2 -translate-y-1/2 bg-sky-500 text-white p-2 rounded-full hover:bg-sky-600 transition"
      >
        <i class="fas fa-chevron-left"></i>
      </button>
      <button
        @click="scrollRight"
        class="absolute right-0 top-1/2 -translate-y-1/2 bg-sky-500 text-white p-2 rounded-full hover:bg-sky-600 transition"
      >
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

const carousel = ref(null)
const scrollAmount = 320 // jarak scroll per kali geser
let interval = null

const reviews = ref([
  {
    name: 'Soojin Park',
    rating: 5,
    comment:
      'Produk original dan pengiriman super cepat! Aku beli lightstick BTS dan semuanya sempurna 💜',
    avatar: 'https://i.pravatar.cc/150?img=47',
  },
  {
    name: 'Nathalie Kim',
    rating: 4.8,
    comment:
      'Albumnya dikemas rapi dan dapet photocard bonus! Bakal belanja lagi pasti 💖',
    avatar: 'https://i.pravatar.cc/150?img=32',
  },
  {
    name: 'Alya Rahman',
    rating: 5,
    comment:
      'Toko ini vibes-nya kayak official K-Pop store! Sukaaa banget konsepnya 😍',
    avatar: 'https://i.pravatar.cc/150?img=12',
  },
  {
    name: 'Minji Lee',
    rating: 4.9,
    comment:
      'Harga bersahabat dan pengiriman cepat banget! Recomended store 💕',
    avatar: 'https://i.pravatar.cc/150?img=55',
  },
  {
    name: 'Jisoo Han',
    rating: 5,
    comment:
      'Desain toko super lucu dan pelayanan cepat banget, serasa belanja di Seoul! 💫',
    avatar: 'https://i.pravatar.cc/150?img=22',
  },
  {
    name: 'Hana Lim',
    rating: 4.7,
    comment:
      'Beli album aespa, dapet bonus photocard! Pokoknya worth it banget 😍',
    avatar: 'https://i.pravatar.cc/150?img=15',
  },
])

// Geser otomatis setiap 4 detik
onMounted(() => {
  startAutoScroll()
})

onBeforeUnmount(() => {
  stopAutoScroll()
})

const startAutoScroll = () => {
  interval = setInterval(() => {
    scrollRight()
  }, 4000)
}

const stopAutoScroll = () => {
  clearInterval(interval)
}

const scrollRight = () => {
  if (!carousel.value) return
  carousel.value.scrollBy({ left: scrollAmount, behavior: 'smooth' })

  // kalau udah di ujung, balik ke awal
  if (
    carousel.value.scrollLeft + carousel.value.clientWidth >=
    carousel.value.scrollWidth
  ) {
    setTimeout(() => {
      carousel.value.scrollTo({ left: 0, behavior: 'smooth' })
    }, 600)
  }
}

const scrollLeft = () => {
  if (!carousel.value) return
  carousel.value.scrollBy({ left: -scrollAmount, behavior: 'smooth' })
}
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
