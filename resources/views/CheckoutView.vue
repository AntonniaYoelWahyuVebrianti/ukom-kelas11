<template>
  <section class="px-6 py-12">
    <div class="mx-auto flex max-w-5xl flex-col gap-8">
      <header>
        <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
        <p class="mt-1 text-sm text-gray-500">Pilih alamat pengiriman dan konfirmasi pesananmu.</p>
      </header>

      <div class="grid gap-8 lg:grid-cols-[1.3fr_1fr]">
        <section class="space-y-6">
          <div>
            <h2 class="text-lg font-semibold text-gray-800">Alamat pengiriman</h2>
            <p class="text-xs text-gray-500">Gunakan alamat tersimpan atau tambahkan alamat baru.</p>
          </div>

          <div v-if="addressesLoading" class="flex items-center gap-3 rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm text-gray-500">
            <span class="h-2 w-2 animate-ping rounded-full bg-sky-400"></span>
            Memuat alamat tersimpan…
          </div>

          <div v-else-if="!addresses.length" class="rounded-2xl border border-dashed border-sky-200 bg-sky-50/60 px-5 py-6 text-sm text-sky-700">
            Kamu belum punya alamat. Tambah alamat baru di bawah.
          </div>

          <div v-else class="space-y-4">
            <label
              v-for="address in addresses"
              :key="address.id"
              class="flex cursor-pointer items-start gap-4 rounded-2xl border px-5 py-4 transition"
              :class="selectedAddressId === address.id
                ? 'border-sky-300 bg-sky-50/60'
                : 'border-gray-200 hover:border-sky-200'"
            >
              <input
                type="radio"
                class="mt-1 h-4 w-4 text-sky-600 focus:ring-sky-500"
                :value="address.id"
                v-model="selectedAddressId"
              >
              <div class="flex-1 text-sm text-gray-700">
                <div class="flex items-center gap-2">
                  <p class="font-semibold text-gray-900">{{ address.recipient_name }}</p>
                  <span v-if="address.is_default" class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-700">Default</span>
                </div>
                <p class="mt-1">{{ address.line1 }}<span v-if="address.line2">, {{ address.line2 }}</span></p>
                <p class="text-xs text-gray-500">
                  {{ [address.city, address.state, address.postal_code, address.country].filter(Boolean).join(', ') }}
                </p>
                <p v-if="address.phone" class="mt-1 text-xs text-gray-400">Telp: {{ address.phone }}</p>
              </div>
            </label>
          </div>

          <div class="rounded-2xl border border-gray-200 bg-white px-5 py-6">
            <button
              class="flex w-full items-center justify-between text-left text-sm font-semibold text-sky-600"
              @click="toggleForm"
            >
              <span>{{ showForm ? 'Sembunyikan form alamat' : 'Tambah alamat baru' }}</span>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 transition"
                :class="{ 'rotate-180': showForm }"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <form v-if="showForm" class="mt-4 grid gap-4" @submit.prevent="submitAddress">
              <div class="grid gap-4 sm:grid-cols-2">
                <div>
                  <label class="text-xs font-semibold text-gray-600" for="address-name">Nama penerima</label>
                  <input id="address-name" v-model="form.recipient_name" type="text" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200" required>
                </div>
                <div>
                  <label class="text-xs font-semibold text-gray-600" for="address-phone">No. Telp</label>
                  <input id="address-phone" v-model="form.phone" type="text" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200">
                </div>
              </div>
              <div>
                <label class="text-xs font-semibold text-gray-600" for="address-line1">Alamat lengkap</label>
                <input id="address-line1" v-model="form.line1" type="text" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200" required>
              </div>
              <div>
                <label class="text-xs font-semibold text-gray-600" for="address-line2">Detail tambahan</label>
                <input id="address-line2" v-model="form.line2" type="text" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200" placeholder="Blok / patokan (opsional)">
              </div>
              <div class="grid gap-4 sm:grid-cols-3">
                <div>
                  <label class="text-xs font-semibold text-gray-600" for="address-city">Kota</label>
                  <input id="address-city" v-model="form.city" type="text" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200" required>
                </div>
                <div>
                  <label class="text-xs font-semibold text-gray-600" for="address-state">Provinsi</label>
                  <input id="address-state" v-model="form.state" type="text" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200">
                </div>
                <div>
                  <label class="text-xs font-semibold text-gray-600" for="address-postal">Kode pos</label>
                  <input id="address-postal" v-model="form.postal_code" type="text" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200">
                </div>
              </div>
              <div class="flex items-center justify-end gap-3">
                <button type="button" class="rounded-full border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-500" @click="resetForm">Reset</button>
                <button type="submit" class="rounded-full bg-sky-600 px-5 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow hover:bg-sky-700" :disabled="addressesLoading">
                  Simpan alamat
                </button>
              </div>
            </form>
          </div>
        </section>

        <aside class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800">Ringkasan Pesanan</h2>
            <router-link to="/cart" class="text-xs font-semibold text-sky-600 hover:text-sky-700">Edit cart</router-link>
          </div>

          <div class="mt-4 space-y-4" v-if="cart.items.length">
            <div
              v-for="item in cart.items"
              :key="item.id"
              class="flex items-center justify-between text-sm text-gray-600"
            >
              <div>
                <p class="font-medium text-gray-800">{{ item.product?.name }}</p>
                <p class="text-xs text-gray-400">x{{ item.quantity }}</p>
              </div>
              <span class="font-semibold text-gray-700">{{ formatPrice(item.line_total) }}</span>
            </div>
          </div>
          <p v-else class="mt-4 text-sm text-gray-500">Cart masih kosong.</p>

          <div class="mt-6 border-t border-dashed border-gray-300 pt-4 text-sm text-gray-600">
            <div class="flex justify-between">
              <span>Subtotal</span>
              <span class="font-semibold text-gray-800">{{ formatPrice(cart.subtotal) }}</span>
            </div>
            <div class="flex justify-between text-xs text-gray-400">
              <span>Pengiriman</span>
              <span>Dihitung kemudian</span>
            </div>
          </div>

          <div class="mt-4 flex items-center justify-between text-base font-semibold text-gray-900">
            <span>Total</span>
            <span>{{ formatPrice(cart.subtotal) }}</span>
          </div>

          <div v-if="message" class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-700">
            {{ message }}
          </div>

          <button
            class="mt-6 w-full rounded-full bg-emerald-600 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
            :disabled="addressesLoading || !canSubmit"
            @click="placeOrder"
          >
            Buat pesanan
          </button>
        </aside>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, reactive, ref, watch, onMounted } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { useAddresses } from '../js/composables/useAddresses'
import { useCart } from '../js/composables/useCart'

const router = useRouter()
const { addresses, loadAddresses, createAddress, loading: addressesLoading } = useAddresses()
const { cart, loadCart } = useCart()

const form = reactive({
  recipient_name: '',
  phone: '',
  line1: '',
  line2: '',
  city: '',
  state: '',
  postal_code: '',
})

const showForm = ref(false)
const selectedAddressId = ref(null)
const message = ref('')

const canSubmit = computed(() => {
  if (cart.value.items.length === 0) {
    return false
  }

  if (selectedAddressId.value) {
    return true
  }

  return form.recipient_name && form.line1 && form.city
})

function formatPrice(value) {
  const amount = Number.parseInt(value ?? 0, 10) / 100
  return amount.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })
}

function toggleForm() {
  showForm.value = !showForm.value
}

function resetForm() {
  form.recipient_name = ''
  form.phone = ''
  form.line1 = ''
  form.line2 = ''
  form.city = ''
  form.state = ''
  form.postal_code = ''
  showForm.value = false
}

async function submitAddress() {
  try {
    const payload = { ...form }
    const newAddress = await createAddress(payload)
    selectedAddressId.value = newAddress.id
    resetForm()
  } catch (error) {
    const responseMessage = error?.response?.data?.message ?? 'Gagal menyimpan alamat.'
    alert(responseMessage)
  }
}

async function placeOrder() {
  if (!canSubmit.value) {
    return
  }

  try {
    const payload = selectedAddressId.value
      ? { address_id: selectedAddressId.value }
      : { address: { ...form } }

    const { data } = await axios.post('/api/checkout', payload)
    message.value = 'Pesanan berhasil dibuat! Kamu bisa melanjutkan ke pembayaran nanti.'
    await loadCart(true)

    setTimeout(() => {
      router.push({ name: 'home' })
    }, 2500)
  } catch (error) {
    const responseMessage = error?.response?.data?.message ?? 'Checkout gagal. Coba lagi.'
    alert(responseMessage)
  }
}

watch(addresses, (items) => {
  if (!items?.length) {
    selectedAddressId.value = null
    return
  }

  const defaultAddress = items.find(address => address.is_default)
  selectedAddressId.value = defaultAddress ? defaultAddress.id : items[0].id
})

onMounted(async () => {
  await Promise.all([loadAddresses(true), loadCart()])

  if (!cart.value.items.length) {
    router.push({ name: 'cart' })
  }
})
</script>
