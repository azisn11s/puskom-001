export const state = () => ({
  classifications: []
})

// getters
export const getters = {
  getClassifications(state) {
    return state.classifications
  }
}

// Mutations
export const mutations = {
  STORE_CLASSIFICATIONS(state, classifications) {
    state.classifications = classifications
  }
}

// Actions
export const actions = {
  storeClassifications({
    commit
  }, classifications) {
    commit('STORE_CLASSIFICATIONS', classifications)
  }
}
