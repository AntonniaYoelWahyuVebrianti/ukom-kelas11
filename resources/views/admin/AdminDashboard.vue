<template>
  <div class="space-y-8">
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <article
        v-for="card in cards"
        :key="card.label"
        class="rounded-2xl border border-slate-700/40 bg-slate-900/60 p-5 shadow-lg shadow-slate-900/20"
      >
        <p class="text-xs uppercase tracking-wider text-slate-400">{{ card.label }}</p>
        <p class="mt-3 text-2xl font-semibold text-white">{{ formatStat(card.value, card.label) }}</p>
      </article>
    </section>

    <section class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
      <article class="rounded-2xl border border-slate-700/40 bg-slate-900/60 p-6 shadow-lg shadow-slate-900/20">
        <header class="flex items-baseline justify-between">
          <h3 class="text-lg font-semibold text-white">Penjualan 6 bulan terakhir</h3>
          <span class="text-xs text-slate-400">Dalam IDR</span>
        </header>
        <div v-if="!salesByMonth.length" class="mt-6 rounded-xl border border-dashed border-slate-700/50 p-8 text-center text-sm text-slate-400">
          Belum ada data penjualan.
        </div>
        <div v-else class="mt-6 space-y-3">
          <div
            v-for="entry in salesByMonth"
            :key="entry.month"
            class="flex items-center gap-4"
          >
            <div class="w-24 text-sm font-semibold text-slate-300">{{ entry.month }}</div>
            <div class="h-2 flex-1 rounded-full bg-slate-800">
              <div
                class="h-2 rounded-full bg-gradient-to-r from-sky-500 to-emerald-400"
                :style="{ width: computeBar(entry.total) }"
              ></div>
            </div>
            <span class="w-28 text-right text-sm text-slate-300">{{ formatCurrency(entry.total) }}</span>
          </div>
        </div>
      </article>

      <article class="rounded-2xl border border-slate-700/40 bg-slate-900/60 p-6 shadow-lg shadow-slate-900/20">
        <h3 class="text-lg font-semibold text-white">Stok menipis</h3>
        <ul class="mt-4 space-y-3 text-sm text-slate-300">
          <li v-if="!lowStock.length" class="rounded-xl border border-dashed border-slate-700/50 px-4 py-5 text-center text-xs text-slate-500">
            Semua stok aman 🎉
          </li>
          <li
            v-for="item in lowStock"
            :key="item.id"
            class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-900/80 px-4 py-3"
          >
            <span class="font-medium text-slate-100">{{ item.name }}</span>
            <span class="text-xs text-emerald-300">{{ item.stock }} pcs</span>
          </li>
        </ul>
      </article>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
      <article class="rounded-2xl border border-slate-700/40 bg-slate-900/60 p-6 shadow-lg shadow-slate-900/20">
        <h3 class="text-lg font-semibold text-white">Produk terlaris</h3>
        <div v-if="!topProducts.length" class="mt-4 rounded-xl border border-dashed border-slate-700/50 px-4 py-6 text-center text-sm text-slate-400">
          Belum ada data penjualan produk.
        </div>
        <ul v-else class="mt-4 space-y-4 text-sm text-slate-300">
          <li
            v-for="product in topProducts"
            :key="product.product_id"
            class="flex items-center justify-between gap-3 rounded-xl border border-slate-800 bg-slate-900/80 px-4 py-3"
          >
            <div>
              <p class="font-semibold text-slate-100">{{ product.product?.name ?? 'Produk dihapus' }}</p>
              <p class="text-xs text-slate-500">{{ formatCurrency(product.total_amount) }}</p>
            </div>
            <span class="text-xs font-semibold text-emerald-300">{{ product.total_quantity }} terjual</span>
          </li>
        </ul>
      </article>

      <article class="rounded-2xl border border-slate-700/40 bg-slate-900/60 p-6 shadow-lg shadow-slate-900/20">
        <h3 class="text-lg font-semibold text-white">Catatan admin</h3>
        <p class="mt-4 text-sm text-slate-300">Gunakan menu di samping untuk memperbarui kategori, produk, dan memantau status pesanan. Statistik ini diperbarui secara real-time dari data order.</p>
        <p class="mt-6 text-xs text-slate-500">Tips: Tandai pesanan sebagai <span class="text-emerald-300">completed</span> setelah barang dikirim untuk menjaga metrik tetap akurat.</p>
      </article>
    </section>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'

const cards = ref([])
const topProducts = ref([])
const salesByMonth = ref([])
const lowStock = ref([])

onMounted(async () => {
  await fetchDashboard()
})

async function fetchDashboard() {
  try {
    const { data } = await axios.get('/api/admin/dashboard')
    cards.value = data.cards ?? []
    topProducts.value = data.top_products ?? []
    salesByMonth.value = data.sales_by_month ?? []
    lowStock.value = data.low_stock ?? []
  } catch (error) {
    console.error('Gagal memuat statistik admin', error)
  }
}

function formatStat(value, label) {
  if (label.toLowerCase().includes('revenue')) {
    return formatCurrency(value)
  }

  return new Intl.NumberFormat('id-ID').format(value ?? 0)
}

function formatCurrency(value) {
  const amount = Number.parseInt(value ?? 0, 10) / 100
  return amount.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
}

function computeBar(value) {
  if (!salesByMonth.value.length) {
    return '0%'
  }
  const max = Math.max(...salesByMonth.value.map(entry => entry.total)) || 1
  return `${Math.max(6, Math.round((value / max) * 100))}%`
}
</script>
