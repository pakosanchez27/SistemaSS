<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<h3>Asignar Permisos a Roles</h3>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<form method="post" action="<?= base_url('admin/roles/permisos/asignar') ?>">

    <div class="mb-3">
        <label>Seleccionar Rol</label>
        <select name="rol_id" id="rol_id" class="form-control" required>
            <?php foreach ($roles as $rol): ?>
                <option value="<?= $rol['id'] ?>" <?= ((int) $rol['id'] === (int) ($selectedRolId ?? 0)) ? 'selected' : '' ?>>
                    <?= esc($rol['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php
    $permGroups = [
        'Dashboard' => ['ver-dashboard'],
        'Usuarios' => ['usuarios-ver', 'usuarios-crear', 'usuarios-editar', 'usuarios-eliminar'],
        'Roles y permisos' => ['roles-ver', 'roles-crear', 'roles-editar', 'roles-asignar-permisos'],
        'Areas' => ['areas-ver', 'areas-crear', 'areas-editar'],
        'Registros' => ['registros-ver', 'registros-crear', 'registros-editar', 'registros-eliminar'],
        'Evidencias' => ['evidencias-subir', 'evidencias-ver', 'evidencias-eliminar'],
        'Reportes' => ['reportes-ver', 'exportar-excel', 'generar-pdf'],
        'Estadisticas' => ['ver-estadisticas'],
        'Administracion avanzada' => ['configuracion-sistema', 'admin-modulos'],
    ];

    $permisosByName = [];
    if (!empty($permisos)) {
        foreach ($permisos as $p) {
            $permisosByName[$p['name']] = $p;
        }
    }

    $groupedNames = [];
    foreach ($permGroups as $names) {
        foreach ($names as $n) {
            $groupedNames[$n] = true;
        }
    }

    $otros = [];
    foreach ($permisosByName as $name => $perm) {
        if (!isset($groupedNames[$name])) {
            $otros[] = $name;
        }
    }
    if (!empty($otros)) {
        $permGroups['Otros'] = $otros;
    }

    $assignedMap = array_fill_keys(array_map('intval', $assignedPermIds ?? []), true);
    ?>

    <label>Permisos:</label>
    <div class="accordion" id="permisosAccordion">
        <?php $i = 0; ?>
        <?php foreach ($permGroups as $groupName => $permNames): ?>
            <?php $i++; $headingId = 'permHeading' . $i; $collapseId = 'permCollapse' . $i; ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="<?= $headingId ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#<?= $collapseId ?>" aria-expanded="false" aria-controls="<?= $collapseId ?>">
                        <?= esc($groupName) ?>
                    </button>
                </h2>
                <div id="<?= $collapseId ?>" class="accordion-collapse collapse" aria-labelledby="<?= $headingId ?>"
                    data-bs-parent="#permisosAccordion">
                    <div class="accordion-body">
                        <div class="row">
                            <?php foreach ($permNames as $permName): ?>
                                <?php if (!isset($permisosByName[$permName])) continue; ?>
                                <?php $perm = $permisosByName[$permName]; ?>
                                <?php $isChecked = isset($assignedMap[(int) $perm['id']]); ?>
                                <div class="col-12 col-md-6 col-lg-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input permiso-checkbox" type="checkbox" name="permisos[]"
                                            id="perm<?= $perm['id'] ?>" value="<?= $perm['id'] ?>" <?= $isChecked ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="perm<?= $perm['id'] ?>">
                                            <?= esc($perm['description'] ?: $perm['name']) ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <button class="btn btn-success mt-3">Guardar Permisos</button>
</form>

<script>
    (function() {
        const baseUrl = document.getElementById('base_url')?.value || '';
        const rolSelect = document.getElementById('rol_id');
        const checkboxes = () => document.querySelectorAll('.permiso-checkbox');

        function setChecked(permIds) {
            const map = new Set(permIds.map(Number));
            checkboxes().forEach(cb => {
                cb.checked = map.has(Number(cb.value));
            });
        }

        async function loadPermisosForRol(rolId) {
            if (!rolId) return;
            try {
                const res = await fetch(`${baseUrl}/admin/roles/permisos/por-rol?rol_id=${rolId}`);
                const json = await res.json();
                if (json && json.ok) {
                    setChecked(json.data || []);
                }
            } catch (e) {
                // No-op: evita romper la vista si falla el request
            }
        }

        if (rolSelect) {
            rolSelect.addEventListener('change', () => {
                loadPermisosForRol(rolSelect.value);
            });
        }
    })();
</script>

<?= $this->endSection() ?>
