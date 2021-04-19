import request from '@/utils/request'

export function guardarDeposito(data) {
  return request({
    url: '/transaccion/deposito',
    method: 'post',
    data
  })
}

export function guardarRetiro(data) {
  return request({
    url: '/transaccion/retiro',
    method: 'post',
    data
  })
}
