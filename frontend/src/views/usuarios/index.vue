<template>
  <div class="app-container">
    <el-row :gutter="24">
      <el-col :span="16">
        <h2>Usuarios</h2>
      </el-col>
      <el-col
        :span="8"
        align="right"
        style="padding-top: 15px"
      >
        <el-button
          type="primary"
          @click="nuevoUsuario"
        >
          Agregar usuario
        </el-button>
      </el-col>
    </el-row>

    <el-row :gutter="24">
      <el-table
        v-loading="cargandoUsuarios"
        :data="usuariosPaginados"
        stripe
        style="width: 100%"
      >
        <el-table-column
          label="Nombre y apellido"
        >
          <template slot-scope="scope">
            {{ scope.row.nombre }} {{ scope.row.apellido }}
          </template>
        </el-table-column>
        <el-table-column
          label="Identificación"
          prop="identificacion"
        />
        <el-table-column
          label="Usuario"
          prop="usuario"
        />
        <el-table-column
          label="Correo"
          prop="correo"
        />
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
                @click="editarUsuario(scope.row)"
              />
            </el-tooltip>

            <el-tooltip
              class="item"
              effect="dark"
              content="Cuentas"
              placement="top"
            >
              <el-button
                size="mini"
                type="primary"
                icon="el-icon-s-cooperation"
                @click="cuentasUsuario(scope.row)"
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
      :visible.sync="formularioUsuarioVisible"
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
        ref="formularioUsuario"
        :model="formularioUsuario"
        :rules="reglas"
      >
        <el-row :gutter="24">
          <el-col :span="8">
            <el-form-item
              label="Nombre"
              prop="nombre"
            >
              <el-input
                v-model="formularioUsuario.nombre"
              />
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item
              label="Apelllido"
              prop="apellido"
            >
              <el-input
                v-model="formularioUsuario.apellido"
              />
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item
              label="Identificación"
              prop="identificacion"
            >
              <el-input
                v-model="formularioUsuario.identificacion"
              />
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="24">
          <el-col :span="8">
            <el-form-item
              label="Usuario"
              prop="usuario"
            >
              <el-input
                v-model="formularioUsuario.usuario"
              />
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item
              label="Correo"
              prop="correo"
            >
              <el-input
                v-model="formularioUsuario.correo"
              />
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item
              label="Contraseña"
              prop="password"
            >
              <el-input
                v-model="formularioUsuario.password"
              />
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <span
        slot="footer"
        class="dialog-footer"
      >
        <el-button @click="formularioUsuarioVisible = false">Cerrar</el-button>
        <el-button
          type="primary"
          @click="validaFormularioUsuario"
        >Guardar</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { guardarUsuario, actualizaUsuario, cargaUsuarios } from '@/api/user'

export default {
  name: 'Index',
  data() {
    return {
      search: '',
      pagina: 1,
      tamanioPagina: 5,
      total: 0,

      formularioUsuarioVisible: false,
      usuarios: [],
      cargandoUsuarios: true,
      formularioUsuario: {
        id: null,
        nombre: '',
        apellido: '',
        identificacion: '',
        usuario: '',
        correo: '',
        password: '',
        tipo: 'cliente',
        activo: true
      },
      formularioErrores: []
    }
  },
  computed: {
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
          { required: this.formularioUsuario.id === null, message: 'El campo es obligatorio.', trigger: 'change' }
        ]
      }
    },
    usuariosPaginados() {
      this.total = this.usuarios.length

      return this.usuarios.slice(this.tamanioPagina * this.pagina - this.tamanioPagina, this.tamanioPagina * this.pagina)
    }
  },
  created() {
    this.cargaUsuarios()
  },
  methods: {
    cargaUsuarios() {
      cargaUsuarios().then(respuesta => {
        this.usuarios = respuesta.data
      })

      this.cargandoUsuarios = false
    },
    seleccionaPagina(val) {
      this.pagina = val
    },
    nuevoUsuario() {
      this.limpiarFormulario()
      this.formularioUsuarioVisible = true
      this.$nextTick(() => {
        this.$refs['formularioUsuario'].clearValidate()
      })
    },
    validaFormularioUsuario() {
      this.$refs['formularioUsuario'].validate((valid) => {
        if (valid) {
          if (this.formularioUsuario.id !== null) {
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
      guardarUsuario(this.formularioUsuario).then(respuesta => {
        if (respuesta.success) {
          this.usuarios.push(respuesta.data)
          this.$message.success(respuesta.message)
          this.formularioUsuarioVisible = false
        } else {
          this.formularioErrores = respuesta.errors
        }
      })
    },
    editarUsuario(scopeRow) {
      this.formularioUsuario.id = scopeRow.id;
      this.formularioUsuario.nombre = scopeRow.nombre;
      this.formularioUsuario.apellido = scopeRow.apellido;
      this.formularioUsuario.identificacion = scopeRow.identificacion;
      this.formularioUsuario.usuario = scopeRow.usuario;
      this.formularioUsuario.correo = scopeRow.correo;

      this.formularioUsuarioVisible = true
    },
    actualizaUsuario() {
      actualizaUsuario(this.formularioUsuario).then(respuesta => {
        if (respuesta.success) {
          const index = this.usuariosPaginados.findIndex(usuario => usuario.id === this.formularioUsuario.id);

          Object.assign(this.usuariosPaginados[index], respuesta.data);
          this.$message.success(respuesta.message)
          this.formularioUsuarioVisible = false
        } else {
          this.formularioErrores = respuesta.errors
        }
      })
    },
    limpiarFormulario() {
      this.formularioUsuario.id = null;
      this.formularioUsuario.nombre = '';
      this.formularioUsuario.apellido = '';
      this.formularioUsuario.identificacion = '';
      this.formularioUsuario.usuario = '';
      this.formularioUsuario.correo = '';
      this.formularioUsuario.password = '';
      this.formularioErrores = []
    },
    cuentasUsuario(scopeRow) {
      // Setea los datos del usuario en la store
      this.$store.dispatch('cuenta/usuarioCuenta', scopeRow);

      //Redirecciona a la pantalla de las cuentas
      this.$router.push({
        name: 'cuentas_usuario'
      });
    }
  }
}
</script>

<style lang="scss" scoped>

</style>
