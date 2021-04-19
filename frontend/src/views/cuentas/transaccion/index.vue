<template>
  <el-dialog
    :visible="modalTransaccionVisible"
    top="3vh"
    width="30%"
    title="Formulario transaccion"
    :close-on-click-modal="false"
  >
    <el-alert v-if="errores.length > 0" type="error">
      <ul>
        <li v-for="(error, index) in errores" :key="index"><strong> {{ error }} </strong></li>
      </ul>
    </el-alert>

    <el-form
      ref="formularioTransaccion"
      :model="formularioTransaccion"
      :rules="reglas"
    >
      <el-row :gutter="24">
        <el-col :span="24">
          <el-form-item
            :label="`$ Monto a ${tipoTransaccion === 'deposito' ? 'depositar' : 'retirar' }`"
            prop="monto"
          >
            <el-input
              v-model="formularioTransaccion.monto"
              type="number"
            />
          </el-form-item>
        </el-col>
      </el-row>
    </el-form>
    <span
      slot="footer"
      class="dialog-footer"
    >
        <el-button @click="cerrarModalTransaccion">Cerrar</el-button>
        <el-button
          :loading="guardando"
          type="primary"
          @click="validaFormularioTransaccion"
        >Guardar</el-button>
      </span>
  </el-dialog>
</template>

<script>
import { mapGetters } from 'vuex';
import { guardarDeposito, guardarRetiro } from '@/api/transaccion'

export default {
  name: "index",
  data() {
    return {
      errores: [],
      guardando: false,
      formularioTransaccion: {
        id: null,
        tipo: '',
        monto: '',
        cuenta_id: ''
      },
      reglas: {
        monto: [
          { required: true, message: 'El campo es obligatorio.', trigger: 'change' }
        ]
      }
    }
  },
  computed: {
    ...mapGetters([
      'cuenta',
      'tipoTransaccion',
      'modalTransaccionVisible'
    ])
  },
  watch: {
    modalTransaccionVisible: function (visible) {
      if (visible) {
        this.limpiarFormularioTransaccion();

        this.$nextTick(() => {
          this.$refs['formularioTransaccion'].clearValidate()
        });
      }
    },
    tipoTransaccion: function (tipo) {
      this.formularioTransaccion.tipo = tipo;
    },

    cuenta: function (cuenta) {
      this.formularioTransaccion.cuenta_id = cuenta.id;
    }
  },
  methods: {
    validaFormularioTransaccion() {
      this.$refs['formularioTransaccion'].validate((valid) => {
        if (valid) {
          this.guardando = true
          if (this.tipoTransaccion === 'deposito') {
            this.guardarDeposito()
          } else {

          }
        } else {
          return false
        }
      })
    },
    guardarDeposito() {
      guardarDeposito(this.formularioTransaccion).then(respuesta => {
        if (respuesta.success) {
          this.$store.dispatch('cuenta/actualizaCuenta', respuesta.data.cuenta);

          this.$message.success(respuesta.message)

          this.cerrarModalTransaccion();
        } else {
          this.errores = respuesta.errors
        }

        this.guardando = false
      });
    },
    limpiarFormularioTransaccion() {
      this.formularioTransaccion = {
        id: null,
        tipo: this.tipoTransaccion,
        monto: '',
        cuenta_id: this.cuenta.id
      };
    },
    cerrarModalTransaccion() {
      this.$store.dispatch('transaccion/modalTransaccionVisible', false);
    }
  }
}
</script>

<style scoped>

</style>
