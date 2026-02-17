$(document).ready(function () {
    $('#permisosTable').DataTable({
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, 'Todos']],
        order: [[0, 'desc']],
        responsive: true,
        autoWidth: false,
        deferRender: true,
        pagingType: 'full_numbers',
        dom:
            "<'row g-2 align-items-center mb-2'<'col-sm-6'l><'col-sm-6'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row g-2 align-items-center mt-2'<'col-sm-5'i><'col-sm-7'p>>",
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json',
            search: '',
            searchPlaceholder: 'Buscar permiso...',
            lengthMenu: '_MENU_ por pagina',
            info: 'Mostrando _START_ a _END_ de _TOTAL_',
            infoEmpty: 'Sin registros',
            zeroRecords: 'No se encontraron resultados',
            paginate: {
                first: 'Primero',
                last: 'Ultimo',
                next: 'Siguiente',
                previous: 'Anterior'
            }
        },
        initComplete: function () {
            const $container = $(this.api().table().container());
            $container.find('input[type="search"]')
                .addClass('form-control form-control-sm')
                .attr('aria-label', 'Buscar');
            $container.find('select')
                .addClass('form-select form-select-sm')
                .attr('aria-label', 'Registros por pagina');
        }
    });
});

function savePermiso() {
    const name = $('[name="name"]').val().trim();
    const description = $('[name="description"]').val().trim();

    if (!name) {
        Swal.fire({
            icon: 'warning',
            title: 'Clave requerida',
            text: 'La clave es obligatoria.'
        });
        return;
    }

    $.ajax({
        url: `${base_url}admin/permisos/store`,
        type: 'POST',
        dataType: 'json',
        data: { name, description }
    }).done(function (res) {
        if (res && res.ok) {
            $('#crearPermisoForm')[0].reset();
            const modalEl = document.getElementById('crearPermisoModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();

            Swal.fire({
                icon: 'success',
                title: 'Guardado',
                text: 'Permiso guardado.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            const msg = res && res.error === 'duplicate'
                ? 'Ya existe un permiso con esa clave.'
                : 'No se pudo guardar.';
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

function editPermiso(id) {
    const modalEl = document.getElementById('editPermisoModal');
    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    document.getElementById('id_edit').value = id;
    modal.show();

    $.ajax({
        url: `${base_url}admin/permisos/show`,
        type: 'GET',
        dataType: 'json',
        data: { id }
    }).done(function (res) {
        if (res && res.ok) {
            const data = res.data;
            $('#edit_name').val(data.name || '');
            $('#edit_description').val(data.description || '');
        }
    });
}

function updatePermiso() {
    const id = $('#id_edit').val();
    const name = $('#edit_name').val().trim();
    const description = $('#edit_description').val().trim();

    if (!name) {
        Swal.fire({
            icon: 'warning',
            title: 'Clave requerida',
            text: 'La clave es obligatoria.'
        });
        return;
    }

    $.ajax({
        url: `${base_url}admin/permisos/update`,
        type: 'POST',
        dataType: 'json',
        data: { id, name, description }
    }).done(function (res) {
        if (res && res.ok) {
            const modalEl = document.getElementById('editPermisoModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();

            Swal.fire({
                icon: 'success',
                title: 'Actualizado',
                text: 'Permiso actualizado.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            const msg = res && res.error === 'duplicate'
                ? 'Ya existe un permiso con esa clave.'
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

function deletePermiso(id) {
    Swal.fire({
        icon: 'warning',
        title: 'Eliminar',
        text: 'Seguro que quieres eliminar este permiso?',
        showCancelButton: true,
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: `${base_url}admin/permisos/delete`,
            type: 'POST',
            dataType: 'json',
            data: { id }
        }).done(function (res) {
            if (res && res.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Eliminado',
                    text: 'Permiso eliminado.',
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
