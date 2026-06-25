<?php

/**
 * Cek apakah user sedang login
 */
function has_role($role_name)
{
    // Kita harus ambil nama rolenya dari session (nanti diset saat login)
    return session()->get('role_name') === $role_name;
}

function can($permission_name)
{
    // Cek apakah nama permission ada di dalam array session permissions
    $perms = session()->get('permissions') ?? [];
    return in_array($permission_name, $perms);
}
