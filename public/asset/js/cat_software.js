$(document).ready(function () {
    $('#softwareTable').DataTable({
        pageLength: 10,
        // lengthMenu: [5, 10, 25, 50, 100],
        order: [[0, 'desc']],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
        }
    });
});


function saveSoftware() {
    const nombre = $('[name="nombre"]').val().trim();
    const estado = $('[name="estado"]').val();

    if (!nombre) {
        Swal.fire({
            icon: 'warning',
            title: 'Nombre requerido',
            text: 'El nombre es obligatorio.'
        });
        return;
    }

    $.ajax({
        url: `${base_url}admin/software/store`,
        type: 'POST',
        dataType: 'json',
        data: { nombre, estado }
    }).done(function (res) {
        if (res && res.ok) {

            $('#crearSoftwareForm')[0].reset();
            const modalEl = document.getElementById('crearSoftwareModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();

            Swal.fire({
                icon: 'success',
                title: 'Guardado',
                text: 'Software guardado.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            const msg = res && res.error === 'duplicate'
                ? 'Ya existe un software con ese nombre.'
                : 'Ya existe un registro igual.';
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

function editSoftware(id) {
    const modalEl = document.getElementById('editSoftwareModal');
    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    document.getElementById('id_edit').value = id;
    modal.show();

    $.ajax({
        url: `${base_url}admin/software/show`,
        type: 'GET',
        dataType: 'json',
        data: { id }
    }).done(function (res) {
        console.log(res);

        if (res && res.ok) {
            const data = res.data;
            // Rellena los campos del formulario
            $('#edit_nombre').val(data.nombre || '');
            $('#edit_estado').val(String(data.estado ?? '1'));
        }
    });

}

function update() {
    const id = $('#id_edit').val();
    const nombre = $('#edit_nombre').val().trim();
    const estado = $('#edit_estado').val();

    if (!nombre) {
        Swal.fire({
            icon: 'warning',
            title: 'Nombre requerido',
            text: 'El nombre es obligatorio.'
        });
        return;
    }

    $.ajax({
        url: `${base_url}admin/software/update`,
        type: 'POST',
        dataType: 'json',
        data: { id, nombre, estado }
    }).done(function (res) {
        if (res && res.ok) {
            const modalEl = document.getElementById('editSoftwareModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();

            Swal.fire({
                icon: 'success',
                title: 'Actualizado',
                text: 'Software actualizado.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            const msg = res && res.error === 'duplicate'
                ? 'Ya existe un software con ese nombre.'
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

function deleteSoftware(id) {
    Swal.fire({
        icon: 'warning',
        title: 'Eliminar',
        text: '¿Seguro que quieres eliminar este software?',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: `${base_url}admin/software/delete`,
            type: 'POST',
            dataType: 'json',
            data: { id }
        }).done(function (res) {
            if (res && res.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Eliminado',
                    text: 'Software eliminado.',
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
