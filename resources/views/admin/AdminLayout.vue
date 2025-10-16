<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-slate-100">
    <div class="mx-auto flex w-full max-w-7xl flex-col gap-8 px-6 py-10 lg:flex-row lg:px-10">
      <aside class="w-full rounded-3xl border border-slate-700/60 bg-slate-900/60 p-6 shadow-2xl shadow-slate-900/30 lg:w-64">
        <div class="mb-8 flex items-center gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-400 to-emerald-400 text-lg font-bold text-slate-900">KW</div>
          <div>
            <p class="text-sm uppercase tracking-widest text-slate-400">Control Center</p>
            <h1 class="text-lg font-semibold text-white">Admin Dashboard</h1>
          </div>
        </div>

        <nav class="space-y-2">
          <RouterLink
            v-for="item in menu"
            :key="item.name"
            :to="item.to"
            class="flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-semibold transition"
            :class="isActive(item.to)
              ? 'bg-sky-500/20 text-white shadow-inner shadow-sky-500/20'
              : 'text-slate-300 hover:bg-slate-800/80 hover:text-white'"
          >
            <span class="flex items-center gap-3">
              <span class="text-base">{{ item.icon }}</span>
              {{ item.label }}
            </span>
            <span v-if="isActive(item.to)" class="h-2 w-2 rounded-full bg-emerald-400"></span>
          </RouterLink>
        </nav>

        <div class="mt-10 rounded-2xl border border-slate-800 bg-slate-900/70 p-4 text-xs text-slate-400">
          <p class="font-semibold text-slate-200">Login Info</p>
          <p class="mt-2">Gunakan akun admin untuk mengelola katalog dan pesanan.</p>
          <p class="mt-3 font-semibold text-emerald-300">admin@kwave.test</p>
        </div>
      </aside>

      <section class="flex-1 space-y-6">
        <header class="flex flex-wrap items-center justify-between gap-4 rounded-3xl border border-slate-700/60 bg-slate-900/60 px-6 py-5 shadow-2xl shadow-slate-900/30">
          <div>
            <p class="text-xs uppercase tracking-widest text-slate-400">Welcome back</p>
            <h2 class="text-2xl font-semibold text-white">{{ pageTitle }}</h2>
          </div>
          <RouterLink
            to="/"
            class="rounded-full bg-gradient-to-br from-sky-500 to-emerald-500 px-5 py-2 text-sm font-semibold text-slate-900 shadow-lg shadow-sky-500/30 transition hover:from-sky-400 hover:to-emerald-400"
          >
            Lihat storefront
          </RouterLink>
        </header>

        <div class="rounded-3xl border border-slate-700/60 bg-slate-900/70 p-6 shadow-2xl shadow-slate-900/30">
          <RouterView />
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const menu = [
  { name: 'dashboard', label: 'Ringkasan', to: { name: 'admin-dashboard' }, icon: '📊' },
  { name: 'categories', label: 'Kategori', to: { name: 'admin-categories' }, icon: '🏷️' },
  { name: 'products', label: 'Produk', to: { name: 'admin-products' }, icon: '🛒' },
  { name: 'orders', label: 'Pesanan', to: { name: 'admin-orders' }, icon: '📦' },
]

const pageTitle = computed(() => {
  const current = menu.find(item => route.name === item.to.name)
  return current ? current.label : 'Dashboard'
})

function isActive(to) {
  return route.name === to.name
}
</script>
