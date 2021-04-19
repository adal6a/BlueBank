import request from '@/utils/request'

export function obtenerCuentas(data) {
  return request({
    url: '/cuentas/user',
    method: 'post',
    data
  })
}

export function guardarCuenta(data) {
  return request({
    url: '/cuenta',
    method: 'post',
    data
  })
}
