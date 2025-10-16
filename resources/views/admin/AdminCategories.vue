<template>
  <div class="space-y-8">
    <section class="rounded-2xl border border-slate-700/40 bg-slate-900/60 p-6 shadow-lg shadow-slate-900/20">
      <header class="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-semibold text-white">Kelola kategori</h2>
          <p class="text-sm text-slate-400">Kategorikan produk untuk memudahkan pencarian pengunjung.</p>
        </div>
        <button
          type="button"
          class="rounded-full bg-gradient-to-r from-sky-500 to-emerald-400 px-5 py-2 text-sm font-semibold text-slate-900 shadow-lg shadow-emerald-500/20 transition hover:from-sky-400 hover:to-emerald-300"
          @click="openModal()"
        >
          Tambah kategori
        </button>
      </header>
    </section>

    <section class="rounded-2xl border border-slate-700/40 bg-slate-900/60 p-6 shadow-lg shadow-slate-900/20">
      <h3 class="text-lg font-semibold text-white">Daftar kategori</h3>

      <div v-if="loading" class="mt-4 rounded-2xl border border-dashed border-slate-700/50 px-4 py-6 text-center text-sm text-slate-400">
        Memuat kategori…
      </div>

      <div v-else-if="!categories.length" class="mt-4 rounded-2xl border border-dashed border-slate-700/50 px-4 py-6 text-center text-sm text-slate-400">
        Belum ada kategori. Tambahkan kategori pertama di form atas.
      </div>

      <ul v-else class="mt-6 grid gap-4 md:grid-cols-2">
        <li
          v-for="category in categories"
          :key="category.id"
          class="flex h-full flex-col justify-between rounded-2xl border border-slate-800 bg-slate-900/80 p-5 shadow-sm shadow-slate-900/30"
        >
          <div class="space-y-2">
            <div class="flex items-center justify-between gap-3">
              <h4 class="text-base font-semibold text-white">{{ category.name }}</h4>
              <span class="text-[10px] uppercase tracking-widest text-slate-500">{{ new Date(category.created_at).toLocaleDateString('id-ID') }}</span>
            </div>
            <p class="text-sm text-slate-400">{{ category.description || 'Belum ada deskripsi.' }}</p>
          </div>

          <div class="mt-6 flex items-center justify-between">
            <button
              type="button"
              class="text-xs font-semibold text-sky-300 hover:text-sky-100"
              @click="openModal(category)"
            >
              Edit
            </button>
            <button
              type="button"
              class="text-xs font-semibold text-red-300 hover:text-red-200"
              @click="remove(category)"
            >
              Hapus
            </button>
          </div>
        </li>
      </ul>

      <div v-if="pagination.total > pagination.per_page" class="mt-6 flex flex-wrap items-center gap-3 text-xs text-slate-400">
        <button
          type="button"
          class="rounded-full border border-slate-700 px-3 py-1 hover:border-slate-500 hover:text-slate-200 disabled:opacity-40"
          :disabled="!pagination.prev_page_url"
          @click="loadCategories(pagination.current_page - 1)"
        >
          Sebelumnya
        </button>
        <span>Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}</span>
        <button
          type="button"
          class="rounded-full border border-slate-700 px-3 py-1 hover:border-slate-500 hover:text-slate-200 disabled:opacity-40"
          :disabled="!pagination.next_page_url"
          @click="loadCategories(pagination.current_page + 1)"
        >
          Selanjutnya
        </button>
      </div>
    </section>
    <transition name="fade">
      <div
        v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-6"
        @keydown.esc.prevent="closeModal"
        @click.self="closeModal"
        tabindex="-1"
        ref="modalRef"
      >
        <div class="relative w-full max-w-2xl rounded-3xl border border-slate-700 bg-slate-900 p-6 shadow-2xl shadow-slate-900/40">
          <button
            type="button"
            class="absolute right-4 top-4 inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-800 text-slate-400 hover:text-white"
            @click="closeModal"
          >
            <span class="sr-only">Tutup</span>
            ×
          </button>

          <h3 class="text-lg font-semibold text-white">{{ form.id ? 'Edit kategori' : 'Kategori baru' }}</h3>
          <p class="mt-1 text-sm text-slate-400">Lengkapi detail kategori untuk menata katalog produk.</p>

          <form class="mt-6 grid gap-4" @submit.prevent="submit">
            <div class="grid gap-4 md:grid-cols-2">
              <label class="text-sm text-slate-300">
                Nama kategori
                <input v-model="form.name" type="text" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-900/80 px-3 py-2 text-sm text-white placeholder:text-slate-500 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/40" required>
              </label>
              <label class="text-sm text-slate-300">
                Banner image URL (opsional)
                <input v-model="form.image_url" type="url" placeholder="https://" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-900/80 px-3 py-2 text-sm text-white placeholder:text-slate-500 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
              </label>
            </div>

            <label class="text-sm text-slate-300">
              Deskripsi singkat
              <textarea v-model="form.description" rows="3" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-900/80 px-3 py-2 text-sm text-white placeholder:text-slate-500 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/40"></textarea>
            </label>

            <div class="flex items-center justify-end gap-3">
              <button
                type="button"
                class="rounded-full border border-slate-700 px-4 py-2 text-xs font-semibold text-slate-400 hover:border-slate-500 hover:text-slate-200"
                @click="closeModal"
              >
                Batal
              </button>
              <button
                type="submit"
                class="rounded-full bg-emerald-500 px-5 py-2 text-xs font-semibold uppercase tracking-wide text-slate-900 shadow-lg shadow-emerald-500/20 disabled:opacity-60"
                :disabled="saving"
              >
                {{ form.id ? 'Simpan perubahan' : 'Simpan kategori' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { nextTick, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue'
import axios from 'axios'

const categories = ref([])
const pagination = reactive({
  total: 0,
  per_page: 10,
  current_page: 1,
  last_page: 1,
  next_page_url: null,
  prev_page_url: null,
})
const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const modalRef = ref(null)

const form = reactive({
  id: null,
  name: '',
  description: '',
  image_url: '',
})

onMounted(() => {
  loadCategories()
})

watch(showModal, (visible) => {
  document.body.classList.toggle('overflow-hidden', visible)

  if (visible) {
    nextTick(() => {
      modalRef.value?.focus()
    })
  }
})

onBeforeUnmount(() => {
  document.body.classList.remove('overflow-hidden')
})

async function loadCategories(page = 1) {
  loading.value = true
  try {
    const { data } = await axios.get('/api/admin/categories', { params: { page } })
    categories.value = data.data ?? []
    Object.assign(pagination, {
      total: data.total,
      per_page: data.per_page,
      current_page: data.current_page,
      last_page: data.last_page,
      next_page_url: data.next_page_url,
      prev_page_url: data.prev_page_url,
    })
  } catch (error) {
    console.error('Gagal memuat kategori', error)
  } finally {
    loading.value = false
  }
}

function openModal(category = null) {
  resetForm()

  if (category) {
    form.id = category.id
    form.name = category.name
    form.description = category.description ?? ''
    form.image_url = category.image_url ?? ''
  }

  showModal.value = true
}

function closeModal() {
  showModal.value = false
  resetForm()
}

function resetForm() {
  form.id = null
  form.name = ''
  form.description = ''
  form.image_url = ''
}

async function submit() {
  if (!form.name) {
    return
  }

  saving.value = true
  try {
    const payload = {
      name: form.name,
      description: form.description || null,
      image_url: form.image_url || null,
    }

    if (form.id) {
      await axios.put(`/api/admin/categories/${form.id}`, payload)
    } else {
      await axios.post('/api/admin/categories', payload)
    }

    closeModal()
    await loadCategories(pagination.current_page)
  } catch (error) {
    const message = error?.response?.data?.message ?? 'Tidak dapat menyimpan kategori.'
    alert(message)
  } finally {
    saving.value = false
  }
}

async function remove(category) {
  if (!confirm(`Hapus kategori ${category.name}?`)) {
    return
  }

  try {
    await axios.delete(`/api/admin/categories/${category.id}`)
    if (categories.value.length === 1 && pagination.current_page > 1) {
      await loadCategories(pagination.current_page - 1)
    } else {
      await loadCategories(pagination.current_page)
    }
  } catch (error) {
    alert('Kategori tidak dapat dihapus saat ini.')
  }
}
</script>
