<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<h3>Asignar Permisos a Roles</h3>

<form method="post" action="<?= base_url('admin/roles/permisos/asignar') ?>">
    
    <div class="mb-3">
        <label>Seleccionar Rol</label>
        <select name="rol_id" class="form-control" required>
            <?php foreach($roles as $rol): ?>
                <option value="<?= $rol['id'] ?>"><?= $rol['nombre'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <label>Permisos:</label>
    <?php foreach($permisos as $permiso): ?>
        <div class="form-check">
            <input type="checkbox" name="permisos[]" value="<?= $permiso['id'] ?>">
            <label><?= $permiso['name'] ?></label>
        </div>
    <?php endforeach; ?>

    <button class="btn btn-success mt-3">Guardar Permisos</button>
</form>

<?= $this->endSection() ?>
