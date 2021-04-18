import { asyncRoutes, constantRoutes } from '@/router'

/**
 * Use meta.role to determine if the current user has permission
 * @param rol
 * @param route
 */
function hasPermission(rol, route) {
  if (route.meta && route.meta.roles) {
    return route.meta.roles.includes(rol)
  } else {
    return true
  }
}

/**
 * Filter asynchronous routing tables by recursion
 * @param routes asyncRoutes
 * @param rol
 */
export function filterAsyncRoutes(routes, rol) {
  const res = []

  routes.forEach(route => {
    const tmp = { ...route }
    if (hasPermission(rol, tmp)) {
      if (tmp.children) {
        tmp.children = filterAsyncRoutes(tmp.children, rol)
      }
      res.push(tmp)
    }
  })

  return res
}

const state = {
  routes: [],
  addRoutes: []
}

const mutations = {
  SET_ROUTES: (state, routes) => {
    state.addRoutes = routes
    state.routes = constantRoutes.concat(routes)
  }
}

const actions = {
  generateRoutes({ commit }, rol) {
    return new Promise(resolve => {
      const accessedRoutes = filterAsyncRoutes(asyncRoutes, rol)
      commit('SET_ROUTES', accessedRoutes)
      resolve(accessedRoutes)
    })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
