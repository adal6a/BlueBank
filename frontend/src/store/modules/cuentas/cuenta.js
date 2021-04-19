import { obtenerCuentas } from '@/api/cuenta'

const cuentaState = () => {
  return {
    cuentas: [],
    usuarioCuenta: null,
    formularioCuenta: {
      id: null,
      numero: '',
      balance: '',
      user_id: '',
      catalogobanco_id: '1',
      activo: true
    }
  }
}

const state = cuentaState

const mutations = {
  SET_USUARIO_CUENTA: (state, usuario) => {
    state.usuarioCuenta = usuario
  },

  SET_CUENTAS: (state, cuentas) => {
    state.cuentas = cuentas
  },

  SET_FORMULARIO_CUENTA: (state, cuenta) => {

  },

  LIMPIA_FORMULARIO_CUENTA: (state) => {
    state.formularioCuenta = {
      id: null,
      numero: '',
      balance: '',
      user_id: '',
      catalogobanco_id: '1',
      activo: true
    }
  }
}

const actions = {
  usuarioCuenta({ commit }, usuario) {
    commit('SET_USUARIO_CUENTA', usuario)
  },

  obtenerCuentas({ commit, state }) {
    return new Promise((resolve, reject) => {
      obtenerCuentas({
        user_id: state.usuarioCuenta.id
      }).then(respuesta => {
        commit('SET_CUENTAS', respuesta.data)
        resolve()
      })
    })
  },
  limpiaFormulario({ commit }) {
    commit('LIMPIA_FORMULARIO_CUENTA')
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
