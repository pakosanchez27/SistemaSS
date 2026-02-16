<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <style>
    @page {
        margin: 0;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 14px;
        margin: 0;
        padding: 0;
        color: #000;
    }

    /* Fondo institucional */
    .background-membrete {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1000;
    }

    .background-membrete img {
        width: 100%;
        height: 100%;
    }

    /* Área de contenido */
    .contenido {
        padding: 170px 70px 90px 70px;
    }

    .persona {
        page-break-after: always;
        position: relative;
    }

    .persona:last-child {
        page-break-after: avoid;
    }

    /* Encabezados */
    h1 {
        text-align: center;
        font-size: 18px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 40px;
    }

    h3 {
        margin-top: 30px;
        margin-bottom: 10px;
        font-size: 16px;
        text-transform: uppercase;
    }

    /* Texto */
    p {
        line-height: 1.7;
        text-align: justify;
        margin: 8px 0;
    }

    /* Línea decorativa */
    .divider {
        margin: 25px 0;
        border-top: 1px solid #000;
    }

    /* Bloque de datos */
    .datos {
        margin-top: 10px;
        margin-bottom: 25px;
    }

    .datos p {
        margin: 4px 0;
    }

    /* Fecha */
    .fecha {
        text-align: right;
        font-size: 13px;
        margin-bottom: 40px;
    }

    /* Firma */
    .firma {
        margin-top: 90px;
        text-align: center;
        width: 100%;
    }

    .titulo-area {
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 25px;
    }

    .firma-imagen img {
        width: 200px;
        margin-bottom: 10px;
    }

    .linea {
        width: 50%;
        margin: 10px auto 5px auto;
        border-top: 1px solid #000;
    }

    .nombre-titular {
        font-weight: bold;
        margin-top: 5px;
    }

    .cargo-titular {
        font-size: 12px;
        margin-top: 3px;
    }

    </style>
</head>

<body>

    <?php foreach ($personas as $p): ?>
    <div class="persona">

        <div class="background-membrete">
            <img src="<?= FCPATH ?>asset/img/membrete.png">
        </div>

        <div class="contenido">

            <div class="fecha">
                <strong>Fecha:</strong> <?= date('d/m/Y') ?>
            </div>

            <h1>
                Registro de Enlaces de Simplificación y Digitalización
            </h1>

            <div class="divider"></div>

            <h3>
                <?= esc($p['nombre'].' '.$p['apellido_paterno'].' '.$p['apellido_materno']) ?>
            </h3>

            <div class="datos">
                <p><strong>Área:</strong> <?= esc($p['area']) ?></p>
                <p>
                    <strong>Cargo:</strong>
                    <?= $p['cargo']?>
                </p>
            </div>

            <p>
                Con la finalidad de dar cumplimiento a la Ley Nacional para Eliminar
                Trámites Burocráticos, y con base en el artículo 3 fracción XIV;
                7 fracción III y IV; 14 y 15 de la Ley antes citada, se le informa
                que ha sido registrado(a) como
                <strong>Enlace de Simplificación y Digitalización</strong>,
                para los efectos administrativos y operativos correspondientes.
            </p>

            <div class="firma">

                <div class="titulo-area">
                    UNIDAD DE SIMPLIFICACION Y DIGITALIZACION DE NEZAHUALCOYOTL
                </div>

                <!-- <div class="firma-imagen">
                    <img src="<?php //FCPATH ?>asset/img/firma_miriam.png" alt="Firma">
                </div>
                <br>
                <br>
                <br>
                <br> -->
                <div class="linea"></div>

                <div class="nombre-titular">
                    Ing. Miriam López Pérez
                </div>

                <div class="cargo-titular">
                    Titular
                </div>

            </div>



        </div>
    </div>
    <?php endforeach; ?>

</body>

</html>