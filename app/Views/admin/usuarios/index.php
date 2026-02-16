<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<h3>Crear Usuario</h3>
<hr>
<div class="container mt-4">

    <div class="row mb-3">
        <div class="col-12">
            <div class="border border-primary border-2 rounded-3">
                
                <form method="post" action="<?= base_url('admin/usuarios/store') ?>">
                    <!-- Datos personales -->
                    <div class="row mb-3">
                        <div class="col-12 col-md-6 col-lg-4">
                            <label>Nombre</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-4">
                            <label>Apellido Paterno</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user-tag"></i></span>
                                <input type="text" name="ap_paterno" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-4">
                            <label>Apellido Materno</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user-tag"></i></span>
                                <input type="text" name="ap_materno" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Area y Rol -->
                    <div class="row mb-3">
                        <div class="col-12 col-md-6">
                            <label>Área</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-building"></i></span>
                                <select name="area" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($areas as $area): ?>
                                    <option value="<?= esc($area['id']) ?>">
                                        <?= esc($area['nombre']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label>Rol</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-users-gear"></i></span>
                                    <select name="rol_id" class="form-control" required>
                                        <option value="">Seleccionar...</option>
                                        <?php foreach($roles as $rol): ?>
                                        <option value="<?= $rol['id'] ?>"><?= $rol['nombre'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Teleono y cargo -->
                    <div class="row mb-3">
                        <div class="col-12 col-md-8">
                            <label>Cargo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-sitemap"></i></span>
                                <input type="text" name="cargo" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label>Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                <input type="text" name="telefono" class="form-control" maxlength="10"
                                    inputmode="numeric" pattern="[0-9]{10}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="10 dígitos"
                                    required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Acceso -->
                    <div class="row mb-3">
                        <div class="col-12 col-md-6">
                            <label>Correo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" name="correo" class="form-control" placeholder="name@neza.gob.mx"
                                    required>
                            </div>
                            <div class="form-text text-primary">
                                <i class="fa-solid fa-circle-info"></i>
                                De preferencia Institucional @neza.gob.mx
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label>contrasena</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="anos_laborando" class="form-control" min="0" required>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-success">Guardar</button>
                </form>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <div class="border border-dark border-2 rounded-3">
                <table class="table table-striped">
                    <thead class="text-uppercase small tableHeader">
                        <tr>
                            <th>Empleado</th>
                            <th>Cargo</th>
                            <th>Área</th>
                            <th class="text-center">Edad</th>
                            <th class="text-center">Años</th>
                            <th>Estudios</th>
                            <th>Profesión</th>
                            <th class="text-center">Sexo</th>
                            <th class="text-center">Detalle</th>
                        </tr>
                    </thead>
                    <tbdy>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>