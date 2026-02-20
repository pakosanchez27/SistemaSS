<body class="background-neza">

<div class="container-fluid adminContainer">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar">
            <div class="sidebar-brand">
                <div class="brand-text">
                    <span>Panel</span>
                    <strong>Administrativo</strong>
                </div>
            </div>

            <?php
            helper('user');
            $currentUser = current_user();
            $isRoot = (int) ($currentUser['is_root'] ?? 0) === 1;
            $areaId = (int) ($currentUser['area_id'] ?? 0);
            $db = \Config\Database::connect();

            $globalPages = [];
            $areaPages = [];

            if ($currentUser) {
                if ($isRoot) {
                    $globalPages = $db->query("
                        SELECT id, nombre, ruta, icono
                        FROM paginas
                        WHERE estado = 1 AND deleted_at IS NULL AND es_global = 1
                        ORDER BY nombre
                    ")->getResultArray();

                    $areaPages = $db->query("
                        SELECT p.id, p.nombre, p.ruta, p.icono, a.nombre AS area_nombre
                        FROM area_paginas ap
                        INNER JOIN paginas p ON p.id = ap.pagina_id
                        INNER JOIN areas a ON a.id = ap.area_id
                        WHERE p.estado = 1 AND p.deleted_at IS NULL
                        ORDER BY a.nombre, p.nombre
                    ")->getResultArray();
                } else {
                    $globalPages = $db->query("
                        SELECT p.id, p.nombre, p.ruta, p.icono
                        FROM paginas p
                        INNER JOIN usuario_paginas up ON up.pagina_id = p.id AND up.usuario_id = ? AND up.puede_ver = 1
                        WHERE p.estado = 1 AND p.deleted_at IS NULL AND p.es_global = 1
                        ORDER BY p.nombre
                    ", [$currentUser['id']])->getResultArray();

                    $areaPages = $db->query("
                        SELECT p.id, p.nombre, p.ruta, p.icono, a.nombre AS area_nombre
                        FROM area_paginas ap
                        INNER JOIN paginas p ON p.id = ap.pagina_id
                        INNER JOIN areas a ON a.id = ap.area_id
                        INNER JOIN usuario_paginas up ON up.pagina_id = p.id AND up.usuario_id = ? AND up.puede_ver = 1
                        WHERE p.estado = 1 AND p.deleted_at IS NULL AND ap.area_id = ?
                        ORDER BY p.nombre
                    ", [$currentUser['id'], $areaId])->getResultArray();
                }
            }

            $groupedByArea = [];
            foreach ($areaPages as $p) {
                $areaName = $p['area_nombre'] ?? 'Area';
                if (!isset($groupedByArea[$areaName])) {
                    $groupedByArea[$areaName] = [];
                }
                $groupedByArea[$areaName][] = $p;
            }
            ?>

            <?php if ($isRoot): ?>
                <div class="sidebar-section-title">Principal</div>
            <?php endif; ?>
            <?php foreach ($globalPages as $p): ?>
                <a href="<?= base_url(ltrim($p['ruta'], '/')) ?>">
                    <i class="<?= esc($p['icono'] ?: 'fa-regular fa-circle') ?>"></i>
                    <span><?= esc($p['nombre']) ?></span>
                </a>
            <?php endforeach; ?>

            <?php if (!empty($groupedByArea)): ?>
                <?php foreach ($groupedByArea as $areaName => $items): ?>
                    <?php if ($isRoot): ?>
                        <div class="sidebar-section-title">Paginas del area <?= esc($areaName) ?></div>
                    <?php endif; ?>
                    <?php foreach ($items as $p): ?>
                        <a href="<?= base_url(ltrim($p['ruta'], '/')) ?>">
                            <i class="<?= esc($p['icono'] ?: 'fa-regular fa-circle') ?>"></i>
                            <span><?= esc($p['nombre']) ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($isRoot): ?>
                <div class="sidebar-section-title">Catalogos</div>
                <div class="sidebar-dropdown">
                    <a href="#catalogosMenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="catalogosMenu">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>Catalogos</span>
                        <i class="fa-solid fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse" id="catalogosMenu">
                        <a class="ms-3" href="<?= base_url('admin/software') ?>">Software</a>
                        <a class="ms-3" href="<?= base_url('admin/areas') ?>">Areas</a>
                        <a class="ms-3" href="<?= base_url('admin/permisos') ?>">Permisos</a>
                        <a class="ms-3" href="<?= base_url('admin/roles') ?>">Roles</a>
                        <a class="ms-3" href="<?= base_url('admin/paginas') ?>">Paginas</a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mt-auto"></div>
            <a href="<?= base_url('admin/perfil') ?>" class="mt-3">
                <i class="fa-solid fa-user"></i>
                <span>Perfil</span>
            </a>
            <a href="<?= base_url('logout') ?>" class="logout-link">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Cerrar Sesion</span>
            </a>

        </div>

        <!-- CONTENIDO -->
        <div class="col-md-10 content">
            <?= $this->renderSection('content') ?>
        </div>

    </div>
</div>

</body>
