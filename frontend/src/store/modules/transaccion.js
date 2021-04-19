const state = {
  modalTransaccionVisible: false,
  tipoTransaccion: ''
}

const mutations = {
  SET_MODAL_VISIBLE: (state, estado) => {
    state.modalTransaccionVisible = estado
  },

  SET_TIPO_TRANSACCION: (state, tipo) => {
    state.tipoTransaccion = tipo
  },
}

const actions = {
  modalTransaccionVisible({ commit }, estado) {
    commit('SET_MODAL_VISIBLE', estado);
  },

  tipoTransaccion({ commit }, tipo) {
    commit('SET_TIPO_TRANSACCION', tipo);
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
