<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('crear-admin', function() {

    $db = \Config\Database::connect();

    $db->table('users')->insert([
        'nombre' => 'Admin',
        'ap_paterno' => 'Sistema',
        'ap_materno' => 'Principal',
        'rol_id' => 1,
        'estado' => 1
    ]);

    $user_id = $db->insertID();

    $db->table('accesos')->insert([
        'user_id' => $user_id,
        'username' => 'admin',
        'password' => password_hash('Admin123*', PASSWORD_DEFAULT),
        'estado' => 1
    ]);

    return "Administrador creado correctamente";
});


// PÚBLICAS (tu formulario)
$routes->get('/', 'EmpleadosController::create');
$routes->get('empleados/registro', 'EmpleadosController::create');
$routes->post('empleados/store', 'EmpleadosController::store');

// LOGIN
$routes->get('login', 'AuthController::login');
$routes->post('login/attempt', 'AuthController::attempt');
$routes->get('logout', 'AuthController::logout');

// ADMIN + SISTEMA PRIVADO
$routes->group('', ['filter' => 'auth'], function($routes) {

    // Dashboard
    $routes->get('admin/dashboard', 'AdminController::dashboard');

    // Usuarios
    $routes->get('admin/usuarios', 'UsuariosController::index');
    $routes->get('admin/usuarios/create', 'UsuariosController::create');
    $routes->post('admin/usuarios/store', 'UsuariosController::store');
    $routes->post('admin/usuarios/reset-password', 'UsuariosController::resetPassword');
    $routes->get('admin/usuarios/permisos', 'UsuariosController::permisos');
    $routes->post('admin/usuarios/permisos', 'UsuariosController::updatePermisos');
    $routes->get('admin/usuarios/show', 'UsuariosController::show');
    $routes->post('admin/usuarios/update', 'UsuariosController::update');

    // Roles
    $routes->get('admin/roles', 'RolesController::index');
    $routes->get('admin/roles/create', 'RolesController::create');
    $routes->post('admin/roles/store', 'RolesController::store');
    $routes->get('admin/roles/show', 'RolesController::show');
    $routes->post('admin/roles/update', 'RolesController::update');
    $routes->post('admin/roles/delete', 'RolesController::delete');

    // Software
    $routes->get('admin/software', 'SoftwareController::index');
    $routes->get('admin/software/create', 'SoftwareController::create');
    $routes->post('admin/software/store', 'SoftwareController::store');
    $routes->get('admin/software/show', 'SoftwareController::show');
    $routes->post('admin/software/update', 'SoftwareController::update');
    $routes->post('admin/software/delete', 'SoftwareController::delete');
    $routes->get('admin/software/show', 'SoftwareController::show');
    $routes->post('admin/software/update', 'SoftwareController::update');

    // Areas
    $routes->get('admin/areas', 'AreaController::index');
    $routes->post('admin/areas/store', 'AreaController::store');
    $routes->get('admin/areas/show', 'AreaController::show');
    $routes->post('admin/areas/update', 'AreaController::update');
    $routes->post('admin/areas/delete', 'AreaController::delete');

    // Permisos
    $routes->get('admin/permisos', 'PermisosController::index');
    $routes->post('admin/permisos/store', 'PermisosController::store');
    $routes->get('admin/permisos/show', 'PermisosController::show');
    $routes->post('admin/permisos/update', 'PermisosController::update');
    $routes->post('admin/permisos/delete', 'PermisosController::delete');

    // Permisos a roles
    $routes->get('admin/roles/permisos', 'RolesPermisosController::index');
    $routes->post('admin/roles/permisos/asignar', 'RolesPermisosController::asignar');

    // TU SISTEMA (protegido por permisos)
    $routes->get('enlases', 'EmpleadosController::index', ['filter' => 'permiso:ver-enlaces']);
    $routes->get('empleados', 'EmpleadosController::index', ['filter' => 'permiso:ver-listado']);
    $routes->get('empleados/exportarExcel', 'EmpleadosController::exportarExcel', ['filter' => 'permiso:exportar-excel']);
    $routes->get('empleados/generarComprobantesPDF/(:num)', 'EmpleadosController::generarComprobantesPDF/$1', ['filter' => 'permiso:generar-pdf']);
});
