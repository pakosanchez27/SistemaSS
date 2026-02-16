<?php

namespace App\Controllers;

class AdminController extends BaseController
{
    public function dashboard()
    {
        $css = [
            'style' => 'admin.css'
        ];

        return view('base/head', $css)
            . view('base/header')
            . view('admin/dashboard')
            . view('base/footer');
    }
}
