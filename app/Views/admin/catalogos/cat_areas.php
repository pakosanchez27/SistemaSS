<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="card p-3 rounded-lg">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Areas</h3>
        <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#crearAreaModal">
            Crear Area
        </a>
    </div>

    <table id="areasTable" class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th class="text-center">Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($areas)): ?>
                <?php foreach ($areas as $item): ?>
                <tr>
                    <td><?= $item['id'] ?></td>
                    <td><?= esc($item['nombre']) ?></td>
                    <td class="text-center">
                        <?php if ((int) $item['activo'] === 1): ?>
                            <span class="badge bg-success">Activo</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-warning" title="Editar Area" onclick="editArea(<?= $item['id'] ?>)">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" title="Eliminar Area" onclick="deleteArea(<?= $item['id'] ?>)">
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

<!-- Modal Crear Área -->
<div class="modal fade" id="crearAreaModal" tabindex="-1" aria-labelledby="crearAreaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearAreaModalLabel">Agregar Area</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="crearAreaForm">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="activo" id="activo">
                            <option value="1" selected>Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="saveArea()">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Área -->
<div class="modal fade" id="editAreaModal" tabindex="-1" aria-labelledby="editAreaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAreaModalLabel">Editar Area</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="editAreaForm">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="edit_nombre" id="edit_nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="edit_activo" id="edit_activo">
                            <option value="1" selected>Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <input type="hidden" id="id_edit">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="updateArea()">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const base_url = "<?= base_url() ?>";
</script>
<script src="<?= base_url('asset/js/cat_areas.js') ?>"></script>

<?= $this->endSection() ?>
