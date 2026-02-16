<body class="bg-light background-neza">

    <div class="container logged">
        <div class="container-logged card shadow mx-auto">
            <div class="card-header head-container text-white text-center">
                <h4 class="mb-0">Iniciar Sesión</h4>
            </div>

            <div class="row g-0 align-items-stretch container-logged">
                <!-- Imagen -->
                <div class="col-12 col-md-6 d-none d-md-block">
                    <img src="<?= base_url('asset/img/USYDNEZA.jpeg') ?>" class="icon-logged img-fluid" alt="Logo USYD">
                </div>

                <!-- Formulario -->
                <div class="col-12 col-md-6">
                    <div class="card-body">
                        <?php if(session('error')): ?>
                        <div class="alert alert-danger">
                            <?= session('error') ?>
                        </div>
                        <?php endif; ?>

                        <form method="post" action="<?= base_url('login/attempt') ?>">
                            <div class="mb-4">
                                <label class="form-label text-uppercase fw-bold">Usuario</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-user"></i> </span>
                                    <input type="text" name="username" class="form-control" placeholder="example@neza.gob.mx" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label text-uppercase fw-bold">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="*************" required>
                                </div>
                            </div>

                            <button class="btn btn-primary w-100"><i class="fa-solid fa-check"></i> Entrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
