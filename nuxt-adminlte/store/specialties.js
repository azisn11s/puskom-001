export const state = () => ({
  specialties: []
})

// getters
export const getters = {
  getSpecialties(state) {
    return state.specialties
  }
}

// Mutations
export const mutations = {
  STORE_SPECIALTIES(state, specialties) {
    state.specialties = specialties
  }
}

// Actions
export const actions = {
  storeSpecialties({
    commit
  }, specialties) {
    commit('STORE_SPECIALTIES', specialties)
  }
}
