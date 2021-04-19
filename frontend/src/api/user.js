import request from '@/utils/request'

export function guardarUsuario(data) {
  return request({
    url: '/user',
    method: 'post',
    data
  })
}

export function actualizaUsuario(data) {
  return request({
    url: '/user/' + data.id,
    method: 'put',
    data
  })
}

export function cargaUsuarios() {
  return request({
    url: '/users'
  })
}

export function detalleUsuario() {
  return request({
    url: '/user',
    method: 'get'
  })
}
