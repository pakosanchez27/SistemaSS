<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
 
<div class="card p-3 rounded-lg">
<div class="d-flex justify-content-between align-items-center">
    <h3>Software</h3>
<a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#crearSoftwareModal">
    Crear Software
</a>
</div>

<table id="softwareTable" class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($software as $item): ?>
        <tr>
            <td><?= $item['id'] ?></td>
            <td><?= esc($item['nombre']) ?></td>
            <td><?= $item['estado'] ? 'Activo' : 'Inactivo' ?></td>
            <td>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-warning" title="Editar Software" onclick="editSoftware(<?php echo $item['id'] ?>)">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" title="Eliminar Software" onclick="deleteSoftware(<?= $item['id'] ?>)">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<!-- Modal Crear Software -->
<div class="modal fade" id="crearSoftwareModal" tabindex="-1" aria-labelledby="crearSoftwareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearSoftwareModalLabel">Agregar Software</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="crearSoftwareForm">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="estado" id="estado" >
                            <option value="1" selected>Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="saveSoftware()">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editSoftwareModal" tabindex="-1" aria-labelledby="editSoftwareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSoftwareModalLabel">Editar Software</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="editSoftwareForm">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="edit_nombre" id="edit_nombre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-select" name="edit_estado" id="edit_estado">
                            <option value="1" selected>Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <input type="hidden" id="id_edit">
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="update()">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const base_url = "<?= base_url() ?>";
    
</script>
<script src="<?= base_url('asset/js/cat_software.js') ?>"></script>

<?= $this->endSection() ?>
