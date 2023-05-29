<template>
  <q-layout class="login-page">
    <q-page-container
      class="window-height window-width row justify-center items-center"
    >
      <div class="column q-pa-lg">
        <div class="row">
          <q-card
            square
            class="shadow-24"
          >
            <q-card-section class="bg-deep-purple-7">
              <h4 class="text-h5 text-white q-my-md">{{ title }}</h4>
            </q-card-section>
            <q-card-section>
              <q-fab
                color="primary"
                @click="switchTypeForm"
                icon="add"
                class="absolute btn-register"
              >
                <q-tooltip> Registration of a new user </q-tooltip>
              </q-fab>
              <q-form class="q-px-sm q-pt-xl">
                <q-input
                  ref="name"
                  v-if="register"
                  square
                  clearable
                  v-model="form.name"
                  lazy-rules
                  :rules="[required, short, formErrors.name || true]"
                  label="Name"
                >
                  <template v-slot:prepend>
                    <q-icon name="person" />
                  </template>
                </q-input>
                <q-input
                  ref="email"
                  square
                  clearable
                  v-model="form.email"
                  type="email"
                  :lazy-rules="false"
                  :rules="[required, isEmail, short, !formErrors.email || formErrors.email[0]]"
                  :reactive-rules="true"
                  label="Email"
                >
                  <template v-slot:prepend>
                    <q-icon name="email" />
                  </template>
                </q-input>
                <q-input
                  ref="password"
                  square
                  clearable
                  v-model="form.password"
                  :type="passwordFieldType"
                  lazy-rules
                  :rules="[required, short, formErrors.password || true]"
                  label="Password"
                >
                  <template v-slot:prepend>
                    <q-icon name="lock" />
                  </template>
                  <template v-slot:append>
                    <q-icon
                      :name="visibilityIcon"
                      @click="switchVisibility"
                      class="cursor-pointer"
                    />
                  </template>
                </q-input>
              </q-form>
            </q-card-section>

            <q-card-actions class="q-px-lg">
              <q-btn
                unelevated
                size="lg"
                color="secondary"
                @click="onSubmit"
                class="full-width text-white"
                :label="btnLabel"
              />
            </q-card-actions>
            <q-card-section v-if="!register" class="text-center q-pa-sm">
              <p class="text-grey-6"></p>
            </q-card-section>
          </q-card>
        </div>
      </div>
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent, ref } from 'vue'
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'
import ErrorService from 'src/services/errorService'
import userService from 'src/services/userService'

export default defineComponent({
  name: 'loginPage',
  setup () {
    const $q = useQuasar()
    const $router = useRouter()
    const formErrors = ref({})
    const form = ref({
      name: '',
      email: '',
      password: ''
    })
    const title = ref('Sing in')
    const register = ref(false)
    const passwordFieldType = ref('password')
    const btnLabel = ref('continue')
    const visibility = ref(false)
    const visibilityIcon = ref('visibility')

    const required = (val) => {
      return ((val && val.length > 0) || 'The field must be filled')
    }

    const short = (val) => {
      return ((val && val.length > 3) || 'The field must be filled')
    }

    const isEmail = (val) => {
      if (formErrors.value.email) {
        return formErrors.value.email[0]
      }
      const emailPattern = /^(?=[a-zA-Z0-9@._%+-]{6,254}$)[a-zA-Z0-9._%+-]{1,64}@(?:[a-zA-Z0-9-]{1,63}\.){1,8}[a-zA-Z]{2,63}$/
      return (emailPattern.test(val) || 'Invalid email')
    }

    const onSubmit = async () => {
      try {
        if (register.value) {
          if (!form.value.email.hasError && (!form.value.password.hasError)) {
            const data = await userService.register(form.value.name, form.value.email, form.value.password)
            if (data.user) {
              $router.push({ name: 'home' })
            }
            alert('register')
          } else {
            $q.notify({
              icon: 'error',
              color: 'negative',
              message: 'Form error'
            })
          }
          return
        }

        if (!register.value) {
          if (!form.value.email.hasError && (!form.value.password.hasError)) {
            const data = await userService.login(form.value.email, form.value.password)
            if (data.user) {
              $router.push({ name: 'home' })
            }
          } else {
            $q.notify({
              icon: 'error',
              color: 'negative',
              message: 'Authorization error'
            })
          }
        }
      } catch (error) {
        const status = error.response.status
        const data = error.response.data || {}

        if ((status === 422) || (data.status && data.status === 'fail')) {
          formErrors.value = data.data || {}
        }

        $q.notify({
          icon: 'error',
          color: 'negative',
          message: 'Error authorization'
        })

        ErrorService.handleFormError(error, $router, $q)
      }
    }

    const switchTypeForm = () => {
      register.value = !register.value
      title.value = register.value ? 'New user' : 'Sing in'
      btnLabel.value = register.value ? 'Registration' : 'Login'
    }

    const switchVisibility = () => {
      visibility.value = !visibility.value
      passwordFieldType.value = visibility.value ? 'text' : 'password'
      visibilityIcon.value = visibility.value ? 'visibility_off' : 'visibility'
    }

    return {
      form,
      formErrors,
      title,
      register,
      passwordFieldType,
      btnLabel,
      visibility,
      visibilityIcon,
      onSubmit,
      switchTypeForm,
      switchVisibility,
      required,
      short,
      isEmail

    }
  }
})
</script>

<style lang="scss">
  .login-page {
    .q-card {
      min-width: 375px;
    }

    .btn-register {
      top: 0;
      right: 12px;
      transform: translateY(-50%)
    }

    .q-page-container{
      background: linear-gradient(#a897ff, #5463b8);
    }
  }
</style>
```
