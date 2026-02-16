<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<h3>Roles</h3>
<a href="<?= base_url('admin/roles/create') ?>" class="btn btn-primary mb-3">Crear Rol</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($roles as $rol): ?>
        <tr>
            <td><?= $rol['id'] ?></td>
            <td><?= $rol['nombre'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
