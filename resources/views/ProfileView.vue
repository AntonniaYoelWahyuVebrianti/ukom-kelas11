<template>
  <section class="px-6 py-12">
    <div class="mx-auto flex max-w-4xl flex-col gap-8">
      <header class="space-y-2">
        <h1 class="text-3xl font-bold text-gray-900">Profil & Alamat</h1>
        <p class="text-sm text-gray-500">Kelola alamat pengiriman untuk mempercepat proses checkout.</p>
      </header>

      <div class="space-y-6">
        <div v-if="loading" class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm text-gray-500">
          Memuat data alamat…
        </div>

        <div v-else class="space-y-6">
          <section v-if="addresses.length" class="space-y-4">
            <article
              v-for="address in addresses"
              :key="address.id"
              class="rounded-2xl border border-gray-100 bg-white px-5 py-4 shadow-sm"
            >
              <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                  <p class="text-sm font-semibold text-gray-900">{{ address.recipient_name }}</p>
                  <p class="text-xs text-gray-500">{{ address.line1 }}<span v-if="address.line2">, {{ address.line2 }}</span></p>
                  <p class="text-[11px] text-gray-400">{{ [address.city, address.state, address.postal_code, address.country].filter(Boolean).join(', ') }}</p>
                  <p v-if="address.phone" class="mt-1 text-[11px] text-gray-400">Telp: {{ address.phone }}</p>
                </div>

                <div class="flex flex-col items-start gap-2 md:items-end">
                  <span v-if="address.is_default" class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-700">Default</span>
                  <div class="flex gap-2 text-xs">
                    <button class="rounded-full border border-gray-200 px-3 py-1 font-semibold text-gray-500 hover:border-sky-200 hover:text-sky-600" @click="edit(address)">Edit</button>
                    <button class="rounded-full border border-red-100 px-3 py-1 font-semibold text-red-500 hover:border-red-200" @click="remove(address)">Hapus</button>
                  </div>
                  <button
                    v-if="!address.is_default"
                    class="text-[11px] font-semibold text-sky-600 hover:text-sky-700"
                    @click="setDefault(address)"
                  >
                    Jadikan default
                  </button>
                </div>
              </div>
            </article>
          </section>

          <div v-else class="rounded-2xl border border-dashed border-sky-200 bg-sky-50/60 px-5 py-6 text-sm text-sky-700">
            Belum ada alamat tersimpan. Tambahkan alamat pertamamu di bawah.
          </div>

          <form class="grid gap-4 rounded-2xl border border-gray-100 bg-gray-50/60 px-5 py-6" @submit.prevent="submit">
            <div class="flex items-center justify-between">
              <p class="text-sm font-semibold text-gray-800">{{ form.id ? 'Edit alamat' : 'Alamat baru' }}</p>
              <button v-if="form.id" type="button" class="text-xs font-semibold text-gray-400 hover:text-gray-500" @click="resetForm">
                Batalkan edit
              </button>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label class="text-xs font-semibold text-gray-600" for="profile-address-name">Nama penerima</label>
                <input id="profile-address-name" v-model="form.recipient_name" type="text" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200" required>
              </div>
              <div>
                <label class="text-xs font-semibold text-gray-600" for="profile-address-phone">No. Telp</label>
                <input id="profile-address-phone" v-model="form.phone" type="text" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200">
              </div>
            </div>
            <div>
              <label class="text-xs font-semibold text-gray-600" for="profile-address-line1">Alamat lengkap</label>
              <input id="profile-address-line1" v-model="form.line1" type="text" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200" required>
            </div>
            <div>
              <label class="text-xs font-semibold text-gray-600" for="profile-address-line2">Detail tambahan</label>
              <input id="profile-address-line2" v-model="form.line2" type="text" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200" placeholder="Blok / patokan (opsional)">
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
              <div>
                <label class="text-xs font-semibold text-gray-600" for="profile-address-city">Kota</label>
                <input id="profile-address-city" v-model="form.city" type="text" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200" required>
              </div>
              <div>
                <label class="text-xs font-semibold text-gray-600" for="profile-address-state">Provinsi</label>
                <input id="profile-address-state" v-model="form.state" type="text" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200">
              </div>
              <div>
                <label class="text-xs font-semibold text-gray-600" for="profile-address-postal">Kode pos</label>
                <input id="profile-address-postal" v-model="form.postal_code" type="text" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-sky-400 focus:ring-2 focus:ring-sky-200">
              </div>
            </div>
            <label class="flex items-center gap-2 text-xs text-gray-600">
              <input type="checkbox" v-model="form.is_default" class="rounded border-sky-200 text-sky-600 focus:ring-sky-400">
              Jadikan alamat default
            </label>
            <div class="flex items-center justify-end gap-3">
              <button type="reset" class="rounded-full border border-gray-200 px-4 py-2 text-xs font-semibold text-gray-500" @click.prevent="resetForm">Reset</button>
              <button type="submit" class="rounded-full bg-sky-600 px-5 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow hover:bg-sky-700" :disabled="loading">
                {{ form.id ? 'Update alamat' : 'Simpan alamat' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, reactive } from 'vue'
import { useAddresses } from '../js/composables/useAddresses'

const { addresses, loadAddresses, createAddress, updateAddress, removeAddress, setDefaultAddress, loading } = useAddresses()

const form = reactive({
  id: null,
  recipient_name: '',
  phone: '',
  line1: '',
  line2: '',
  city: '',
  state: '',
  postal_code: '',
  is_default: false,
})

function resetForm() {
  form.id = null
  form.recipient_name = ''
  form.phone = ''
  form.line1 = ''
  form.line2 = ''
  form.city = ''
  form.state = ''
  form.postal_code = ''
  form.is_default = false
}

function edit(address) {
  Object.assign(form, address)
}

async function submit() {
  const payload = {
    recipient_name: form.recipient_name,
    phone: form.phone,
    line1: form.line1,
    line2: form.line2,
    city: form.city,
    state: form.state,
    postal_code: form.postal_code,
    is_default: form.is_default,
  }

  try {
    if (form.id) {
      await updateAddress(form.id, payload)
    } else {
      await createAddress(payload)
    }
    resetForm()
  } catch (error) {
    const message = error?.response?.data?.message ?? 'Gagal menyimpan alamat.'
    alert(message)
  }
}

async function remove(address) {
  if (!confirm('Hapus alamat ini?')) {
    return
  }

  try {
    await removeAddress(address.id)
    if (form.id === address.id) {
      resetForm()
    }
  } catch (error) {
    alert('Gagal menghapus alamat.')
  }
}

async function setDefault(address) {
  try {
    await setDefaultAddress(address.id)
  } catch (error) {
    alert('Tidak bisa mengubah alamat default.')
  }
}

onMounted(() => {
  loadAddresses(true)
})
</script>
