<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="card p-3 rounded-lg">
    <h3>Mi Perfil</h3>
    <hr>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('admin/perfil/update') ?>">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" name="nombre" value="<?= esc($usuario['nombre'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Apellido Paterno</label>
                <input type="text" class="form-control" name="ap_paterno" value="<?= esc($usuario['ap_paterno'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Apellido Materno</label>
                <input type="text" class="form-control" name="ap_materno" value="<?= esc($usuario['ap_materno'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Cargo</label>
                <input type="text" class="form-control" name="cargo" value="<?= esc($usuario['cargo'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Telefono</label>
                <input type="text" class="form-control" name="telefono" value="<?= esc($usuario['telefono'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo</label>
                <input type="email" class="form-control" name="correo" value="<?= esc($usuario['correo'] ?? '') ?>" required>
            </div>
        </div>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
