const getters = {
  sidebar: state => state.app.sidebar,
  size: state => state.app.size,
  device: state => state.app.device,
  visitedViews: state => state.tagsView.visitedViews,
  cachedViews: state => state.tagsView.cachedViews,
  token: state => state.user.token,
  avatar: state => state.user.avatar,
  name: state => state.user.name,
  introduction: state => state.user.introduction,
  rol: state => state.user.rol,
  permission_routes: state => state.permission.routes,

  // Cuentas
  cuentas: state => state.cuenta.cuentas,
  cuenta: state => state.cuenta.cuenta,

  //Transacciones
  modalTransaccionVisible: state => state.transaccion.modalTransaccionVisible,
  tipoTransaccion: state => state.transaccion.tipoTransaccion,
}

export default getters
