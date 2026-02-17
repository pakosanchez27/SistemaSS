<body class="background-neza">

<div class="container-fluid adminContainer">
    <div class="row">
        
        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar">
            <h4 class="text-center mt-3">ADMIN</h4>
            <hr>

            <a href="<?= base_url('admin/dashboard') ?>">📊 Dashboard</a>
            <a href="<?= base_url('admin/usuarios') ?>">👥 Usuarios</a>
            <a href="<?= base_url('admin/roles') ?>">🔐 Roles</a>
            <a href="<?= base_url('admin/roles/permisos') ?>">⚙ Asignar Permisos a Roles</a>
            <a href="<?= base_url('admin/usuarios/permisos') ?>">🎯 Permisos por Usuario</a>
            <div class="sidebar-dropdown">
                <a href="#catalogosMenu" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="catalogosMenu">
                    <i class="fa-solid fa-layer-group mr-2"></i> Catalogos
                </a>
                <div class="collapse" id="catalogosMenu">
                    <a class="ms-3" href="<?= base_url('admin/software') ?>">Software</a>
                    <a class="ms-3" href="<?= base_url('admin/areas') ?>">Areas</a>
                    <a class="ms-3" href="<?= base_url('admin/permisos') ?>">Permisos</a>
                </div>
            </div>

            <hr>
            <a href="<?= base_url('empleados') ?>">📋 Ir al Sistema</a>
            <hr>
            <a href="<?= base_url('enlases') ?>"><i class="fa-solid fa-arrows-down-to-people"></i> Enlases</a>
            <hr>
            <a href="<?= base_url('logout') ?>" class="text-danger">🚪 Cerrar Sesión</a>
        </div>

        <!-- CONTENIDO -->
        <div class="col-md-10 content">
            <?= $this->renderSection('content') ?>
        </div>

    </div>
</div>

</body>