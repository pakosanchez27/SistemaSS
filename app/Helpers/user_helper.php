<?php

if (!function_exists('current_user_id')) {
    function current_user_id(): ?int
    {
        $id = session()->get('user_id');
        return $id ? (int) $id : null;
    }
}

if (!function_exists('current_user')) {
    function current_user(): ?array
    {
        $userId = current_user_id();
        if (!$userId) {
            return null;
        }

        $db = \Config\Database::connect();
        return $db->table('users u')
            ->select('u.*, ac.username as correo')
            ->join('accesos ac', 'ac.user_id = u.id', 'left')
            ->where('u.id', $userId)
            ->get()
            ->getRowArray();
    }
}
