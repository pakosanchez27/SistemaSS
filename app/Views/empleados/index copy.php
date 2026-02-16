<body class="background-neza">

    <div class="container-xl py-5 view_enlaces">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
            <h3 class="mb-0">Listado de Empleados</h3>

            <div class="d-flex align-items-center gap-2">
                <a href="<?= base_url('empleados/exportarExcel') ?>" class="btn btn-success">
                    <i class="fa-solid fa-file-excel"></i> Descargar Excel
                </a>

                <span class="badge bg-secondary">
                    Total: <?= count($empleados) ?>
                </span>
            </div>
        </div>


        <div class="card shadow border-0 rounded-3">
            <div class="card-body p-4 table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small">
                        <tr>
                            <th>Empleado</th>
                            <th>Cargo</th>
                            <th>Área</th>
                            <th class="text-center">Edad</th>
                            <th class="text-center">Años</th>
                            <th>Estudios</th>
                            <th>Profesión</th>
                            <th class="text-center">Sexo</th>
                            <th class="text-center">Detalle</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($empleados as $e): ?>
                        <tr>
                            <td>
                                <strong>
                                    <?= esc($e['nombre']) ?>
                                    <?= esc($e['apellido_paterno']) ?>
                                    <?= esc($e['apellido_materno']) ?>
                                </strong>
                            </td>

                            <td class="text-center">
                                <?= esc(mb_strtoupper($e['cargo'], 'UTF-8')) ?>
                            </td>

                            <td><?= esc($e['area']) ?></td>
                            <td class="text-center"><?= esc($e['edad']) ?></td>
                            <td class="text-center"><?= esc($e['anos_laborando']) ?></td>
                            <td><?= esc($e['grado_estudios']) ?></td>
                            <td><?= esc($e['profesion'] ?? '—') ?></td>
                            <td class="text-center"><?= esc($e['sexo']) ?></td>

                            <!-- Botón detalle -->
                            <td class="text-center align-middle">
                                <div class="btn-group btn-group-sm" role="group">

                                    <button class="btn btn-outline-secondary" data-bs-toggle="collapse"
                                        data-bs-target="#detalle-<?= $e['id'] ?>" title="Ver detalles">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <button class="btn btn-outline-danger" onclick="generarPDF(<?= $e['id'] ?>)"
                                        title="Generar PDF">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </button>

                                </div>
                            </td>
                        </tr>

                        <!-- FILA DETALLE -->
                        <tr class="collapse bg-light" id="detalle-<?= $e['id'] ?>">
                            <td colspan="9">
                                <div class="p-3">
                                    <div class="row mt-3">
                                        <div class="col-md-4">
                                            <strong>Teléfono:</strong><br>
                                            <?= esc($e['telefono']) ?>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Correo:</strong><br>
                                            <?= esc($e['correo']) ?>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Registro:</strong><br>
                                            <?= date('d/m/Y', strtotime($e['created_at'])) ?>
                                        </div>
                                    </div>


                                    <p class="mb-2">
                                        <strong>📄 Descripción de labores:</strong><br>
                                        <?= esc($e['descripcion_labores']) ?>
                                    </p>

                                    <p class="mb-2">
                                        <strong>💻 Software y nivel de uso:</strong>
                                    </p>

                                    <?php if (!empty($e['software'])): ?>
                                    <ul class="mb-0">
                                        <?php foreach ($e['software'] as $sw): ?>
                                        <li>
                                            <?= esc($sw['nombre']) ?> —
                                            <span class="fw-bold"><?= esc($sw['nivel']) ?></span>
                                        </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php else: ?>
                                    <span class="text-muted">No registró software</span>
                                    <?php endif; ?>


                                </div>
                            </td>
                        </tr>

                        <?php endforeach; ?>

                    </tbody>
                </table>

            </div>
        </div>

    </div>
    <script>
    function generarPDF(id) {
        window.open("<?= base_url('empleados/generarComprobantesPDF/') ?>/" + id, '_blank');
    }
    </script>

</body>