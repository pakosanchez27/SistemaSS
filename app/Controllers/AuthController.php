<?php

namespace App\Controllers;

use App\Models\AccesosModel;

class AuthController extends BaseController
{
    public function login()
    {
        $css = [
            'style' => 'logged.css'
        ];

        return view('base/head', $css)
            . view('base/header')
            . view('auth/login')
            . view('base/footer');
    }

    public function attempt()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $model = new AccesosModel();
        $user = $model->where('username', $username)
            ->where('estado', 1)
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Usuario no encontrado');
        }


        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Contraseña incorrecta');
        }

    
        session()->set([
            'user_id' => $user['user_id'],
            'logged_in' => true
        ]);

        $model->update($user['id'], [
            'last_login' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/empleados');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
