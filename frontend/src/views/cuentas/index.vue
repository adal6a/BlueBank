<template>
  <div class="app-container">
    <el-row :gutter="24">
      <el-col :span="16">
        <h2>Cuentas del cliente {{ usuarioCuenta.nombre }} {{ usuarioCuenta.apellido }}</h2>
      </el-col>
      <el-col
        :span="8"
        align="right"
        style="padding-top: 15px"
      >
        <el-button
          v-if="rol === 'empleado'"
          type="primary"
          @click="nuevaCuenta"
        >
          Agregar cuenta
        </el-button>
      </el-col>
    </el-row>

    <el-row :gutter="24">
      <el-table
        v-loading="cargandoCuentas"
        :data="cuentasPaginadas"
        stripe
        style="width: 100%"
      >
        <el-table-column
          label="Número de cuenta"
          prop="numero"
        />
        <el-table-column
          label="Balance"
        >
          <template slot-scope="scope">
            $ {{ scope.row.balance }}
          </template>
        </el-table-column>
        <el-table-column
          label="Estado"
        >
          <template slot-scope="scope">
            <el-tag :type="scope.row.activo ? 'success' : 'warning'">
              {{ scope.row.activo ? 'Activa' : 'Inactiva' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column
          label="Acciones"
          align="center"
          width="300"
        >
          <template slot-scope="scope">
            <el-tooltip
              v-if="rol === 'empleado'"
              class="item"
              effect="dark"
              content="Editar"
              placement="top"
            >
              <el-button
                size="mini"
                type="primary"
                icon="el-icon-edit"
                @click="editarCuenta(scope.row)"
              />
            </el-tooltip>

            <el-tooltip
              class="item"
              effect="dark"
              content="Depositar"
              placement="top"
            >
              <el-button
                size="mini"
                type="primary"
                icon="el-icon-sold-out"
                :disabled="!scope.row.activo"
                @click="depositar(scope.row)"
              />
            </el-tooltip>

            <el-tooltip
              class="item"
              effect="dark"
              content="Retirar"
              placement="top"
            >
              <el-button
                size="mini"
                type="primary"
                icon="el-icon-sell"
                :disabled="!scope.row.activo"
                @click="retirar(scope.row)"
              />
            </el-tooltip>

            <el-tooltip
              class="item"
              effect="dark"
              content="Transacciones"
              placement="top"
            >
              <el-button
                size="mini"
                type="primary"
                icon="el-icon-s-data"
                :loading="cargandoTransacciones"
                @click="verTransacciones(scope.row)"
              />
            </el-tooltip>
          </template>
        </el-table-column>
      </el-table>

      <el-divider />

      <div style="text-align: right">
        <el-pagination
          background
          layout="prev, pager, next"
          :page-size="tamanioPagina"
          :total="total"
          @current-change="seleccionaPagina"
        />
      </div>
    </el-row>

    <el-dialog
      :visible.sync="formularioCuentaVisible"
      top="3vh"
      width="30%"
      title="Formulario cuenta de ahorro"
      :close-on-click-modal="false"
    >
      <el-alert v-if="formularioErrores.length > 0" type="error">
        <ul>
          <li v-for="(error, index) in formularioErrores" :key="index"><strong> {{ error }} </strong></li>
        </ul>
      </el-alert>

      <el-form
        ref="formularioCuenta"
        :model="formularioCuenta"
        :rules="reglas"
      >
        <el-row :gutter="24">
          <el-col :span="24">
            <el-form-item
              label="Balance"
              prop="balance"
            >
              <el-input
                v-model="formularioCuenta.balance"
                type="number"
                :disabled="formularioCuenta.id !== null"
              />
            </el-form-item>
          </el-col>
        </el-row>

        <el-row :gutter="24">
          <el-col :span="24">
            <el-switch
              v-model="formularioCuenta.activo"
              :disabled="formularioCuenta.id === null"
              style="display: block"
              active-color="#13ce66"
              inactive-color="#ff4949"
              active-text="Activa"
              inactive-text="Inactiva"
            />
          </el-col>
        </el-row>
      </el-form>
      <span
        slot="footer"
        class="dialog-footer"
      >
        <el-button @click="formularioCuentaVisible = false">Cerrar</el-button>
        <el-button
          :loading="guardando"
          type="primary"
          @click="validaFormularioCuenta"
        >Guardar</el-button>
      </span>
    </el-dialog>

    <el-dialog title="Transacciones" :visible.sync="modalTransaccionesVisible">
      <el-table :data="transacciones" size="mini">
        <el-table-column property="monto" :label="'$ Monto'" />
        <el-table-column label="Fecha y hora">
          <template slot-scope="scope">
            {{ formateaFecha(scope.row.fecha_hora) }}
          </template>
        </el-table-column>
        <el-table-column label="Tipo">
          <template slot-scope="scope">
            <el-tag :type="scope.row.tipo === 'deposito' ? 'success' : ''">
              {{ scope.row.tipo === 'deposito' ? 'Depósito' : 'Retiro' }}
            </el-tag>
          </template>
        </el-table-column>
      </el-table>
    </el-dialog>

    <ModalTransaccion />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

import { guardarCuenta, actualizaCuenta, transaccionesCuenta } from '@/api/cuenta'
import ModalTransaccion from '@/views/cuentas/transaccion/index'
export default {
  name: 'Index',
  components: {
    ModalTransaccion
  },
  data() {
    return {
      search: '',
      pagina: 1,
      tamanioPagina: 5,
      total: 0,

      guardando: false,

      formularioCuentaVisible: false,
      cargandoCuentas: false,
      usuarioCuenta: null,
      formularioCuenta: {
        id: null,
        numero: '',
        balance: '',
        user_id: '',
        catalogobanco_id: '1',
        activo: true
      },
      formularioErrores: [],

      modalTransaccionesVisible: false,
      cargandoTransacciones: false,
      transacciones: []
    }
  },
  computed: {
    ...mapGetters([
      'cuentas',
      'user',
      'rol'
    ]),
    reglas() {
      return {
        balance: [
          { required: true, message: 'El campo es obligatorio.', trigger: 'change' }
        ]
      }
    },
    cuentasPaginadas() {
      this.total = this.cuentas.length

      return this.cuentas.slice(this.tamanioPagina * this.pagina - this.tamanioPagina, this.tamanioPagina * this.pagina)
    }
  },
  created() {
    if (this.rol === 'cliente') {
      this.usuarioCuenta = this.user
    } else {
      this.usuarioCuenta = JSON.parse(localStorage.getItem('usuarioCuenta'))
    }

    this.cargaCuentas()
  },
  methods: {
    cargaCuentas() {
      this.cargandoCuentas = true
      this.$store.dispatch('cuenta/obtenerCuentas', {
        user_id: this.usuarioCuenta.id
      }).then(() => {
        this.cargandoCuentas = false
      })
    },
    seleccionaPagina(val) {
      this.pagina = val
    },
    nuevaCuenta() {
      this.limpiaFormulario()
      this.formularioCuentaVisible = true
      this.$nextTick(() => {
        this.$refs['formularioCuenta'].clearValidate()
      })
    },
    validaFormularioCuenta() {
      this.$refs['formularioCuenta'].validate((valid) => {
        if (valid) {
          this.guardando = true
          if (this.formularioCuenta.id !== null) {
            this.actualizaCuenta()
          } else {
            this.guardarCuenta()
          }
        } else {
          return false
        }
      })
    },
    guardarCuenta() {
      guardarCuenta(this.formularioCuenta).then(respuesta => {
        if (respuesta.success) {
          this.$store.dispatch('cuenta/guardaCuenta', respuesta.data)

          this.$message.success(respuesta.message)
          this.formularioCuentaVisible = false
        } else {
          this.formularioErrores = respuesta.errors
        }

        this.guardando = false
      })
    },
    editarCuenta(scopeRow) {
      this.formularioCuenta = {
        id: scopeRow.id,
        numero: scopeRow.numero,
        balance: scopeRow.balance,
        user_id: this.usuarioCuenta.id,
        catalogobanco_id: '1',
        activo: scopeRow.activo
      }

      this.formularioCuentaVisible = true
    },
    actualizaCuenta() {
      actualizaCuenta(this.formularioCuenta).then(respuesta => {
        if (respuesta.success) {
          this.$store.dispatch('cuenta/actualizaCuenta', respuesta.data)
          this.$message.success(respuesta.message)
          this.formularioCuentaVisible = false
        } else {
          this.formularioErrores = respuesta.errors
        }

        this.guardando = false
      })
    },
    limpiaFormulario() {
      this.formularioCuenta = {
        id: null,
        numero: '',
        balance: '',
        user_id: this.usuarioCuenta.id,
        catalogobanco_id: '1',
        activo: true
      }

      this.formularioErrores = []
    },
    depositar(scopeRow) {
      this.$store.dispatch('cuenta/seleccionaCuenta', scopeRow)
      this.$store.dispatch('transaccion/tipoTransaccion', 'deposito')
      this.$store.dispatch('transaccion/modalTransaccionVisible', true)
    },
    retirar(scopeRow) {
      this.$store.dispatch('cuenta/seleccionaCuenta', scopeRow)
      this.$store.dispatch('transaccion/tipoTransaccion', 'retiro')
      this.$store.dispatch('transaccion/modalTransaccionVisible', true)
    },
    verTransacciones(scopeRow) {
      this.cargandoTransacciones = true

      transaccionesCuenta({
        cuenta_id: scopeRow.id
      }).then(respuesta => {
        this.transacciones = respuesta.data

        this.cargandoTransacciones = false
        this.modalTransaccionesVisible = true
      })
    },
    formateaFecha(fechaHora) {
      const fechaHoraJs = new Date(fechaHora)

      const fecha = fechaHoraJs.toLocaleString('es-MX', {
        day: 'numeric',
        year: 'numeric',
        month: 'numeric'
      })

      const hora = fechaHoraJs.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })

      return `${fecha} ${hora}`
    }
  }
}
</script>

<style lang="scss" scoped>

</style>
