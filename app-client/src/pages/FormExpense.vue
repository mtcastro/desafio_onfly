<template>
  <q-page padding>
    <q-form
      @submit="onSubmit"
      class="q-col-gutter-sm"
    >
      <div class="q-toolbar">
        <span class="text-h6">Create new expense</span>
      </div>
      <q-input
        outlined
        v-model="form.description"
        label="Description of expense"
        lazy-rules
        form-error="Please enter a description"
        :rules="[ (val => val && val.length > 0 || 'Please enter a description'), formErrors.description || true ]"
      />
      <div class="row q-col-gutter-sm">
        <CustomCurrencyInput
          v-model="form.amount"
          :options="{ locale: 'pt-BR', currency: 'BRL' }"
          class="col-md-6"
          :role="[formErrors.amount]"
        />
        <q-input
          outlineds
          v-model="form.date"
          label="Date"
          type="date"
          class="col-md-6"
          lazy-rules
          :rules="[(val => val && val.length > 0 || 'Please enter a date'), formErrors.date || true]"
        />
      </div>
      <div class="q-gutter-sm">
        <q-btn
          type="submit"
          color="primary"
          label="Save"
          class="float-right"
          icon="save"
        />
        <q-btn
          type="reset"
          color="white"
          label="Cancel"
          class="float-right"
          text-color="primary"
          icon="cancel"
          :to="{ name: 'home' }"
        />
      </div>
    </q-form>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import CustomCurrencyInput from 'components/CustomCurrencyInput.vue'
import { api } from 'boot/axios'
import { useQuasar } from 'quasar'
import { useRouter, useRoute } from 'vue-router'
import ErrorService from 'src/services/errorService'

export default defineComponent({
  name: 'FormExpenses',

  components: {
    CustomCurrencyInput
  },

  setup () {
    const $q = useQuasar()
    const $router = useRouter()
    const $route = useRoute()
    const formErrors = ref({})
    const form = ref({
      description: '',
      amount: 0,
      date: ''
    })

    onMounted(async () => {
      const expenseId = $route.params.id
      if (expenseId) {
        try {
          let { data } = await api.get(`/expenses/${expenseId}`)
          data = data.data

          form.value = data
          form.value.amount = parseFloat(data.amount)
        } catch (error) {
          ErrorService.handlePageError(error, $router, $q)
          $router.push({ name: 'home' })
        }
      }
    })

    const onSubmit = async () => {
      try {
        if (form.value.id) {
          await api.put(`/expenses/${form.value.id}`, form.value)
          $q.notify({
            color: 'positive',
            message: 'Expense updated successfully',
            icon: 'check'
          })
        } else {
          await api.post('/expenses', form.value)
          $q.notify({
            color: 'positive',
            message: 'Expense created successfully',
            icon: 'check'
          })
        }
        $router.push({ name: 'home' })
      } catch (error) {
        const status = error.response.status
        const data = error.response.data || {}

        if ((status === 422) || (data.status && data.status === 'fail')) {
          formErrors.value = data.data || {}
        }

        $q.notify({
          color: 'negative',
          message: 'Error creating expense',
          icon: 'warning'
        })
        ErrorService.handleFormError(error, $router, $q)
      }
    }

    return {
      form,
      formErrors,
      onSubmit
    }
  }
})
</script>
