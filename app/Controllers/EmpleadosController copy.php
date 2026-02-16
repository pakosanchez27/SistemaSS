<?php

namespace App\Controllers;

use App\Models\EmpleadosModel;
use App\Models\AreasModel;
use App\Models\SoftwareModel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\TemplateProcessor;
use Dompdf\Dompdf;
use Dompdf\Options;

class EmpleadosController extends BaseController
{

    public bool $enableEmployeesView = true;

    private array $gradosValidos = [
        'Primaria',
        'Secundaria',
        'Bachillerato',
        'Licenciatura',
        'Maestria',
        'Doctorado'
    ];

    private array $gradosConProfesion = [
        'Bachillerato',
        'Licenciatura',
        'Maestria',
        'Doctorado'
    ];

    private array $sexosValidos = [
        'Masculino',
        'Femenino',
        'Otro'
    ];

    private array $cargosValidos = [
        'Titular del area',
        'Secretatio(a)',
        'Auxiliar administrativo'
    ];

    private array $conocimientoValidos = [
        //'No lo utilizo',
        'Sin conocimiento',
        'Basico',
        'Intermedio',
        'Avanzado'
    ];

    public function create()
    {
        $css = [
            'style' => 'style.css'
        ];

        $areasModel = new AreasModel();
        $softwareModel = new SoftwareModel();

        $areas = $areasModel
            ->where('activo', 1)
            ->orderBy('nombre', 'ASC')
            ->findAll();

        $software = $softwareModel
            ->orderBy('nombre', 'ASC')
            ->findAll();

        return view('base/head', $css)
            . view('base/header')
            . view('empleados/form', [
                'grados'   => $this->gradosValidos,
                'sexos'    => $this->sexosValidos,
                'cargos'    => $this->cargosValidos,
                'conocimiento'    => $this->conocimientoValidos,
                'areas'    => $areas,
                'software' => $software
            ])
            . view('base/footer');
    }

    public function store()
    {
        // 1. VALIDACIONES BASICAS
        $rules = [
            'nombre'              => 'required|min_length[2]',
            'apellido_paterno'    => 'required|min_length[2]',
            'telefono'            => 'required|regex_match[/^[0-9]{10}$/]',
            'correo'              => 'required|valid_email',
            'area'                => 'required',
            'cargo'                => 'required',
            'anos_laborando'      => 'required|is_natural',
            'edad'                => 'required|is_natural',
            'grado_estudios'      => 'required',
            'sexo'                => 'required',
            'descripcion_labores' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode('<br>', $this->validator->getErrors()));
        }

        // 2. OBTENER DATOS Y ACCION
        $data   = $this->request->getPost();
        $accion = $this->request->getPost('accion');

        unset($data['accion'], $data['csrf_token'], $data['software'], $data['nuevo_software'], $data['nuevo_software_nivel']);

        // 3. VALIDAR CATALOGOS
        if (!in_array($data['grado_estudios'], $this->gradosValidos)) {
            return redirect()->back()->with('error', 'Grado no válido');
        }

        if (!in_array($data['sexo'], $this->sexosValidos)) {
            return redirect()->back()->with('error', 'Sexo no válido');
        }

        if (!in_array($data['grado_estudios'], $this->gradosConProfesion)) {
            $data['profesion'] = null;
        } elseif (empty($data['profesion'])) {
            return redirect()->back()->with('error', 'Debe indicar la profesión');
        }

        // 4. NORMALIZACION DE TEXTO
        $camposTexto = [
            'nombre',
            'apellido_paterno',
            'apellido_materno',
            'area',
            'profesion',
            'descripcion_labores'
        ];

        foreach ($camposTexto as $campo) {
            if (isset($data[$campo]) && $data[$campo] !== '') {
                $texto = trim($data[$campo]);
                $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto) ?: $texto;
                $texto = strtoupper($texto);
                $texto = preg_replace('/[^A-Z0-9 ]/', '', $texto);
                $texto = trim(preg_replace('/\s+/', ' ', $texto));
                $data[$campo] = $texto;
            }
        }

        // 5. AGREGAR REGISTRO TEMPORAL
        if ($accion === 'sub') {

            $subs = session()->get('subs') ?? [];
            $data['parent_id'] = null;

            $subs[] = $data;
            session()->set('subs', $subs);

            return redirect()->back()->with('success', 'Registro agregado temporalmente');
        }

        // 6. GUARDAR REGISTROS DEFINITIVOS
        if ($accion === 'principal') {

            $db    = db_connect();
            $model = new EmpleadosModel();

            $db->transStart();

            $subs = session()->get('subs') ?? [];
            $subs[] = $data;

            // Guardar principal
            $principal = array_shift($subs);
            $principal['tipo'] = 'PRINCIPAL';
            $principal['parent_id'] = null;

            $model->insert($principal);
            $principalId = $model->insertID();

            // 7. GUARDAR SOFTWARE EXISTENTE
            $softwareSeleccionado = $this->request->getPost('software') ?? [];

            foreach ($softwareSeleccionado as $softwareId => $nivel) {
                if ($nivel === '') {
                    continue;
                }

                $db->table('empleado_software')->insert([
                    'empleado_id' => $principalId,
                    'software_id' => $softwareId,
                    'nivel_uso'   => $nivel
                ]);
            }

            // 8. GUARDAR NUEVOS SOFTWARES
            $nuevosSoftwares = $this->request->getPost('nuevo_software') ?? [];
            $nuevosNiveles   = $this->request->getPost('nuevo_software_nivel') ?? [];

            $softwareTable = $db->table('software');

            foreach ($nuevosSoftwares as $i => $nombreRaw) {

                $nombreRaw = trim($nombreRaw);
                $nivel     = $nuevosNiveles[$i] ?? '';

                if ($nombreRaw === '' || $nivel === '') {
                    continue;
                }

                $nombre = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nombreRaw) ?: $nombreRaw;
                $nombre = strtoupper($nombre);
                $nombre = preg_replace('/[^A-Z0-9 ]/', '', $nombre);
                $nombre = trim(preg_replace('/\s+/', ' ', $nombre));

                if ($nombre === '') {
                    continue;
                }

                $existente = $softwareTable
                    ->where('nombre', $nombre)
                    ->get()
                    ->getRowArray();

                if ($existente) {
                    $softwareId = $existente['id'];
                } else {
                    $softwareTable->insert(['nombre' => $nombre]);
                    $softwareId = $db->insertID();
                }

                $db->table('empleado_software')->insert([
                    'empleado_id' => $principalId,
                    'software_id' => $softwareId,
                    'nivel_uso'   => $nivel
                ]);
            }

            // 9. GUARDAR SUB ENLACES
            foreach ($subs as $sub) {
                $sub['tipo'] = 'SUB';
                $sub['parent_id'] = $principalId;
                $model->insert($sub);
            }

            session()->remove('subs');

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Error al guardar la información');
            }

            //PDF's
            //$this->generarComprobantesPDF($principalId);
            //return redirect()->back()->with('success', 'Registro completo guardado correctamente');

            return redirect()->back()
                ->with('success', 'Registro completo guardado correctamente')
                ->with('descargar_id', $principalId);
        }

        return redirect()->back()->with('error', 'Acción no válida');
    }

    public function generarComprobantesPDF(int $principalId)
    {
        $db = db_connect();

        $personas = $db->table('empleados')
            ->where('id', $principalId)
            ->orWhere('parent_id', $principalId)
            ->get()
            ->getResultArray();
            
        $html = view('empleados/comprobante_pdf', [
            'personas' => $personas
        ]);

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'DejaVu Sans');
        // $options->set('isRemoteEnabled', false); // ya no HTTP
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); 
        $options->set('chroot', FCPATH);
        

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        return $dompdf->stream(
            'COMPROBANTES_REGISTRO.pdf',
            ['Attachment' => true]
        );
    }

    public function index()
    {
        $db = db_connect();

        // Empleados
        $empleados = $db->table('empleados')
            ->orderBy('parent_id', 'ASC')
            ->orderBy('tipo', 'DESC')
            ->get()
            ->getResultArray();

        // Software por empleado
        $softwareEmpleado = $db->table('empleado_software es')
            ->select('es.empleado_id, s.nombre, es.nivel_uso')
            ->join('software s', 's.id = es.software_id')
            ->orderBy('s.nombre', 'ASC')
            ->get()
            ->getResultArray();

        // Agrupar software por empleado
        $softwareAgrupado = [];
        foreach ($softwareEmpleado as $row) {
            $softwareAgrupado[$row['empleado_id']][] = [
                'nombre' => $row['nombre'],
                'nivel'  => $row['nivel_uso']
            ];
        }

        // Inyectar software a cada empleado
        foreach ($empleados as &$e) {
            $e['software'] = $softwareAgrupado[$e['id']] ?? [];
        }
        unset($e);

        $css = [
            'style' => 'style.css'
        ];
        // echo '<pre>';
        // print_r($empleados);
        // echo '</pre>';exit;
        return view('base/head', $css)
            . view('base/header')
            . view('empleados/index', [
                'empleados' => $empleados
            ])
            . view('base/footer');
    }

    public function exportarExcel()
    {
        $db = db_connect();

        $data = $db->query("
            SELECT 
                CONCAT(nombre, ' ', apellido_paterno, ' ', apellido_materno) AS empleado,
                cargo,
                area,
                edad,
                anos_laborando,
                grado_estudios,
                profesion,
                sexo,
                telefono,
                correo,
                descripcion_labores,
                created_at
            FROM empleados
            ORDER BY area, tipo DESC, nombre
        ")->getResultArray();

        // Limpiar buffer por seguridad
        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=empleados.csv');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // BOM para Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Encabezados
        fputcsv($output, [
            'EMPLEADO',
            'CARGO',
            'AREA',
            'EDAD',
            'AÑOS LABORANDO',
            'GRADO DE ESTUDIOS',
            'PROFESIÓN',
            'SEXO',
            'TELÉFONO',
            'CORREO',
            'DESCRIPCIÓN DE LABORES',
            'FECHA DE REGISTRO'
        ]);

        foreach ($data as $row) {
            fputcsv($output, [
                $row['empleado'],
                $row['cargo'],
                $row['area'],
                $row['edad'],
                $row['anos_laborando'],
                $row['grado_estudios'],
                $row['profesion'],
                $row['sexo'],
                $row['telefono'],
                $row['correo'],
                $row['descripcion_labores'],
                $row['created_at']
            ]);
        }

        fclose($output);
        exit;
    }

}