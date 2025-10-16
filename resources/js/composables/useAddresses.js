import { ref } from 'vue'
import axios from 'axios'

const addresses = ref([])
const loading = ref(false)
const error = ref(null)
const initialized = ref(false)

function setAddresses(data) {
  addresses.value = Array.isArray(data) ? data : []
}

export function useAddresses() {
  async function loadAddresses(force = false) {
    if (initialized.value && !force) {
      return
    }

    loading.value = true
    error.value = null

    try {
      const { data } = await axios.get('/api/addresses')
      setAddresses(data)
      initialized.value = true
    } catch (err) {
      error.value = err
      if (err?.response?.status === 401) {
        initialized.value = false
      }
    } finally {
      loading.value = false
    }
  }

  async function createAddress(payload) {
    loading.value = true
    error.value = null

    try {
      const { data } = await axios.post('/api/addresses', payload)
      addresses.value = [data, ...addresses.value]
      return data
    } catch (err) {
      error.value = err
      throw err
    } finally {
      loading.value = false
    }
  }

  async function updateAddress(id, payload) {
    loading.value = true
    error.value = null

    try {
      const { data } = await axios.put(`/api/addresses/${id}`, payload)
      addresses.value = addresses.value.map(address => (address.id === id ? data : address))
      return data
    } catch (err) {
      error.value = err
      throw err
    } finally {
      loading.value = false
    }
  }

  async function removeAddress(id) {
    loading.value = true
    error.value = null

    try {
      await axios.delete(`/api/addresses/${id}`)
      addresses.value = addresses.value.filter(address => address.id !== id)
    } catch (err) {
      error.value = err
      throw err
    } finally {
      loading.value = false
    }
  }

  async function setDefaultAddress(id) {
    loading.value = true
    error.value = null

    try {
      const { data } = await axios.post(`/api/addresses/${id}/default`)
      addresses.value = addresses.value.map(address => ({
        ...address,
        is_default: address.id === id,
      }))
      return data
    } catch (err) {
      error.value = err
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    addresses,
    loading,
    error,
    loadAddresses,
    createAddress,
    updateAddress,
    removeAddress,
    setDefaultAddress,
  }
}
