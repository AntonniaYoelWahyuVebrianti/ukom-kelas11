import { computed, ref } from 'vue'
import axios from 'axios'

const cart = ref({ items: [], subtotal: 0 })
const loading = ref(false)
const error = ref(null)
const initialized = ref(false)

function setCart(payload) {
  cart.value = {
    items: payload?.items ?? [],
    subtotal: payload?.subtotal ?? 0,
  }
}

export function useCart() {
  async function loadCart(force = false) {
    if (initialized.value && !force) {
      return
    }

    loading.value = true

    try {
      const { data } = await axios.get('/api/cart')
      setCart(data)
      initialized.value = true
    } catch (err) {
      error.value = err
    } finally {
      loading.value = false
    }
  }

  async function addToCart(productId, quantity = 1) {
    loading.value = true
    error.value = null

    try {
      const { data } = await axios.post('/api/cart', {
        product_id: productId,
        quantity,
      })
      setCart(data)
      initialized.value = true
      return data
    } catch (err) {
      error.value = err
      throw err
    } finally {
      loading.value = false
    }
  }

  async function updateQuantity(itemId, quantity) {
    loading.value = true
    error.value = null

    try {
      const { data } = await axios.patch(`/api/cart/${itemId}`, {
        quantity,
      })
      setCart(data)
      return data
    } catch (err) {
      error.value = err
      throw err
    } finally {
      loading.value = false
    }
  }

  async function removeItem(itemId) {
    loading.value = true
    error.value = null

    try {
      const { data } = await axios.delete(`/api/cart/${itemId}`)
      setCart(data)
      return data
    } catch (err) {
      error.value = err
      throw err
    } finally {
      loading.value = false
    }
  }

  async function claimCart() {
    try {
      const { data } = await axios.post('/api/cart/claim')
      if (data?.items) {
        setCart(data)
      }
    } catch (err) {
      // ignore but store for debugging
      error.value = err
    }
  }

  const itemCount = computed(() => cart.value.items.reduce((total, item) => total + item.quantity, 0))

  return {
    cart,
    loading,
    error,
    itemCount,
    loadCart,
    addToCart,
    updateQuantity,
    removeItem,
    claimCart,
  }
}
