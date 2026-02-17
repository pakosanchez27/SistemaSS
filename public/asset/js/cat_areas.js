
$(document).ready(function () {
    $('#areasTable').DataTable({
        pageLength: 10,
        // lengthMenu: [5, 10, 25, 50, 100],
        order: [[0, 'desc']],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
        }
    });
});
function saveArea() {
    const nombre = $('[name="nombre"]').val().trim();
    const activo = $('[name="activo"]').val();

    if (!nombre) {
        Swal.fire({
            icon: 'warning',
            title: 'Nombre requerido',
            text: 'El nombre es obligatorio.'
        });
        return;
    }

    $.ajax({
        url: `${base_url}admin/areas/store`,
        type: 'POST',
        dataType: 'json',
        data: { nombre, activo }
    }).done(function (res) {
        if (res && res.ok) {
            $('#crearAreaForm')[0].reset();
            const modalEl = document.getElementById('crearAreaModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();

            Swal.fire({
                icon: 'success',
                title: 'Guardado',
                text: 'Area guardada.',
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

function editArea(id) {
    const modalEl = document.getElementById('editAreaModal');
    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    document.getElementById('id_edit').value = id;
    modal.show();

    $.ajax({
        url: `${base_url}admin/areas/show`,
        type: 'GET',
        dataType: 'json',
        data: { id }
    }).done(function (res) {
        if (res && res.ok) {
            const data = res.data;
            $('#edit_nombre').val(data.nombre || '');
            $('#edit_activo').val(String(data.activo ?? '1'));
        }
    });
}

function updateArea() {
    const id = $('#id_edit').val();
    const nombre = $('#edit_nombre').val().trim();
    const activo = $('#edit_activo').val();

    if (!nombre) {
        Swal.fire({
            icon: 'warning',
            title: 'Nombre requerido',
            text: 'El nombre es obligatorio.'
        });
        return;
    }

    $.ajax({
        url: `${base_url}admin/areas/update`,
        type: 'POST',
        dataType: 'json',
        data: { id, nombre, activo }
    }).done(function (res) {
        if (res && res.ok) {
            const modalEl = document.getElementById('editAreaModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();

            Swal.fire({
                icon: 'success',
                title: 'Actualizado',
                text: 'Area actualizada.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo actualizar.'
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

function deleteArea(id) {
    Swal.fire({
        icon: 'warning',
        title: 'Eliminar',
        text: 'Seguro que quieres eliminar esta area?',
        showCancelButton: true,
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: `${base_url}admin/areas/delete`,
            type: 'POST',
            dataType: 'json',
            data: { id }
        }).done(function (res) {
            if (res && res.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Eliminado',
                    text: 'Area eliminada.',
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
