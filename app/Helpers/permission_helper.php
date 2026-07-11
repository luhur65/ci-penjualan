<?php

if (!function_exists('has_permission')) {
  function has_permission(string $class, string $method): bool
  {
    $class = strtolower($class);
    $method = strtolower($method);

    // Daftar pengecualian (seperti pada kode Anda sebelumnya)
    $exceptMethods = [];
    if (in_array($method, $exceptMethods)) {
      return true;
    }

    // Bypass otorisasi khusus untuk modul testingmasterdetail agar CRUD bisa diakses tanpa login
    // if ($class === 'testingmasterdetail') {
    //   return true;
    // }

    // Ambil data dari Session, BUKAN dari database
    $permissions = session()->get('permissions') ?? [];
    $accessKey = $class . '.' . $method;

    return in_array($accessKey, $permissions);
  }
}
