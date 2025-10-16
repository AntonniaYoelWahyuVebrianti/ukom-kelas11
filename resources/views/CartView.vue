<template>
  <section class="px-6 py-12">
    <div class="mx-auto flex max-w-4xl flex-col gap-8">
      <header class="flex flex-col gap-2">
        <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
        <p class="text-sm text-gray-500">Kelola barang yang sudah kamu tambahkan sebelum checkout.</p>
      </header>

      <div v-if="loading && !cart.items.length" class="flex h-48 items-center justify-center rounded-2xl border border-dashed border-sky-200 bg-sky-50/60 text-sm text-sky-600">
        Memuat cart…
      </div>

      <div v-else-if="!cart.items.length" class="flex flex-col items-center justify-center gap-4 rounded-2xl border border-dashed border-gray-200 bg-white px-8 py-16 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l3-8H5.4M7 13l-1.293 3.879A1 1 0 006.675 18h10.65a1 1 0 00.967-.707L20 13M7 13l1.5-4.5M19 21a1 1 0 11-2 0 1 1 0 012 0zm-10 0a1 1 0 11-2 0 1 1 0 012 0z" />
        </svg>
        <div>
          <p class="text-lg font-semibold text-gray-800">Keranjangmu masih kosong</p>
          <p class="text-sm text-gray-500">Cari produk favorit di beranda dan tambahkan ke cart.</p>
        </div>
        <router-link
          to="/"
          class="rounded-full bg-sky-600 px-5 py-2 text-sm font-semibold text-white shadow transition hover:bg-sky-700"
        >
          Mulai belanja
        </router-link>
      </div>

      <div v-else class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        <div class="space-y-4">
          <article
            v-for="item in cart.items"
            :key="item.id"
            class="flex gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm"
          >
            <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-xl bg-gray-100">
              <img
                v-if="item.product?.image_url"
                :src="item.product.image_url"
                :alt="item.product?.name"
                class="h-full w-full object-cover"
              >
              <div v-else class="flex h-full w-full items-center justify-center text-xs text-gray-400">No image</div>
            </div>

            <div class="flex flex-1 flex-col justify-between">
              <div>
                <p class="text-base font-semibold text-gray-800">{{ item.product?.name }}</p>
                <p class="text-sm text-gray-500">{{ formatPrice(item.product?.price) }}</p>
              </div>

              <div class="mt-4 flex items-center justify-between">
                <div class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50">
                  <button
                    class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100"
                    @click="decrease(item)"
                    :disabled="loading"
                  >
                    –
                  </button>
                  <span class="px-3 text-sm font-semibold text-gray-700">{{ item.quantity }}</span>
                  <button
                    class="px-3 py-1 text-sm text-gray-600 hover:bg-gray-100"
                    @click="increase(item)"
                    :disabled="loading"
                  >
                    +
                  </button>
                </div>
                <button
                  class="text-xs font-medium text-red-500 hover:text-red-600"
                  @click="remove(item)"
                  :disabled="loading"
                >
                  Hapus
                </button>
              </div>
            </div>
          </article>
        </div>

        <aside class="flex flex-col gap-4 rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
          <div class="flex items-center justify-between text-sm text-gray-600">
            <span>Subtotal</span>
            <span class="text-base font-semibold text-gray-900">{{ formatPrice(cart.subtotal) }}</span>
          </div>
          <p class="text-xs text-gray-400">*Belum termasuk ongkos kirim. Stok akan dicek ulang saat checkout.</p>
          <button
            class="mt-2 w-full rounded-full bg-sky-600 py-3 text-sm font-semibold text-white shadow transition hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="loading || !cart.items.length"
            @click="goToCheckout"
          >
            {{ user ? 'Lanjut ke checkout' : 'Login untuk checkout' }}
          </button>
        </aside>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCart } from '../js/composables/useCart'

const router = useRouter()
const { cart, loading, loadCart, updateQuantity, removeItem } = useCart()

const user = computed(() => window.Laravel?.user ?? null)

function formatPrice(value) {
  const amount = Number.parseInt(value ?? 0, 10) / 100
  return amount.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
}

function increase(item) {
  updateQuantity(item.id, item.quantity + 1)
}

function decrease(item) {
  if (item.quantity <= 1) {
    remove(item)
    return
  }
  updateQuantity(item.id, item.quantity - 1)
}

function remove(item) {
  removeItem(item.id)
}

function goToCheckout() {
  if (!cart.value.items.length) {
    return
  }

  if (!user.value) {
    router.push({ path: '/login', query: { redirect: '/checkout' } })
    return
  }

  router.push({ name: 'checkout' })
}

onMounted(() => {
  loadCart(true)
})
</script>
