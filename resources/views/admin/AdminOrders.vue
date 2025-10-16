<template>
  <div class="space-y-6">
    <header class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h2 class="text-xl font-semibold text-white">Riwayat pesanan</h2>
        <p class="text-sm text-slate-400">Pantau status dan detail transaksi pelanggan.</p>
      </div>
      <select v-model="filters.status" class="rounded-full border border-slate-700 bg-slate-900/80 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-300 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/40" @change="loadOrders()">
        <option value="">Semua status</option>
        <option v-for="status in statuses" :key="status.value" :value="status.value">{{ status.label }}</option>
      </select>
    </header>

    <div class="rounded-2xl border border-slate-700/40 bg-slate-900/60 p-6 shadow-lg shadow-slate-900/20">
      <div v-if="loading" class="rounded-2xl border border-dashed border-slate-700/50 px-4 py-6 text-center text-sm text-slate-400">
        Memuat pesanan…
      </div>

      <div v-else-if="!orders.length" class="rounded-2xl border border-dashed border-slate-700/50 px-4 py-6 text-center text-sm text-slate-400">
        Belum ada pesanan pada filter ini.
      </div>

      <ul v-else class="space-y-4">
        <li
          v-for="order in orders"
          :key="order.id"
          class="rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-sm shadow-slate-900/30"
        >
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
              <p class="text-xs uppercase tracking-wider text-slate-400">Order #{{ order.id }}</p>
              <p class="text-sm text-slate-300">{{ formatDate(order.created_at) }}</p>
            </div>
            <div class="flex items-center gap-2">
              <span class="rounded-full bg-slate-800 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-slate-300">{{ order.status }}</span>
              <select
                v-model="order.status"
                class="rounded-full border border-slate-700 bg-slate-900/90 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-200 focus:border-sky-400 focus:outline-none"
                @change="updateStatus(order)"
              >
                <option v-for="status in statuses" :key="status.value" :value="status.value">{{ status.label }}</option>
              </select>
            </div>
          </div>

          <div class="mt-4 grid gap-4 md:grid-cols-2">
            <div>
              <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Customer</p>
              <p class="text-sm text-white">{{ order.user?.name }}</p>
              <p class="text-xs text-slate-400">{{ order.user?.email }}</p>
            </div>
            <div>
              <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total</p>
              <p class="text-lg font-semibold text-emerald-300">{{ formatCurrency(order.total) }}</p>
            </div>
          </div>

          <div class="mt-4 rounded-2xl border border-slate-800 bg-slate-900/70 p-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Items</p>
            <ul class="mt-2 space-y-2 text-sm text-slate-300">
              <li v-for="item in order.items" :key="item.id" class="flex items-center justify-between">
                <span>{{ item.product?.name ?? 'Produk dihapus' }} <span class="text-xs text-slate-500">x{{ item.quantity }}</span></span>
                <span>{{ formatCurrency(item.total) }}</span>
              </li>
            </ul>
          </div>

          <div v-if="order.address" class="mt-4 text-xs text-slate-400">
            <p class="font-semibold text-slate-300">Alamat pengiriman</p>
            <p>{{ order.address.recipient_name }} · {{ order.address.phone }}</p>
            <p>{{ order.address.line1 }}<span v-if="order.address.line2">, {{ order.address.line2 }}</span></p>
            <p>{{ [order.address.city, order.address.state, order.address.postal_code, order.address.country].filter(Boolean).join(', ') }}</p>
          </div>
        </li>
      </ul>

      <div v-if="pagination.total > pagination.per_page" class="mt-6 flex flex-wrap items-center gap-3 text-xs text-slate-400">
        <button
          type="button"
          class="rounded-full border border-slate-700 px-3 py-1 hover:border-slate-500 hover:text-slate-200 disabled:opacity-40"
          :disabled="!pagination.prev_page_url"
          @click="loadOrders(pagination.current_page - 1)"
        >
          Sebelumnya
        </button>
        <span>Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}</span>
        <button
          type="button"
          class="rounded-full border border-slate-700 px-3 py-1 hover:border-slate-500 hover:text-slate-200 disabled:opacity-40"
          :disabled="!pagination.next_page_url"
          @click="loadOrders(pagination.current_page + 1)"
        >
          Selanjutnya
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import axios from 'axios'

const orders = ref([])
const loading = ref(false)

const filters = reactive({
  status: '',
})

const pagination = reactive({
  total: 0,
  per_page: 15,
  current_page: 1,
  last_page: 1,
  next_page_url: null,
  prev_page_url: null,
})

const statuses = [
  { value: 'pending', label: 'Pending' },
  { value: 'processing', label: 'Processing' },
  { value: 'completed', label: 'Completed' },
  { value: 'cancelled', label: 'Cancelled' },
]

onMounted(() => {
  loadOrders()
})

async function loadOrders(page = 1) {
  loading.value = true
  try {
    const { data } = await axios.get('/api/admin/orders', {
      params: {
        page,
        status: filters.status || undefined,
      },
    })
    orders.value = data.data ?? []
    Object.assign(pagination, {
      total: data.total,
      per_page: data.per_page,
      current_page: data.current_page,
      last_page: data.last_page,
      next_page_url: data.next_page_url,
      prev_page_url: data.prev_page_url,
    })
  } catch (error) {
    console.error('Tidak dapat memuat pesanan', error)
  } finally {
    loading.value = false
  }
}

async function updateStatus(order) {
  try {
    await axios.put(`/api/admin/orders/${order.id}`, { status: order.status })
  } catch (error) {
    alert('Status tidak dapat diperbarui.')
    await loadOrders(pagination.current_page)
  }
}

function formatCurrency(value) {
  const amount = Number.parseInt(value ?? 0, 10) / 100
  return amount.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
}

function formatDate(value) {
  return new Date(value).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })
}
</script>
