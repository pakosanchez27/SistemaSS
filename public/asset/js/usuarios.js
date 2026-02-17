$(document).ready(function () {
    $('#usuariosTable').DataTable({
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, 'Todos']],
        order: [[0, 'asc']],
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
            searchPlaceholder: 'Buscar usuario...',
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

    $('#crearUsuarioForm').on('submit', function (e) {
        e.preventDefault();
        saveUsuario();
    });

    $('#resetPasswordForm').on('submit', function (e) {
        e.preventDefault();
        submitResetPassword();
    });

    $('#userPermisosForm').on('submit', function (e) {
        e.preventDefault();
        submitUserPermisos();
    });

    $('#editUsuarioForm').on('submit', function (e) {
        e.preventDefault();
        submitEditUsuario();
    });
});

function saveUsuario() {
    const $form = $('#crearUsuarioForm');
    const data = $form.serialize();

    $.ajax({
        url: `${base_url}admin/usuarios/store`,
        type: 'POST',
        dataType: 'json',
        data: data
    }).done(function (res) {
        if (res && res.ok) {
            $form[0].reset();
            const modalEl = document.getElementById('crearUsuarioModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();

            Swal.fire({
                icon: 'success',
                title: 'Guardado',
                text: 'Usuario creado.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            const msg = res && res.error === 'duplicate'
                ? 'El correo ya existe.'
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

function resetPassword(id) {
    $('#reset_user_id').val(id);
    $('#resetPasswordForm')[0].reset();
    const modalEl = document.getElementById('resetPasswordModal');
    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modal.show();
}

function submitResetPassword() {
    const userId = $('#reset_user_id').val();
    const password = $('#resetPasswordForm [name="password"]').val();

    if (!password) {
        Swal.fire({
            icon: 'warning',
            title: 'Contrasena requerida',
            text: 'Ingresa una nueva contrasena.'
        });
        return;
    }

    $.ajax({
        url: `${base_url}admin/usuarios/reset-password`,
        type: 'POST',
        dataType: 'json',
        data: { user_id: userId, password }
    }).done(function (res) {
        if (res && res.ok) {
            const modalEl = document.getElementById('resetPasswordModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();

            Swal.fire({
                icon: 'success',
                title: 'Actualizada',
                text: 'Contrasena restablecida.',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo restablecer.'
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

function managePermisos(id) {
    $('#permisos_user_id').val(id);
    $('#permisosList').html('<div class="text-muted">Cargando permisos...</div>');
    const modalEl = document.getElementById('userPermisosModal');
    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modal.show();

    $.ajax({
        url: `${base_url}admin/usuarios/permisos`,
        type: 'GET',
        dataType: 'json',
        data: { user_id: id }
    }).done(function (res) {
        if (!res || !res.ok) {
            $('#permisosList').html('<div class="text-danger">No se pudieron cargar los permisos.</div>');
            return;
        }

        const permisos = res.data || [];
        if (!permisos.length) {
            $('#permisosList').html('<div class="text-muted">Sin permisos disponibles.</div>');
            return;
        }

        const groups = [
            { name: 'Dashboard', keys: ['ver-dashboard'] },
            { name: 'Usuarios', keys: ['usuarios-ver', 'usuarios-crear', 'usuarios-editar', 'usuarios-eliminar'] },
            { name: 'Roles y permisos', keys: ['roles-ver', 'roles-crear', 'roles-editar', 'roles-asignar-permisos'] },
            { name: 'Areas', keys: ['areas-ver', 'areas-crear', 'areas-editar'] },
            { name: 'Registros', keys: ['registros-ver', 'registros-crear', 'registros-editar', 'registros-eliminar'] },
            { name: 'Evidencias', keys: ['evidencias-subir', 'evidencias-ver', 'evidencias-eliminar'] },
            { name: 'Reportes', keys: ['reportes-ver', 'exportar-excel', 'generar-pdf'] },
            { name: 'Estadisticas', keys: ['ver-estadisticas'] },
            { name: 'Administracion avanzada', keys: ['configuracion-sistema', 'admin-modulos'] }
        ];

        const byName = {};
        permisos.forEach(p => { byName[p.name] = p; });

        const renderPerm = (p) => {
            const checked = String(p.assigned) === '1' ? 'checked' : '';
            const label = p.description || p.name;
            return `
                <div class="col-12 col-md-6 col-lg-4 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permisos[]" id="perm_${p.id}" value="${p.id}" ${checked}>
                        <label class="form-check-label" for="perm_${p.id}">${label}</label>
                    </div>
                </div>
            `;
        };

        let html = '<div class="accordion" id="permisosAccordionUser">';
        let idx = 0;

        groups.forEach(g => {
            const items = g.keys.map(k => byName[k]).filter(Boolean);
            if (!items.length) return;
            idx += 1;
            const headingId = `permUserHeading${idx}`;
            const collapseId = `permUserCollapse${idx}`;
            html += `
                <div class="accordion-item">
                    <h2 class="accordion-header" id="${headingId}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">
                            ${g.name}
                        </button>
                    </h2>
                    <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headingId}"
                        data-bs-parent="#permisosAccordionUser">
                        <div class="accordion-body">
                            <div class="row">
                                ${items.map(renderPerm).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        const groupedNames = new Set(groups.flatMap(g => g.keys));
        const otherItems = permisos.filter(p => !groupedNames.has(p.name));
        if (otherItems.length) {
            idx += 1;
            const headingId = `permUserHeading${idx}`;
            const collapseId = `permUserCollapse${idx}`;
            html += `
                <div class="accordion-item">
                    <h2 class="accordion-header" id="${headingId}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">
                            Otros
                        </button>
                    </h2>
                    <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headingId}"
                        data-bs-parent="#permisosAccordionUser">
                        <div class="accordion-body">
                            <div class="row">
                                ${otherItems.map(renderPerm).join('')}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        html += '</div>';
        $('#permisosList').html(html);
    }).fail(function () {
        $('#permisosList').html('<div class="text-danger">Error en la solicitud.</div>');
    });
}

function submitUserPermisos() {
    const userId = $('#permisos_user_id').val();
    const permisos = $('#userPermisosForm').serialize();

    $.ajax({
        url: `${base_url}admin/usuarios/permisos`,
        type: 'POST',
        dataType: 'json',
        data: permisos
    }).done(function (res) {
        if (res && res.ok) {
            const modalEl = document.getElementById('userPermisosModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();

            Swal.fire({
                icon: 'success',
                title: 'Actualizado',
                text: 'Permisos actualizados.',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron actualizar los permisos.'
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

function editUsuario(id) {
    $('#edit_user_id').val(id);
    const modalEl = document.getElementById('editUsuarioModal');
    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modal.show();

    $.ajax({
        url: `${base_url}admin/usuarios/show`,
        type: 'GET',
        dataType: 'json',
        data: { id }
    }).done(function (res) {
        if (res && res.ok) {
            const data = res.data;
            $('#edit_nombre').val(data.nombre || '');
            $('#edit_ap_paterno').val(data.ap_paterno || '');
            $('#edit_ap_materno').val(data.ap_materno || '');
            $('#edit_cargo').val(data.cargo || '');
            $('#edit_telefono').val(data.telefono || '');
            $('#edit_area').val(String(data.area_id || ''));
            $('#edit_rol_id').val(String(data.rol_id || ''));
            $('#edit_correo').val(data.correo || '');
            $('#edit_estado').val(String(data.estado ?? '1'));
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar el usuario.'
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

function deleteUsuario(id) {
    Swal.fire({
        icon: 'warning',
        title: 'Eliminar',
        text: 'Seguro que quieres eliminar este usuario?',
        showCancelButton: true,
        confirmButtonText: 'Si, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (!result.isConfirmed) return;

        $.ajax({
            url: `${base_url}admin/usuarios/delete`,
            type: 'POST',
            dataType: 'json',
            data: { id }
        }).done(function (res) {
            if (res && res.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Eliminado',
                    text: 'Usuario eliminado.',
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

function submitEditUsuario() {
    const data = $('#editUsuarioForm').serialize();

    $.ajax({
        url: `${base_url}admin/usuarios/update`,
        type: 'POST',
        dataType: 'json',
        data: data
    }).done(function (res) {
        if (res && res.ok) {
            const modalEl = document.getElementById('editUsuarioModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();

            Swal.fire({
                icon: 'success',
                title: 'Actualizado',
                text: 'Usuario actualizado.',
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
