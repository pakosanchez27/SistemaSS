<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="card p-3 rounded-lg">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Usuarios</h3>
        <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#crearUsuarioModal" title="Crear Usuario">
            Crear Usuario
        </a>
    </div>

    <table id="usuariosTable" class="table table-bordered">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Cargo</th>
                <th>Area</th>
                <th>Rol</th>
                <th>Telefono</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= esc(trim($u['nombre'] . ' ' . $u['ap_paterno'] . ' ' . $u['ap_materno'])) ?></td>
                        <td><?= esc($u['correo'] ?? '') ?></td>
                        <td><?= esc($u['cargo'] ?? '') ?></td>
                        <td><?= esc($u['area'] ?? '') ?></td>
                        <td><?= esc($u['rol'] ?? '') ?></td>
                        <td><?= esc($u['telefono'] ?? '') ?></td>
                        <td class="text-center">
                            <?php if ((int) ($u['estado'] ?? 0) === 1): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <button type="button" class="btn btn-sm btn-secondary" title="Restablecer contraseña"
                                    onclick="resetPassword(<?= (int) $u['id'] ?>)">
                                    <i class="fa-solid fa-key"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-info text-white" title="Permisos"
                                    onclick="managePermisos(<?= (int) $u['id'] ?>)">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" title="Editar"
                                    onclick="editUsuario(<?= (int) $u['id'] ?>)">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" title="Eliminar"
                                    onclick="deleteUsuario(<?= (int) $u['id'] ?>)">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center text-muted">Sin registros</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Crear Usuario -->
<div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-labelledby="crearUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearUsuarioModalLabel">Crear Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="crearUsuarioForm" method="post" action="<?= base_url('admin/usuarios/store') ?>">
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
                                        <?php foreach ($roles as $rol): ?>
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
                            <label>Telefono</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                <input type="text" name="telefono" class="form-control" maxlength="10"
                                    inputmode="numeric" pattern="[0-9]{10}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="10 dÃ­gitos"
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
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <!-- Permisos -->
                    <div class="mb-3">
                        <div class="accordion" id="permisosAccordion">
                            <?php
                            $permGroups = [
                                'Dashboard' => ['ver-dashboard'],
                                'Usuarios' => ['usuarios-ver', 'usuarios-crear', 'usuarios-editar', 'usuarios-eliminar'],
                                'Roles y permisos' => ['roles-ver', 'roles-crear', 'roles-editar', 'roles-asignar-permisos'],
                                'Areas' => ['areas-ver', 'areas-crear', 'areas-editar'],
                                'Registros' => ['registros-ver', 'registros-crear', 'registros-editar', 'registros-eliminar'],
                                'Evidencias' => ['evidencias-subir', 'evidencias-ver', 'evidencias-eliminar'],
                                'Reportes' => ['reportes-ver', 'exportar-excel', 'generar-pdf'],
                                'Estadisticas' => ['ver-estadisticas'],
                                'Administracion avanzada' => ['configuracion-sistema', 'admin-modulos'],
                            ];

                            $permisosByName = [];
                            if (!empty($permisos)) {
                                foreach ($permisos as $p) {
                                    $permisosByName[$p['name']] = $p;
                                }
                            }
                            ?>

                            <?php $i = 0; ?>
                            <?php foreach ($permGroups as $groupName => $permNames): ?>
                                <?php $i++; $headingId = 'permHeading' . $i; $collapseId = 'permCollapse' . $i; ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="<?= $headingId ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#<?= $collapseId ?>" aria-expanded="false" aria-controls="<?= $collapseId ?>">
                                            <?= esc($groupName) ?>
                                        </button>
                                    </h2>
                                    <div id="<?= $collapseId ?>" class="accordion-collapse collapse" aria-labelledby="<?= $headingId ?>"
                                        data-bs-parent="#permisosAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <?php foreach ($permNames as $permName): ?>
                                                    <?php if (!isset($permisosByName[$permName])) continue; ?>
                                                    <?php $perm = $permisosByName[$permName]; ?>
                                                    <div class="col-12 col-md-6 col-lg-4 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="permisos[]"
                                                                id="perm<?= $perm['id'] ?>" value="<?= $perm['id'] ?>">
                                                            <label class="form-check-label" for="perm<?= $perm['id'] ?>">
                                                                <?= esc($perm['description'] ?: $perm['name']) ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Restablecer Contrasena -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel">Restablecer Contrasena</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="resetPasswordForm">
                    <input type="hidden" id="reset_user_id" name="user_id">
                    <div class="mb-3">
                        <label>Nueva contrasena</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editUsuarioModal" tabindex="-1" aria-labelledby="editUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUsuarioModalLabel">Editar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="editUsuarioForm">
                    <input type="hidden" id="edit_user_id" name="id">

                    <div class="row mb-3">
                        <div class="col-12 col-md-6 col-lg-4">
                            <label>Nombre</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4">
                            <label>Apellido Paterno</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user-tag"></i></span>
                                <input type="text" name="ap_paterno" id="edit_ap_paterno" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 col-lg-4">
                            <label>Apellido Materno</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user-tag"></i></span>
                                <input type="text" name="ap_materno" id="edit_ap_materno" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 col-md-6">
                            <label>Area</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-building"></i></span>
                                <select name="area" id="edit_area" class="form-select" required>
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
                            <label>Rol</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-users-gear"></i></span>
                                <select name="rol_id" id="edit_rol_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($roles as $rol): ?>
                                        <option value="<?= $rol['id'] ?>"><?= $rol['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 col-md-8">
                            <label>Cargo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-sitemap"></i></span>
                                <input type="text" name="cargo" id="edit_cargo" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <label>Telefono</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                <input type="text" name="telefono" id="edit_telefono" class="form-control" maxlength="10"
                                    inputmode="numeric" pattern="[0-9]{10}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="10 digitos" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 col-md-6">
                            <label>Correo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" name="correo" id="edit_correo" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label>Estado</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-toggle-on"></i></span>
                                <select name="estado" id="edit_estado" class="form-select" required>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Permisos por Usuario -->
<div class="modal fade" id="userPermisosModal" tabindex="-1" aria-labelledby="userPermisosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userPermisosModalLabel">Permisos del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="userPermisosForm">
                    <input type="hidden" id="permisos_user_id" name="user_id">
                    <div id="permisosList" class="row"></div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const base_url = "<?= base_url() ?>";
</script>
<script src="<?= base_url('asset/js/usuarios.js') ?>"></script>

<?= $this->endSection() ?>
