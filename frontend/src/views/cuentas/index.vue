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
          type="primary"
          @click="nuevaCuenta"
        >
          Agregar cuenta
        </el-button>
      </el-col>
    </el-row>

    <el-row :gutter="24">
      <el-table
        v-loading="cargandoUsuarios"
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
          prop="balance"
        />
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
          width="200"
        >
          <template slot-scope="scope">
            <el-tooltip
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
      width="70%"
      title="Formulario usuario"
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
          <el-col :span="8">
            <el-form-item
              label="Nombre"
              prop="nombre"
            >
              <el-input
                v-model="formularioCuenta.numero"
              />
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <span
        slot="footer"
        class="dialog-footer"
      >
        <el-button @click="formularioCuentaVisible = false">Cerrar</el-button>
        <el-button
          type="primary"
          @click="validaFormularioCuenta"
        >Guardar</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'Index',
  data() {
    return {
      search: '',
      pagina: 1,
      tamanioPagina: 5,
      total: 0,

      formularioCuentaVisible: false,
      usuarios: [],
      cargandoUsuarios: true,
      formularioErrores: []
    }
  },
  computed: {
    ...mapGetters([
      'usuarioCuenta',
      'cuentas',
      'formularioCuenta'
    ]),
    reglas() {
      return {
        nombre: [
          { required: true, message: 'El campo es obligatorio.', trigger: 'change' }
        ],
        apellido: [
          { required: true, message: 'El campo es obligatorio.', trigger: 'change' }
        ],
        identificacion: [
          { required: true, message: 'El campo es obligatorio.', trigger: 'change' }
        ],
        usuario: [
          { required: true, message: 'El campo es obligatorio.', trigger: 'change' }
        ],
        correo: [
          { required: true, message: 'El campo es obligatorio.', trigger: 'change' }
        ],
        password: [
          { required: this.formularioCuenta.id === null, message: 'El campo es obligatorio.', trigger: 'change' }
        ]
      }
    },
    cuentasPaginadas() {
      this.total = this.cuentas.length

      return this.cuentas.slice(this.tamanioPagina * this.pagina - this.tamanioPagina, this.tamanioPagina * this.pagina)
    }
  },
  created() {
    this.cargaCuentas()
  },
  methods: {
    cargaCuentas() {
      this.$store.dispatch('cuenta/obtenerCuentas');
      this.cargandoUsuarios = false
    },
    seleccionaPagina(val) {
      this.pagina = val
    },
    nuevaCuenta() {
      this.$store.dispatch('cuenta/limpiaFormulario');
      this.formularioCuentaVisible = true
      this.$nextTick(() => {
        this.$refs['formularioCuenta'].clearValidate()
      })
    },
    validaFormularioCuenta() {
      this.$refs['formularioCuenta'].validate((valid) => {
        if (valid) {
          if (this.formularioCuenta.id !== null) {
            this.actualizaUsuario();
          } else {
            this.guardarUsuario();
          }
        } else {
          return false
        }
      })
    },
    guardarUsuario() {

    },
    editarCuenta(scopeRow) {
      this.formularioCuentaVisible = true
    },
    actualizaUsuario() {

    }
  }
}
</script>

<style lang="scss" scoped>

</style>
