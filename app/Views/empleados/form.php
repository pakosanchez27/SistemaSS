<body class="background-neza">

    <div class="container py-5">
        <div class="card shadow">
            <div class="fs-2 card-header bg-primary fw-bold text-white">
                Registro de Enlaces de Simplificación y Digitalización
            </div>
            <br>
            <div class="row mb-3">
                <div class="col-12 col-md-3">
                    <img src="<?= base_url('asset/img/USYDNEZA.jpeg') ?>" class="img-fluid img-thumbnail rounded" style="width: 70%,; height:200px; margin-left: 2%;"
                        alt="Logo USYD">
                </div>
                <div class="col-12 col-md-9">
                    <div class="alert alert alert-info fw-bolder" role="alert" style="margin-right: 5%; margin-top: 2%;">
                        <i class="fa-solid fa-circle-info"></i>
                        Con la finalidad de dar cumplimiento a la Ley Nacional para Eliminar Trámites Burocráticos,
                        y con base en el artículo 3 fracción XIV; 7 fracción III y IV; 14;15 de la Ley antes citada,
                        le solicito de la manera más atenta designar a su Enlace de Simplificación y Digitalización,
                        mismo que deberá contar con nivel jerárquico inferior inmediato al Titular del Área, para tal
                        fin y estar en posibilidad de darle la atención correspondiente, deberá cargar los siguientes
                        datos:
                    </div>
                </div>
            </div>


            <div class="card-body">

                <?php if (session()->getFlashdata('errors')): ?>
                <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error en el formulario',
                    text: 'Revisa los campos marcados en rojo'
                });
                </script>
                <?php endif; ?>


                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>

                    <?php if (session()->getFlashdata('descargar_id')): ?>
                        <script>
                        // Esto se ejecuta apenas carga la página con el mensaje de éxito
                        window.addEventListener('load', function() {
                            const id = "<?= session()->getFlashdata('descargar_id') ?>";
                            // Abrimos el PDF en una pestaña invisible/nueva para que se descargue
                            window.open("<?= base_url('empleados/generarComprobantesPDF/') ?>/" + id, '_blank');
                        });
                        </script>
                    <?php endif; ?>
                <?php endif; ?>

                <form id="form-empleado" method="POST" action="<?= base_url('empleados/store') ?>">
                    <?= csrf_field() ?>

                    <!-- ================= DATOS PERSONALES ================= -->

                    <div class="row mb-3">
                        <div class="col-12 col-md-6 col-lg-4">
                            <label>Nombre</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-4">
                            <label>Apellido Paterno</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user-tag"></i></span>
                                <input type="text" name="apellido_paterno" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-4">
                            <label>Apellido Materno</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user-tag"></i></span>
                                <input type="text" name="apellido_materno" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12 col-md-6">
                            <label>Teléfono</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                <input type="text" name="telefono" class="form-control" maxlength="10"
                                    inputmode="numeric" pattern="[0-9]{10}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="10 dígitos"
                                    required>

                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label>Correo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" name="correo" class="form-control" placeholder="name@neza.gob.mx"
                                    required>
                            </div>
                            <div class="form-text text-primary">
                                <i class="fa-solid fa-circle-info"></i>
                                De preferencia Institucional @neza.gob.mx
                            </div>
                        </div>
                    </div>

                    <!-- ================= AREA Y CARGO ================= -->

                    <div class="row mb-3">
                        <div class="col-12 col-lg-6">
                            <label>Área</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-building"></i></span>
                                <select name="area" class="form-select" required>
                                    <option value="">Seleccione un área</option>
                                    <?php foreach ($areas as $area): ?>
                                    <option value="<?= esc($area['nombre']) ?>">
                                        <?= esc($area['nombre']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <label>Cargo</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-sitemap"></i></span>
                                        <input type="text" name="cargo" class="form-control" required>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label>Años laborando</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa-solid fa-clock"></i></span>
                                        <input type="number" name="anos_laborando" class="form-control" min="0"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ================= SEXO Y EDAD ================= -->

                    <div class="row mb-3">
                        <div class="col-12 col-md-6">
                            <label><i class="fa-solid fa-venus-mars"></i> Sexo</label>
                            <select name="sexo" class="form-select" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($sexos as $s): ?>
                                <option value="<?= $s ?>"><?= $s ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label>Edad</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-cake-candles"></i>
                                </span>
                                <input type="number" name="edad" class="form-control" min="0" required>
                            </div>
                        </div>
                    </div>

                    <!-- ================= ESCOLARIDAD ================= -->

                    <div class="row mb-3">
                        <div class="col-12 col-md-6">
                            <label><i class="fa-solid fa-graduation-cap"></i> Escolaridad</label>
                            <select name="grado_estudios" id="grado" class="form-select" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($grados as $g): ?>
                                <option value="<?= $g ?>"><?= $g ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="d-none" id="TituloProfecion">
                                <i class="fa-solid fa-briefcase"></i> En
                            </label>
                            <input type="text" name="profesion" id="profesion" class="form-control d-none">
                        </div>
                    </div>

                    <!-- ================= DESCRIPCIÓN ================= -->

                    <div class="mb-3">
                        <label><i class="fa-solid fa-list-check"></i>
                            Descripción de labores
                        </label>
                        <textarea name="descripcion_labores" class="form-control" required></textarea>
                    </div>

                    <!-- ================= SOFTWARE ================= -->

                    <div class="row mb-3">
                        <label>
                            <i class="fa-solid fa-laptop-code"></i>
                            Manejo de paquetería / software y nivel de conocimeinto
                        </label>

                        <div class="form-text text-primary">
                            <i class="fa-solid fa-circle-info"></i>
                            Información se usara solo para fines de capacitación
                        </div>
                        <div class="col-12 list-sofware">


                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Software</th>
                                            <th style="min-width:250px;">
                                                Nivel de conocimiento
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($software as $s): ?>
                                        <tr>
                                            <td><strong><?= esc($s['nombre']) ?></strong></td>
                                            <td>
                                                <select name="software[<?= $s['id'] ?>]" class="form-select" required>
                                                    <option value="">Seleccionar</option>
                                                    <?php foreach ($conocimiento as $s): ?>
                                                    <option value="<?= $s ?>"><?= $s ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <hr>

                            <div class="row mt-3 align-items-center">
                                <div class="col-12 col-md-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="toggleOtroPrograma">
                                        <label class="form-check-label" for="toggleOtroPrograma">
                                            Otro programa
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <input type="text" name="nuevo_software[]" id="inputOtroSoftware"
                                        class="form-control" placeholder="Otro software" disabled>
                                </div>

                                <div class="col-12 col-md-3">
                                    <select name="nuevo_software_nivel[]" id="selectOtroNivel" class="form-select"
                                        disabled>
                                        <option value="">Seleccione nivel</option>
                                        <option value="Sin conocimiento">Sin conocimiento</option>
                                        <option value="Basico">Basico</option>
                                        <option value="Intermedio">Intermedio</option>
                                        <option value="Avanzado">Avanzado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-text text-primary mt-2">
                                <i class="fa-solid fa-circle-info"></i>
                                El software agregado quedará disponible para futuros registros
                            </div>

                        </div>
                    </div>

                    <input type="hidden" name="accion" id="accion">

                    <button type="button" class="btn btn-success" onclick="confirmarAccion()">
                        <i class="fa-solid fa-check"></i> Aceptar
                    </button>

                </form>
            </div>
        </div>
    </div>

    <!-- JS CORRECTO -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // Mostrar / ocultar profesión
        document.getElementById('grado').addEventListener('change', function() {
            const requiere = ['Bachillerato', 'Licenciatura', 'Maestria', 'Doctorado'];
            const titulo = document.getElementById('TituloProfecion');
            const profesion = document.getElementById('profesion');

            titulo.classList.toggle('d-none', !requiere.includes(this.value));
            profesion.classList.toggle('d-none', !requiere.includes(this.value));
        });

        // Normalizar texto
        function normalizar(campo) {
            campo.addEventListener('input', function() {
                let v = this.value;
                v = v.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                v = v.replace(/[^A-Za-z0-9 ]/g, '');
                this.value = v.toUpperCase();
            });
        }

        ['nombre', 'apellido_paterno', 'apellido_materno', 'profesion', 'cargo']
        .forEach(n => {
            const c = document.querySelector(`[name="${n}"]`);
            if (c) normalizar(c);
        });

    });

    // habilitar otro
    document.addEventListener('DOMContentLoaded', function() {

        const toggle = document.getElementById('toggleOtroPrograma');
        const input = document.getElementById('inputOtroSoftware');
        const select = document.getElementById('selectOtroNivel');

        toggle.addEventListener('change', function() {

            if (this.checked) {
                input.disabled = false;
                select.disabled = false;
            } else {
                input.disabled = true;
                select.disabled = true;

                // Limpiar valores cuando se desactive
                input.value = '';
                select.value = '';
            }

        });

    });

    function validarFormulario() {

        let valido = true;

        // Limpiar errores anteriores
        document.querySelectorAll('.input-error').forEach(el => {
            el.classList.remove('input-error');
        });

        function marcarError(campo) {
            campo.classList.add('input-error');
            valido = false;
        }

        // ==============================
        // VALIDACIONES INDIVIDUALES
        // ==============================

        const nombre = document.querySelector('[name="nombre"]');
        if (nombre.value.trim().length < 2) marcarError(nombre);

        const apellidoP = document.querySelector('[name="apellido_paterno"]');
        if (apellidoP.value.trim().length < 2) marcarError(apellidoP);

        const telefono = document.querySelector('[name="telefono"]');
        const telefonoRegex = /^[0-9]{10}$/;
        if (!telefonoRegex.test(telefono.value.trim())) marcarError(telefono);

        const correo = document.querySelector('[name="correo"]');
        const correoRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!correoRegex.test(correo.value.trim())) marcarError(correo);

        const area = document.querySelector('[name="area"]');
        if (!area.value) marcarError(area);

        const cargo = document.querySelector('[name="cargo"]');
        if (!cargo.value.trim()) marcarError(cargo);

        const anos = document.querySelector('[name="anos_laborando"]');
        if (!/^[0-9]+$/.test(anos.value) || anos.value === "") marcarError(anos);

        const edad = document.querySelector('[name="edad"]');
        if (!/^[0-9]+$/.test(edad.value) || edad.value === "") marcarError(edad);

        const grado = document.querySelector('[name="grado_estudios"]');
        if (!grado.value) marcarError(grado);

        const sexo = document.querySelector('[name="sexo"]');
        if (!sexo.value) marcarError(sexo);

        const descripcion = document.querySelector('[name="descripcion_labores"]');
        if (!descripcion.value.trim()) marcarError(descripcion);

        // ==============================
        // SOFTWARE
        // ==============================

        const softwareSelects = document.querySelectorAll('select[name^="software["]');
        softwareSelects.forEach(select => {
            if (!select.value) marcarError(select);
        });

        const nuevosSoft = document.querySelectorAll('input[name="nuevo_software[]"]');
        const nuevosNivel = document.querySelectorAll('select[name="nuevo_software_nivel[]"]');

        nuevosSoft.forEach((input, i) => {
            const nombreSoft = input.value.trim();
            const nivelSoft = nuevosNivel[i].value;

            if ((nombreSoft && !nivelSoft) || (!nombreSoft && nivelSoft)) {
                marcarError(input);
                marcarError(nuevosNivel[i]);
            }
        });

        // ==============================
        // ALERTA
        // ==============================

        if (!valido) {
            Swal.fire({
                icon: 'warning',
                title: 'Formulario incompleto o incorrecto',
                text: 'Revisa los campos marcados en rojo'
            });
        }

        return valido;
    }

    let enviando = false;

    function confirmarAccion() {

        if (!validarFormulario()) {
            return;
        }

        if (enviando) return;

        Swal.fire({
            title: 'Confirmar acción',
            text: '¿Qué deseas hacer?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Guardar información',
            cancelButtonText: 'Agregar sub auxiliar',
            reverseButtons: true
        }).then((result) => {

            if (!result.isConfirmed && result.dismiss !== Swal.DismissReason.cancel) {
                return;
            }

            enviando = true;

            const form = document.getElementById('form-empleado');
            const btn = document.querySelector('.btn-success');

            btn.disabled = true;
            btn.innerHTML = 'Procesando...';

            document.getElementById('accion').value =
                result.isConfirmed ? 'principal' : 'sub';

            form.submit();
        });
    }
    </script>

</body>