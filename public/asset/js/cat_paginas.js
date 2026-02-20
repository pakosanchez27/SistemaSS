$(document).ready(function () {
    $.fn.dataTable.ext.errMode = 'none';
    const tabla = $('#paginasTable').DataTable({
        pageLength: 10,
        // lengthMenu: [5, 10, 25, 50, 100],
        order: [[0, 'desc']],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json',
            emptyTable: 'Sin registros',
            zeroRecords: 'Sin registros'
        },
        ajax: {
            url: base_url + 'admin/paginas/list',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'ruta' },
            {
                data: null,
                render: function (row) {
                    if (parseInt(row.es_global, 10) === 1) {
                        return 'Global';
                    }
                    return row.areas ? row.areas : 'Sin areas';
                }
            },
            {
                data: null,
                orderable: false,
                render: function (data) {
                    return '<button class="btn btn-sm btn-outline-primary me-1 btn-editar" data-id="' + data.id + '">Editar</button>' +
                        '<button class="btn btn-sm btn-outline-danger" disabled>Eliminar</button>';
                }
            }
        ]
    });

    function resetPaginaForm() {
        const form = $('#paginaForm')[0];
        form.reset();
        $('#paginaId').val('');
        $('#paginaSubmitBtn').text('Guardar');
        $('#crearPaginaModalLabel').text('Nueva Pagina');
        $('#iconoPreview').html('<i class="fa-regular fa-circle"></i><span class="small">Sin icono</span>');
        $('#iconoSeleccionado').val('');
        $('#iconCatalogo input[name="icono_catalogo"]:checked').prop('checked', false);
        $('#paginaGlobal').prop('checked', false).trigger('change');
        $('#paginaAreas input[type="checkbox"]').prop('checked', false);
        $('#paginaOrden').val('0');
    }

    $('#crearPaginaModal').on('show.bs.modal', function () {
        if (!$('#paginaId').val()) {
            resetPaginaForm();
        }
    });

    $('#paginasTable').on('click', '.btn-editar', function () {
        const id = $(this).data('id');
        $.get(base_url + 'admin/paginas/show', { id: id }, function (res) {
            if (!res || !res.ok) {
                alert(res && res.error ? res.error : 'No se pudo cargar la pagina.');
                return;
            }

            const p = res.pagina;
            resetPaginaForm();
            $('#paginaId').val(p.id);
            $('#paginaNombre').val(p.nombre);
            $('#paginaSlug').val(p.slug);
            $('#paginaRuta').val(p.ruta || '');
            $('#paginaEstado').val(String(p.estado));

            if (p.icono) {
                $('#iconoSeleccionado').val(p.icono);
                $('#iconoPreview').html('<i class="' + p.icono + '"></i><span class="small">Seleccionado</span>');
                $('#iconCatalogo input[name="icono_catalogo"][value="' + p.icono + '"]').prop('checked', true);
            }

            const esGlobal = parseInt(p.es_global, 10) === 1;
            $('#paginaGlobal').prop('checked', esGlobal).trigger('change');

            if (!esGlobal && Array.isArray(res.areas)) {
                res.areas.forEach(function (item) {
                    $('#paginaAreas input[type="checkbox"][value="' + item.area_id + '"]').prop('checked', true);
                });
                if (res.areas[0] && res.areas[0].orden !== undefined) {
                    $('#paginaOrden').val(res.areas[0].orden);
                }
            }

            $('#paginaSubmitBtn').text('Actualizar');
            $('#crearPaginaModalLabel').text('Editar Pagina');
            $('#crearPaginaModal').modal('show');
        }, 'json');
    });

    $('#paginaForm').on('submit', function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const isGlobal = $('#paginaGlobal').is(':checked');
        const isEdit = $('#paginaId').val() !== '';

        if (isGlobal) {
            formData.set('es_global', '1');
            formData.delete('areas[]');
        } else {
            formData.set('es_global', '0');
        }

        $.ajax({
            url: base_url + (isEdit ? 'admin/paginas/update' : 'admin/paginas/store'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json'
        })
            .done(function (res) {
                if (res && res.ok) {
                    resetPaginaForm();
                    $('#crearPaginaModal').modal('hide');
                    tabla.ajax.reload(null, false);
                } else {
                    const msg = res && res.error ? res.error : 'No se pudo guardar la pagina.';
                    alert(msg);
                }
            })
            .fail(function () {
                alert('Error al guardar la pagina.');
            });
    });
});
