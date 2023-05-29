<template>
  <q-page padding>
    <q-table
      title="Expenses list"
      :rows="expenses"
      :columns="columns"
      row-key="name"
    >
      <template v-slot:top>
        <q-toolbar>
          <span class="text-h5">Expenses list</span>
          <q-space/>
          <q-btn
            color="primary"
            label="Add expense"
            icon="add"
            :to="{ name: 'formExpenses' }"
          />
        </q-toolbar>
      </template>

      <template v-slot:body-cell-actions="props">
        <q-td :props="props" class="q-gutter-sm">
          <q-btn
            color="primary"
            dense
            flat
            round
            icon="edit"
            @click="editExpense(props.row.id)"
          />
          <q-btn
            color="negative"
            dense
            flat
            round
            icon="delete"
            @click="removeExpense(props.row.id)"
          />
        </q-td>
      </template>

    </q-table>
  </q-page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue'
import { api } from 'boot/axios'
import { useQuasar, date } from 'quasar'
import { useRouter } from 'vue-router'
import ErrorService from 'src/services/errorService'

export default defineComponent({
  name: 'homePage',
  setup () {
    const expenses = ref([])
    const columns = [
      { name: 'id', label: 'ID', field: 'id', sortable: true, aling: 'left' },
      { name: 'date', label: 'Date', field: 'date', sortable: true, aling: 'left', format: val => date.formatDate(val, 'DD/MM/YYYY') },
      { name: 'description', label: 'Description', field: 'description', sortable: true, aling: 'left' },
      { name: 'amount', label: 'Amount', field: 'amount', sortable: true, aling: 'left', format: val => parseFloat(val).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) },
      { name: 'actions', label: '', field: 'actions', aling: 'left' }

    ]

    const $q = useQuasar()
    const $router = useRouter()

    onMounted(() => {
      getExpenses()
    })

    const getExpenses = async () => {
      try {
        let { data } = await api.get('expenses')
        data = data.data

        expenses.value = data.data
      } catch (error) {
        ErrorService.handlePageError(error, $router, $q)
      }
    }

    const editExpense = async (id) => {
      $router.push({ name: 'formExpenses', params: { id } })
    }

    const removeExpense = async (id) => {
      try {
        $q.dialog({
          title: 'Confirm',
          message: 'Are you sure to delete the expense?',
          cancel: true,
          persistent: true
        }).onOk(async () => {
          await api.delete(`expenses/${id}`)
          $q.notify({ message: 'Remove expense success', icon: 'check', color: 'positive' })
          await getExpenses()
        })
      } catch (error) {
        $q.notify({ message: 'Remove expense error', icon: 'times', color: 'negative' })
      }
    }

    return {
      expenses,
      columns,
      editExpense,
      removeExpense
    }
  }
})
</script>
