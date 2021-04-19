import { obtenerCuentas } from '@/api/cuenta'

const state = {
  cuentas: [],
  cuenta: {
    id: null,
    balance: null
  }
}

const mutations = {
  SET_CUENTAS: (state, cuentas) => {
    state.cuentas = cuentas
  },

  ADD_CUENTA: (state, cuenta) => {
    state.cuentas.push(cuenta)
  },

  UPDATE_CUENTA: (state, cuentaActualizada) => {
    const index = state.cuentas.findIndex(cuenta => cuenta.id === cuentaActualizada.id)

    Object.assign(state.cuentas[index], cuentaActualizada)
  },

  SET_CUENTA: (state, cuenta) => {
    state.cuenta = cuenta
  }
}

const actions = {
  obtenerCuentas({ commit }, datos) {
    return new Promise((resolve, reject) => {
      obtenerCuentas(datos).then(respuesta => {
        commit('SET_CUENTAS', respuesta.data)
        resolve()
      }).catch(error => {
        reject(error)
      })
    })
  },

  guardaCuenta({ commit }, cuenta) {
    commit('ADD_CUENTA', cuenta)
  },

  actualizaCuenta({ commit }, cuentaActualizada) {
    commit('UPDATE_CUENTA', cuentaActualizada)
  },

  seleccionaCuenta({ commit }, cuenta) {
    commit('SET_CUENTA', cuenta)
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
