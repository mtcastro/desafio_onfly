import UserService from 'src/services/userService.js'

/**
 * Tratar resposta de erro em um carregamento de página.
 *
 * @param error   Resposta de erro do axios.
 * @param [next]  Função de navegação do vue-router.
 */
function handlePageError (error, next, quasar) {
  const defaultAlert = () => {
    quasar.dialog.alert('An error occurred while trying to process your request.')
  }

  if (typeof error.response.status !== 'undefined') {
    const status = error.response.status

    if (status === 401) {
      UserService.logout()
      redirect('/login', next)
    } else if (status === 403) {
      const errorMessage = 'You do not have permission to access this page.'
      quasar.notify({ message: errorMessage, color: 'red-5', icon: 'report_problem' })
      redirect('/', next)
    } else if (status === 404) {
      redirect('/404', next)
    } else {
      defaultAlert()
    }
  } else {
    defaultAlert()
  }
}

function handleFormError (error, vm, quasar) {
  if (error.response) {
    const status = error.response.status

    if (status === 401) {
      UserService.logout()
      redirect('/login', vm.$router.push)
    } else if (status === 403) {
      quasar.notify({ message: 'You do not have permission to access this page.', color: 'red-5', icon: 'report_problem' })
      redirect('/', vm.$router.push)
    }
  }
}

/**
 * Redirecionamento de página usando next ou location.
 * @private
 *
 * @param {string}   to         Endereço de destino
 * @param {Function} [next]     Função de navegação do vue-router
 * @param {Object}   [options]  Opções para a função next()
 */
function redirect (to, next, options) {
  if (typeof next !== 'undefined') {
    let _options = { path: to }

    if (typeof options === 'object') {
      _options = Object.assign(_options, options)
    }

    next.push(_options)
  } else {
    window.location.href = to
  }
}

const ErrorService = {
  handlePageError,
  handleFormError
}

export default ErrorService
