import request from '@/utils/request'

export function guardarUsuario(data) {
  return request({
    url: '/user',
    method: 'post',
    data
  })
}

export function cargaUsuarios() {
  return request({
    url: '/users'
  })
}

