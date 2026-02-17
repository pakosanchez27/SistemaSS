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

            <div class="sidebar-section-title">Principal</div>
            <a href="<?= base_url('admin/dashboard') ?>">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= base_url('admin/usuarios') ?>">
                <i class="fa-solid fa-users"></i>
                <span>Usuarios</span>
            </a>
            <a href="<?= base_url('admin/roles/permisos') ?>">
                <i class="fa-solid fa-gear"></i>
                <span>Permisos por Rol</span>
            </a>

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
                </div>
            </div>

            <div class="sidebar-section-title">Sistema</div>
            <a href="<?= base_url('empleados') ?>">
                <i class="fa-solid fa-list-check"></i>
                <span>Ir al Sistema</span>
            </a>
            <a href="<?= base_url('enlases') ?>">
                <i class="fa-solid fa-arrows-down-to-people"></i>
                <span>Enlaces</span>
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
