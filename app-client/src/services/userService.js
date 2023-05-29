import { api } from 'boot/axios'
import { Cookies } from 'quasar'

function get () {
  return api.get('/user')
    .then(response => response.data.data)
}

/**
 * Limpar sessão e deslogar o usuário atual.
 */
async function logout () {
  const token = Cookies.get('access_token')

  if (token) {
    try {
      await api.post('/logout')
      Cookies.remove('access_token')
    } catch (error) { /* ignore */ }
  }

  sessionStorage.clear()
  return true
}

async function register (name, email, password) {
  let { data } = await api.post('/register', { name, email, password })
  data = data.data

  const authorisation = data.authorisation || {}
  if (await accessToken(authorisation)) {
    return data
  } else { throw new Error('Error registering user') }
}

async function login (email, password) {
  let { data } = await api.post('/login', { email, password })
  data = data.data

  const authorisation = data.authorisation || {}

  accessToken(authorisation)
  return data
}

async function accessToken (authorisation) {
  const token = authorisation.token
  const expires = parseInt(authorisation.expires_in) * 1000

  if (token && expires) {
    const options = {
      expires,
      secure: true,
      sameSite: 'strict'
    }

    Cookies.set('access_token', token, options)
    return true
  }

  return false
}

export default {
  get,
  logout,
  login,
  register
}
