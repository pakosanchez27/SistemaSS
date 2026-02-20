<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $areas = $areas ?? []; ?>

<div class="card p-3 rounded-lg">
    <div class="d-flex justify-content-between align-items-center">
        <h3>Paginas</h3>
        <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#crearPaginaModal">
            Crear Pagina
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered" id="paginasTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>URL</th>
                    <th>Alcance</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui se cargaran las paginas dinamicamente -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para crear pagina -->
<div class="modal fade" id="crearPaginaModal" tabindex="-1" aria-labelledby="crearPaginaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="paginaForm" autocomplete="off">
                <input type="hidden" id="paginaId" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="crearPaginaModalLabel">Nueva Pagina</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="paginaNombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="paginaNombre" name="nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="paginaSlug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="paginaSlug" name="slug" placeholder="mi-pagina" required>
                        </div>
                        <div class="col-md-6">
                            <label for="paginaRuta" class="form-label">Ruta</label>
                            <input type="text" class="form-control" id="paginaRuta" name="ruta" placeholder="/admin/mi-ruta" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label d-block">Alcance</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="paginaGlobal" name="es_global" value="1">
                                <label class="form-check-label" for="paginaGlobal">Pagina global (visible en todas las areas)</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label d-block">Icono (catalogo Font Awesome 6)</label>
                            <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                                <input type="text" class="form-control" id="iconoSeleccionado" name="icono" placeholder="Selecciona un icono" readonly required>
                                <button type="button" class="btn btn-outline-secondary" id="toggleIconCatalog" aria-controls="iconCatalogo">Ver iconos</button>
                                <button type="button" class="btn btn-outline-danger" id="clearIconSelection">Quitar icono</button>
                                <span id="iconoPreview" class="border rounded px-2 py-1 d-inline-flex align-items-center gap-2">
                                    <i class="fa-regular fa-circle"></i>
                                    <span class="small">Sin icono</span>
                                </span>
                            </div>
                            <div class="icon-catalog d-none" id="iconCatalogo"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="paginaEstado" class="form-label">Estado</label>
                            <select class="form-select" id="paginaEstado" name="estado" required>
                                <option value="1">Activa</option>
                                <option value="0">Inactiva</option>
                            </select>
                        </div>
                        <div class="col-12" id="areaConfig">
                            <label class="form-label d-block">Areas permitidas</label>
                            <div class="area-list" id="paginaAreas">
                                <?php if (!empty($areas)) : ?>
                                    <?php foreach ($areas as $area) : ?>
                                        <label class="area-option">
                                            <input type="checkbox" name="areas[]" value="<?= (int) $area['id'] ?>">
                                            <span><?= esc($area['nombre']) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <div class="text-muted small">No hay areas activas</div>
                                <?php endif; ?>
                            </div>
                            <div class="form-text">Si la pagina es global, no es necesario elegir areas.</div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-4">
                                    <label for="paginaOrden" class="form-label">Orden</label>
                                    <input type="number" class="form-control" id="paginaOrden" name="orden" min="0" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="paginaSubmitBtn">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .icon-catalog {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
        padding: 10px;
        border: 1px solid #e5e5e5;
        border-radius: 10px;
        background: #fafafa;
        max-height: 320px;
        overflow-y: auto;
    }
    .icon-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border: 1px solid #dcdcdc;
        border-radius: 8px;
        background: #fff;
        cursor: pointer;
        font-size: 14px;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .icon-option i {
        font-size: 18px;
        width: 22px;
        text-align: center;
    }
    .icon-option input {
        display: none;
    }
    .icon-option:has(input:checked) {
        border-color: #0d6efd;
        box-shadow: 0 0 0 2px rgba(13, 110, 253, .15);
    }
    .area-list {
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        background: #fff;
        max-height: 200px;
        overflow-y: auto;
        padding: 8px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 6px 12px;
    }
    .area-option {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 8px;
        border-radius: 6px;
        cursor: pointer;
    }
    .area-option:hover {
        background: #f6f7f9;
    }
</style>

<script>
    const base_url = "<?= base_url() ?>";
    (function () {
        const toggleBtn = document.getElementById('toggleIconCatalog');
        const catalog = document.getElementById('iconCatalogo');
        const input = document.getElementById('iconoSeleccionado');
        const preview = document.getElementById('iconoPreview');
        const clearBtn = document.getElementById('clearIconSelection');
        const iconList = [
            'fa-solid fa-house|Inicio',
            'fa-solid fa-link|Enlace',
            'fa-solid fa-file-lines|Archivo',
            'fa-solid fa-gear|Config',
            'fa-solid fa-users|Usuarios',
            'fa-solid fa-chart-line|Dashboard',
            'fa-solid fa-list-check|Lista',
            'fa-solid fa-folder-open|Carpeta',
            'fa-solid fa-bell|Alertas',
            'fa-solid fa-calendar-days|Calendario',
            'fa-solid fa-envelope|Correo',
            'fa-solid fa-globe|Web',
            'fa-solid fa-shield-halved|Seguridad',
            'fa-solid fa-key|Acceso',
            'fa-solid fa-database|Datos',
            'fa-solid fa-layer-group|Catalogo',
            'fa-solid fa-image|Imagen',
            'fa-solid fa-clipboard|Notas',
            'fa-solid fa-address-book|Contacto',
            'fa-solid fa-circle-info|Info',
            'fa-solid fa-bolt|Accion',
            'fa-solid fa-pen-to-square|Editar',
            'fa-solid fa-print|Imprimir',
            'fa-solid fa-magnifying-glass|Buscar',
            'fa-solid fa-filter|Filtro',
            'fa-solid fa-plus|Agregar',
            'fa-solid fa-minus|Quitar',
            'fa-solid fa-trash|Eliminar',
            'fa-solid fa-rotate|Actualizar',
            'fa-solid fa-download|Descargar',
            'fa-solid fa-upload|Subir',
            'fa-solid fa-lock|Bloquear',
            'fa-solid fa-unlock|Desbloquear',
            'fa-solid fa-user|Usuario',
            'fa-solid fa-user-plus|Nuevo',
            'fa-solid fa-user-pen|Editar Usuario',
            'fa-solid fa-user-xmark|Bloqueado',
            'fa-solid fa-eye|Ver',
            'fa-solid fa-eye-slash|Ocultar',
            'fa-solid fa-house-user|Casa',
            'fa-solid fa-building|Edificio',
            'fa-solid fa-city|Ciudad',
            'fa-solid fa-road|Ruta',
            'fa-solid fa-location-dot|Ubicacion',
            'fa-solid fa-map|Mapa',
            'fa-solid fa-map-location-dot|Mapa 2',
            'fa-solid fa-phone|Telefono',
            'fa-solid fa-mobile-screen|Movil',
            'fa-solid fa-fax|Fax',
            'fa-solid fa-at|Arroba',
            'fa-solid fa-comment|Comentario',
            'fa-solid fa-comments|Comentarios',
            'fa-solid fa-comment-dots|Chat',
            'fa-solid fa-paper-plane|Enviar',
            'fa-solid fa-paperclip|Adjunto',
            'fa-solid fa-folder|Folder',
            'fa-solid fa-folder-tree|Arbol',
            'fa-solid fa-file|Archivo 2',
            'fa-solid fa-file-pdf|PDF',
            'fa-solid fa-file-excel|Excel',
            'fa-solid fa-file-word|Word',
            'fa-solid fa-file-powerpoint|PPT',
            'fa-solid fa-clipboard-list|Checklist',
            'fa-solid fa-check|Check',
            'fa-solid fa-circle-check|Ok',
            'fa-solid fa-xmark|Cerrar',
            'fa-solid fa-circle-xmark|Error',
            'fa-solid fa-triangle-exclamation|Alerta',
            'fa-solid fa-question|Pregunta',
            'fa-solid fa-circle-question|Ayuda',
            'fa-solid fa-info|Info 2',
            'fa-solid fa-star|Favorito',
            'fa-solid fa-heart|Like',
            'fa-solid fa-book|Libro',
            'fa-solid fa-bookmark|Marcador',
            'fa-solid fa-tag|Etiqueta',
            'fa-solid fa-tags|Etiquetas',
            'fa-solid fa-ticket|Boleto',
            'fa-solid fa-credit-card|Pago',
            'fa-solid fa-wallet|Cartera',
            'fa-solid fa-coins|Monedas',
            'fa-solid fa-money-bill|Dinero',
            'fa-solid fa-receipt|Recibo',
            'fa-solid fa-cart-shopping|Carrito',
            'fa-solid fa-basket-shopping|Compra',
            'fa-solid fa-truck|Envio',
            'fa-solid fa-box|Caja',
            'fa-solid fa-boxes-stacked|Almacen',
            'fa-solid fa-industry|Industria',
            'fa-solid fa-briefcase|Trabajo',
            'fa-solid fa-suitcase|Viaje',
            'fa-solid fa-clock|Hora',
            'fa-solid fa-hourglass|Espera',
            'fa-solid fa-calendar-check|Cita',
            'fa-solid fa-bullseye|Meta',
            'fa-solid fa-flag|Bandera',
            'fa-solid fa-trophy|Trofeo',
            'fa-solid fa-gauge|Rendimiento',
            'fa-solid fa-sliders|Ajustes',
            'fa-solid fa-wrench|Herramienta',
            'fa-solid fa-screwdriver-wrench|Servicio',
            'fa-solid fa-microchip|Tecnologia',
            'fa-solid fa-server|Servidor',
            'fa-solid fa-network-wired|Red',
            'fa-solid fa-wifi|Wifi',
            'fa-solid fa-signal|Senal',
            'fa-solid fa-desktop|PC',
            'fa-solid fa-laptop|Laptop',
            'fa-solid fa-tablet-screen-button|Tablet',
            'fa-solid fa-hard-drive|Disco',
            'fa-solid fa-cloud|Nube',
            'fa-solid fa-cloud-arrow-up|Subir Nube',
            'fa-solid fa-cloud-arrow-down|Bajar Nube',
            'fa-solid fa-shield|Proteccion',
            'fa-solid fa-bug|Bug',
            'fa-solid fa-code|Codigo',
            'fa-solid fa-terminal|Terminal',
            'fa-solid fa-database|Base',
            'fa-solid fa-robot|Robot',
            'fa-solid fa-camera|Camara',
            'fa-solid fa-video|Video',
            'fa-solid fa-music|Musica',
            'fa-solid fa-volume-high|Audio',
            'fa-solid fa-volume-xmark|Mute',
            'fa-solid fa-play|Play',
            'fa-solid fa-pause|Pausa',
            'fa-solid fa-stop|Stop',
            'fa-solid fa-forward|Adelante',
            'fa-solid fa-backward|Atras',
            'fa-solid fa-arrows-rotate|Sync',
            'fa-solid fa-arrows-up-down|Orden',
            'fa-solid fa-arrows-left-right|Mover',
            'fa-solid fa-chevron-up|Arriba',
            'fa-solid fa-chevron-down|Abajo',
            'fa-solid fa-chevron-left|Izquierda',
            'fa-solid fa-chevron-right|Derecha',
            'fa-solid fa-share|Compartir',
            'fa-solid fa-link-slash|Sin Enlace',
            'fa-solid fa-circle-plus|Agregar 2',
            'fa-solid fa-circle-minus|Quitar 2',
            'fa-solid fa-circle-dot|Punto',
            'fa-solid fa-square-check|Check 2',
            'fa-solid fa-square|Cuadro',
            'fa-solid fa-rectangle-list|Lista 2'
        ];

        if (toggleBtn && catalog) {
            toggleBtn.addEventListener('click', () => {
                catalog.classList.toggle('d-none');
                toggleBtn.textContent = catalog.classList.contains('d-none') ? 'Ver iconos' : 'Ocultar iconos';
            });
        }

        if (catalog) {
            const iconItems = iconList.map((item) => {
                const parts = item.split('|');
                return {
                    cls: parts[0],
                    label: parts[1] || parts[0]
                };
            }).sort((a, b) => a.label.localeCompare(b.label, 'es'));

            catalog.innerHTML = iconItems.map((item) => {
                return (
                    '<label class="icon-option">' +
                    '<input type="radio" name="icono_catalogo" value="' + item.cls + '">' +
                    '<i class="' + item.cls + '"></i>' +
                    '<span>' + item.label + '</span>' +
                    '</label>'
                );
            }).join('');
        }

        if (catalog && input && preview) {
            catalog.addEventListener('change', (e) => {
                const target = e.target;
                if (target && target.name === 'icono_catalogo') {
                    input.value = target.value;
                    preview.innerHTML = '<i class=\"' + target.value + '\"></i><span class=\"small\">' + target.closest('label').querySelector('span').textContent + '</span>';
                }
            });
        }

        if (clearBtn && catalog && input && preview) {
            clearBtn.addEventListener('click', () => {
                const checked = catalog.querySelector('input[name="icono_catalogo"]:checked');
                if (checked) {
                    checked.checked = false;
                }
                input.value = '';
                preview.innerHTML = '<i class="fa-regular fa-circle"></i><span class="small">Sin icono</span>';
            });
        }

        const areaConfig = document.getElementById('areaConfig');
        const paginaGlobal = document.getElementById('paginaGlobal');

        function toggleAreaConfig() {
            const isGlobal = paginaGlobal && paginaGlobal.checked;
            if (areaConfig) {
                areaConfig.classList.toggle('d-none', isGlobal);
            }
        }

        if (paginaGlobal) {
            paginaGlobal.addEventListener('change', toggleAreaConfig);
            toggleAreaConfig();
        }
    })();
</script>
<script src="<?= base_url('asset/js/cat_paginas.js') ?>"></script>

<?= $this->endSection() ?>
