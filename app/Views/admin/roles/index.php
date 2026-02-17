<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="card p-3 rounded-lg">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Roles</h3>
        <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#crearRolModal">
            Crear Rol
        </a>
    </div>
    
    <table id="table-roles" class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($roles as $rol): ?>
                <tr>
                    <td><?= $rol['id'] ?></td>
                    <td><?= $rol['nombre'] ?></td>
                    <td>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-warning" title="Editar Rol" onclick="editRol(<?= $rol['id'] ?>)">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" title="Eliminar Rol" onclick="deleteRol(<?= $rol['id'] ?>)">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Modal Crear Rol -->
    <div class="modal fade" id="crearRolModal" tabindex="-1" aria-labelledby="crearRolModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="crearRolModalLabel">Agregar Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="crearRolForm">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="rol_nombre" required>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="saveRol()">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Editar Rol -->
    <div class="modal fade" id="editRolModal" tabindex="-1" aria-labelledby="editRolModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRolModalLabel">Editar Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="editRolForm">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="edit_nombre" id="edit_rol_nombre" required>
                        </div>
                        <input type="hidden" id="edit_rol_id">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="updateRol()">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const base_url = "<?= base_url() ?>";
    </script>
    <script src="<?= base_url('asset/js/cat_roles.js') ?>"></script>

    <?= $this->endSection() ?>
