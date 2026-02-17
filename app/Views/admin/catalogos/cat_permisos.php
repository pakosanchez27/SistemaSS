<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="card p-3 rounded-lg">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Permisos</h3>
        <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#crearPermisoModal">
            Crear Permiso
        </a>
    </div>

    <table id="permisosTable" class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Clave</th>
                <th>Descripcion</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($permisos)): ?>
                <?php foreach ($permisos as $item): ?>
                <tr>
                    <td><?= $item['id'] ?></td>
                    <td><?= esc($item['name']) ?></td>
                    <td><?= esc($item['description']) ?></td>
                    <td>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-warning" title="Editar Permiso" onclick="editPermiso(<?= $item['id'] ?>)">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" title="Eliminar Permiso" onclick="deletePermiso(<?= $item['id'] ?>)">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Crear Permiso -->
<div class="modal fade" id="crearPermisoModal" tabindex="-1" aria-labelledby="crearPermisoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearPermisoModalLabel">Agregar Permiso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="crearPermisoForm">
                    <div class="mb-3">
                        <label class="form-label">Clave</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripcion</label>
                        <textarea class="form-control" name="description" id="description" rows="2"></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="savePermiso()">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Permiso -->
<div class="modal fade" id="editPermisoModal" tabindex="-1" aria-labelledby="editPermisoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPermisoModalLabel">Editar Permiso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="editPermisoForm">
                    <div class="mb-3">
                        <label class="form-label">Clave</label>
                        <input type="text" class="form-control" name="edit_name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripcion</label>
                        <textarea class="form-control" name="edit_description" id="edit_description" rows="2"></textarea>
                    </div>
                    <input type="hidden" id="id_edit">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="updatePermiso()">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const base_url = "<?= base_url() ?>";
</script>
<script src="<?= base_url('asset/js/cat_permisos.js') ?>"></script>

<?= $this->endSection() ?>
