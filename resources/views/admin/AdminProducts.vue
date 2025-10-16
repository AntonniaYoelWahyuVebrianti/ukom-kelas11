<template>
  <div class="space-y-8">
    <section class="rounded-2xl border border-slate-700/40 bg-slate-900/60 p-6 shadow-lg shadow-slate-900/20">
      <header class="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-semibold text-white">Kelola produk</h2>
          <p class="text-sm text-slate-400">Perbarui katalog dan stok item penjualan.</p>
        </div>
        <button
          type="button"
          class="rounded-full bg-gradient-to-r from-sky-500 to-emerald-400 px-5 py-2 text-sm font-semibold text-slate-900 shadow-lg shadow-emerald-500/20 transition hover:from-sky-400 hover:to-emerald-300"
          @click="openModal()"
        >
          Produk baru
        </button>
      </header>
    </section>

    <section class="rounded-2xl border border-slate-700/40 bg-slate-900/60 p-6 shadow-lg shadow-slate-900/20">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h3 class="text-lg font-semibold text-white">Daftar produk</h3>
        <div class="text-xs text-slate-400">{{ pagination.total }} item</div>
      </div>

      <div v-if="loading" class="mt-4 rounded-2xl border border-dashed border-slate-700/50 px-4 py-6 text-center text-sm text-slate-400">
        Memuat produk…
      </div>

      <div v-else-if="!products.length" class="mt-4 rounded-2xl border border-dashed border-slate-700/50 px-4 py-6 text-center text-sm text-slate-400">
        Belum ada produk tersimpan.
      </div>

      <div v-else class="mt-6 overflow-hidden rounded-2xl border border-slate-800">
        <table class="w-full text-left text-sm text-slate-300">
          <thead class="bg-slate-900/80 text-xs uppercase tracking-wider text-slate-400">
            <tr>
              <th class="px-4 py-3">Produk</th>
              <th class="px-4 py-3">Kategori</th>
              <th class="px-4 py-3">Harga</th>
              <th class="px-4 py-3">Stok</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="product in products"
              :key="product.id"
              class="border-t border-slate-800/80"
            >
              <td class="px-4 py-4">
                <div class="flex items-center gap-3">
                  <img
                    v-if="product.image_url"
                    :src="product.image_url"
                    :alt="product.name"
                    class="h-12 w-12 rounded-xl object-cover"
                  >
                  <div>
                    <p class="font-semibold text-white">{{ product.name }}</p>
                    <p class="text-xs text-slate-500">SKU: {{ product.sku || '—' }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">{{ product.category_model?.name ?? product.category_name ?? '—' }}</td>
              <td class="px-4 py-4">{{ formatCurrency(product.price) }}</td>
              <td class="px-4 py-4">{{ product.stock }} pcs</td>
              <td class="px-4 py-4 text-right">
                <button class="mr-3 text-xs font-semibold text-sky-300 hover:text-sky-100" @click="openModal(product)">Edit</button>
                <button class="text-xs font-semibold text-red-300 hover:text-red-200" @click="remove(product)">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="pagination.total > pagination.per_page" class="mt-6 flex flex-wrap items-center gap-3 text-xs text-slate-400">
        <button
          type="button"
          class="rounded-full border border-slate-700 px-3 py-1 hover:border-slate-500 hover:text-slate-200 disabled:opacity-40"
          :disabled="!pagination.prev_page_url"
          @click="loadProducts(pagination.current_page - 1)"
        >
          Sebelumnya
        </button>
        <span>Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}</span>
        <button
          type="button"
          class="rounded-full border border-slate-700 px-3 py-1 hover:border-slate-500 hover:text-slate-200 disabled:opacity-40"
          :disabled="!pagination.next_page_url"
          @click="loadProducts(pagination.current_page + 1)"
        >
          Selanjutnya
        </button>
      </div>
    </section>

    <transition name="fade">
      <div
        v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm p-6"
        @click.self="closeModal"
        @keydown.esc.prevent="closeModal"
        tabindex="-1"
        ref="modalRef"
      >
        <div class="relative w-full max-w-3xl rounded-3xl border border-slate-700 bg-slate-900 p-6 shadow-2xl shadow-slate-900/40">
          <button
            type="button"
            class="absolute right-4 top-4 inline-flex h-9 w-9 items-center justify-center rounded-full bg-slate-800 text-slate-400 hover:text-white"
            @click="closeModal"
          >
            <span class="sr-only">Tutup</span>
            ×
          </button>

          <h3 class="text-lg font-semibold text-white">{{ form.id ? 'Edit produk' : 'Produk baru' }}</h3>
          <p class="mt-1 text-sm text-slate-400">Lengkapi informasi produk untuk katalog kamu.</p>

          <form class="mt-6 grid gap-4" @submit.prevent="submit">
            <div class="grid gap-4 md:grid-cols-2">
              <label class="text-sm text-slate-300">
                Nama produk
                <input v-model="form.name" type="text" required class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
              </label>
              <label class="text-sm text-slate-300">
                SKU (opsional)
                <input v-model="form.sku" type="text" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
              </label>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
              <label class="text-sm text-slate-300">
                Kategori
                <select v-model="form.category_id" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
                  <option :value="null">Pilih kategori</option>
                  <option v-for="category in categoryOptions" :key="category.id" :value="category.id">{{ category.name }}</option>
                </select>
              </label>
              <label class="text-sm text-slate-300">
                Stok
                <input v-model.number="form.stock" min="0" type="number" required class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
              </label>
              <label class="text-sm text-slate-300">
                Harga (IDR)
                <input v-model="form.price_display" type="number" min="0" step="1000" required class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/40">
              </label>
            </div>

            <div class="text-sm text-slate-300">
              <div class="flex flex-wrap items-center justify-between gap-2">
                <span>Gambar produk</span>
                <div class="flex gap-1 rounded-full bg-slate-800/60 p-1 text-xs">
                  <button
                    type="button"
                    class="rounded-full px-3 py-1 font-semibold transition"
                    :class="imageMode === 'url' ? 'bg-slate-900 text-sky-300 shadow-sm' : 'text-slate-400 hover:text-slate-200'"
                    @click="setImageMode('url')"
                  >
                    Pakai URL
                  </button>
                  <button
                    type="button"
                    class="rounded-full px-3 py-1 font-semibold transition"
                    :class="imageMode === 'upload' ? 'bg-slate-900 text-emerald-300 shadow-sm' : 'text-slate-400 hover:text-slate-200'"
                    @click="setImageMode('upload')"
                  >
                    Unggah gambar
                  </button>
                </div>
              </div>
              <div v-if="imageMode === 'url'" class="mt-2">
                <input
                  v-model="form.image_url"
                  type="url"
                  placeholder="https://"
                  class="w-full rounded-xl border border-slate-700 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/40"
                >
              </div>
              <div v-else class="mt-2 space-y-2">
                <input
                  key="file-input"
                  type="file"
                  accept="image/*"
                  class="block w-full cursor-pointer rounded-xl border border-dashed border-slate-700 bg-slate-900/80 px-3 py-2 text-xs text-slate-300 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-800 file:px-3 file:py-2 file:text-xs file:font-semibold file:uppercase file:tracking-wide file:text-slate-200 hover:border-slate-600"
                  :disabled="saving"
                  @change="handleImageChange"
                >
                <p class="text-xs text-slate-500">Format JPG, PNG, atau WEBP hingga 2MB.</p>
              </div>
              <div v-if="imagePreviewUrl" class="mt-3 flex items-center gap-3 rounded-xl border border-slate-800 bg-slate-900/70 p-3">
                <img :src="imagePreviewUrl" alt="Pratinjau gambar produk" class="h-16 w-16 rounded-lg object-cover">
                <div class="flex-1 text-xs text-slate-400">
                  <p class="font-semibold text-slate-200">Pratinjau gambar</p>
                  <p v-if="imageMode === 'upload'" class="mt-1">Gambar akan disimpan ke penyimpanan toko saat produk disimpan.</p>
                  <p v-else class="mt-1">Gambar ditarik dari URL yang kamu masukkan.</p>
                </div>
              </div>
            </div>

            <label class="text-sm text-slate-300">
              Deskripsi produk
              <textarea v-model="form.description" rows="4" class="mt-1 w-full rounded-xl border border-slate-700 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-500/40"></textarea>
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
                {{ form.id ? 'Simpan perubahan' : 'Simpan produk' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue'
import axios from 'axios'

const products = ref([])
const categoryOptions = ref([])
const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const modalRef = ref(null)
const imageMode = ref('url')
const imageFile = ref(null)
const imagePreview = ref(null)

const pagination = reactive({
  total: 0,
  per_page: 12,
  current_page: 1,
  last_page: 1,
  next_page_url: null,
  prev_page_url: null,
})

const form = reactive({
  id: null,
  name: '',
  sku: '',
  category_id: null,
  stock: 0,
  price_display: '',
  image_url: '',
  description: '',
})

const imagePreviewUrl = computed(() => {
  if (imageMode.value === 'upload') {
    return imagePreview.value
  }

  return form.image_url || null
})

onMounted(async () => {
  await Promise.all([loadCategories(), loadProducts()])
})

watch(showModal, (visible) => {
  document.body.classList.toggle('overflow-hidden', visible)

  if (visible) {
    nextTick(() => {
      modalRef.value?.focus()
    })
  }
})

async function loadCategories() {
  try {
    const { data } = await axios.get('/api/admin/categories', { params: { per_page: 100 } })
    categoryOptions.value = data.data ?? []
  } catch (error) {
    console.error('Tidak dapat memuat kategori', error)
  }
}

async function loadProducts(page = 1) {
  loading.value = true
  try {
    const { data } = await axios.get('/api/admin/products', { params: { page } })
    products.value = data.data ?? []
    Object.assign(pagination, {
      total: data.total,
      per_page: data.per_page,
      current_page: data.current_page,
      last_page: data.last_page,
      next_page_url: data.next_page_url,
      prev_page_url: data.prev_page_url,
    })
  } catch (error) {
    console.error('Gagal memuat produk', error)
  } finally {
    loading.value = false
  }
}

function openModal(product = null) {
  resetForm()

  if (product) {
    form.id = product.id
    form.name = product.name
    form.sku = product.sku ?? ''
    form.category_id = product.category_id ?? product.category_model?.id ?? null
    form.stock = product.stock
    form.price_display = String(Number.parseInt(product.price ?? 0, 10) / 100)
    form.image_url = product.image_url ?? ''
    form.description = product.description ?? ''

    if (form.image_url) {
      imageMode.value = 'url'
    } else {
      setImageMode('upload')
    }
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
  form.sku = ''
  form.category_id = null
  form.stock = 0
  form.price_display = ''
  form.image_url = ''
  form.description = ''
  imageMode.value = 'url'
  clearImageSelection()
}

async function submit() {
  if (!form.name) {
    return
  }

  const priceValue = Number.parseFloat(form.price_display || '0')
  if (Number.isNaN(priceValue)) {
    alert('Harga tidak valid.')
    return
  }

  saving.value = true
  try {
    const payload = new FormData()
    payload.append('name', form.name)
    payload.append('sku', form.sku ?? '')
    payload.append('category_id', form.category_id ?? '')
    payload.append('stock', String(Number.parseInt(form.stock, 10) || 0))
    payload.append('price', String(Math.round(priceValue * 100)))
    payload.append('description', form.description ?? '')

    if (imageMode.value === 'url') {
      payload.append('image_url', form.image_url ?? '')
    } else if (imageFile.value) {
      payload.append('image', imageFile.value)
    }

    const config = { headers: { 'Content-Type': 'multipart/form-data' } }

    if (form.id) {
      payload.append('_method', 'PUT')
      await axios.post(`/api/admin/products/${form.id}`, payload, config)
    } else {
      await axios.post('/api/admin/products', payload, config)
    }

    closeModal()
    await loadProducts(pagination.current_page)
  } catch (error) {
    const message = error?.response?.data?.message ?? 'Produk gagal disimpan.'
    alert(message)
  } finally {
    saving.value = false
  }
}

async function remove(product) {
  if (!confirm(`Hapus ${product.name}?`)) {
    return
  }

  try {
    await axios.delete(`/api/admin/products/${product.id}`)
    if (products.value.length === 1 && pagination.current_page > 1) {
      await loadProducts(pagination.current_page - 1)
    } else {
      await loadProducts(pagination.current_page)
    }
  } catch (error) {
    alert('Produk tidak bisa dihapus saat ini.')
  }
}

function formatCurrency(value) {
  const amount = Number.parseInt(value ?? 0, 10) / 100
  return amount.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
}

function setImageMode(mode) {
  if (mode === imageMode.value) {
    return
  }

  imageMode.value = mode

  if (mode === 'url') {
    clearImageSelection()
  } else {
    form.image_url = ''
  }
}

function clearImageSelection() {
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value)
  }
  imagePreview.value = null
  imageFile.value = null
}

function handleImageChange(event) {
  const [file] = event.target?.files ?? []
  clearImageSelection()

  if (file) {
    imageFile.value = file
    imagePreview.value = URL.createObjectURL(file)
  }

  if (event.target) {
    event.target.value = ''
  }
}

onBeforeUnmount(() => {
  clearImageSelection()
  document.body.classList.remove('overflow-hidden')
})
</script>
