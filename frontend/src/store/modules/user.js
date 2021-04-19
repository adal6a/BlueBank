import { login, logout } from '@/api/auth'
import { getToken, setToken, removeToken } from '@/utils/auth'
import { resetRouter } from '@/router'
import { detalleUsuario } from '@/api/user'

const state = {
  token: getToken(),
  name: '',
  avatar: '',
  introduction: '',
  rol: null,
  user: null
}

const mutations = {
  SET_USER: (state, user) => {
    state.user = user
  },
  SET_TOKEN: (state, token) => {
    state.token = token
  },
  SET_ROL: (state, rol) => {
    state.rol = rol
  }
}

const actions = {
  // user login
  login({ commit }, userInfo) {
    const { usuario, password } = userInfo
    return new Promise((resolve, reject) => {
      login({ usuario: usuario.trim(), password: password }).then(response => {
        const data = response.data
        commit('SET_USER', data.usuario)
        commit('SET_TOKEN', data.token)
        setToken(data.token)

        commit('SET_ROL', data.usuario.tipo)
        resolve()
      }).catch(error => {
        reject(error)
      })
    })
  },

  // user logout
  logout({ commit, state, dispatch }) {
    return new Promise((resolve, reject) => {
      logout(state.token).then(() => {
        commit('SET_TOKEN', '')
        commit('SET_ROL', null)
        removeToken()
        resetRouter()

        // reset visited views and cached views
        // to fixed https://github.com/PanJiaChen/vue-element-admin/issues/2485
        dispatch('tagsView/delAllViews', null, { root: true })

        resolve()
      }).catch(error => {
        reject(error)
      })
    })
  },

  detalleUsuario({ commit }) {
    return new Promise((resolve, reject) => {
      detalleUsuario().then(respuesta => {
        const datos = respuesta.data;

        commit('SET_USER', datos);
        commit('SET_ROL', datos.tipo)
        resolve()
      }).catch(error => {
        reject(error)
      })
    })
  },

  // remove token
  resetToken({ commit }) {
    return new Promise(resolve => {
      commit('SET_TOKEN', '')
      commit('SET_ROL', null)
      removeToken()
      resolve()
    })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
