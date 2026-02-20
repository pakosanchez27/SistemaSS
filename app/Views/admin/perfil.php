<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="card p-3 rounded-lg">
    <h3>Mi Perfil</h3>
    <hr>

    <div id="perfilAlert" class="alert d-none" role="alert"></div>

    <form id="perfilForm" method="post" action="<?= base_url('admin/perfil/update') ?>">
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
        <div class="mt-3 d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#cambiarPasswordModal">
                Cambiar contrasena
            </button>
        </div>
    </form>
</div>

<div class="modal fade" id="cambiarPasswordModal" tabindex="-1" aria-labelledby="cambiarPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cambiarPasswordModalLabel">Cambiar contrasena</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="passwordAlert" class="alert d-none" role="alert"></div>
                <form id="passwordForm" method="post" action="<?= base_url('admin/perfil/password') ?>">
                    <div class="mb-3">
                        <label class="form-label">Contrasena actual</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nueva contrasena</label>
                        <input type="password" class="form-control" name="password" required>
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

<script>
    (function () {
        function showAlert(type, message, target) {
            const $alert = $(target);
            $alert.removeClass('d-none alert-success alert-danger')
                .addClass(type === 'success' ? 'alert-success' : 'alert-danger')
                .text(message);
        }

        $('#perfilForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: this.action,
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize()
            }).done(function (res) {
                if (res && res.ok) {
                    showAlert('success', 'Perfil actualizado.', '#perfilAlert');
                } else {
                    showAlert('error', res && res.error ? res.error : 'No se pudo actualizar el perfil.', '#perfilAlert');
                }
            }).fail(function () {
                showAlert('error', 'Error en la solicitud.', '#perfilAlert');
            });
        });

        $('#passwordForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: this.action,
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize()
            }).done(function (res) {
                if (res && res.ok) {
                    $('#passwordForm')[0].reset();
                    const modalEl = document.getElementById('cambiarPasswordModal');
                    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modal.hide();
                    showAlert('success', 'Contrasena actualizada.', '#perfilAlert');
                } else {
                    showAlert('error', res && res.error ? res.error : 'No se pudo actualizar la contrasena.', '#passwordAlert');
                }
            }).fail(function () {
                showAlert('error', 'Error en la solicitud.', '#passwordAlert');
            });
        });
    })();
</script>

<?= $this->endSection() ?>
