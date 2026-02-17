$(document).ready(function () {
    $('#table-roles').DataTable({
        pageLength: 10,
        // lengthMenu: [5, 10, 25, 50, 100],
        order: [[0, 'desc']],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
        }
    });
});

function saveRol() {
    const nombre = $('#rol_nombre').val().trim();

    if (!nombre) {
        Swal.fire({
            icon: 'warning',
            title: 'Nombre requerido',
            text: 'El nombre es obligatorio.'
        });
        return;
    }

    $.ajax({
        url: `${base_url}admin/roles/store`,
        type: 'POST',
        dataType: 'json',
        data: { nombre }
    }).done(function (res) {
        if (res && res.ok) {
            $('#crearRolForm')[0].reset();
            const modalEl = document.getElementById('crearRolModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();

            Swal.fire({
                icon: 'success',
                title: 'Guardado',
                text: 'Rol guardado.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo guardar.'
            });
        }
    }).fail(function () {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error en la solicitud.'
        });
    });
}

function editRol(id) {
    const modalEl = document.getElementById('editRolModal');
    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    document.getElementById('edit_rol_id').value = id;
    modal.show();

    $.ajax({
        url: `${base_url}admin/roles/show`,
        type: 'GET',
        dataType: 'json',
        data: { id }
    }).done(function (res) {
        if (res && res.ok) {
            const data = res.data;
            $('#edit_rol_nombre').val(data.nombre || '');
        }
    });
}

function updateRol() {
    const id = $('#edit_rol_id').val();
    const nombre = $('#edit_rol_nombre').val().trim();

    if (!nombre) {
        Swal.fire({
            icon: 'warning',
            title: 'Nombre requerido',
            text: 'El nombre es obligatorio.'
        });
        return;
    }

    $.ajax({
        url: `${base_url}admin/roles/update`,
        type: 'POST',
        dataType: 'json',
        data: { id, nombre }
    }).done(function (res) {
        if (res && res.ok) {
            const modalEl = document.getElementById('editRolModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();

            Swal.fire({
                icon: 'success',
                title: 'Actualizado',
                text: 'Rol actualizado.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            const msg = res && res.error === 'duplicate'
                ? 'Ya existe un rol con ese nombre.'
                : 'No se pudo actualizar.';
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: msg
            });
        }
    }).fail(function () {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error en la solicitud.'
        });
    });
}

function deleteRol(id) {
    Swal.fire({
        icon: 'warning',
        title: 'Eliminar',
        text: '¿Seguro que quieres eliminar este rol?',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: `${base_url}admin/roles/delete`,
            type: 'POST',
            dataType: 'json',
            data: { id }
        }).done(function (res) {
            if (res && res.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Eliminado',
                    text: 'Rol eliminado.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo eliminar.'
                });
            }
        }).fail(function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error en la solicitud.'
            });
        });
    });
}

