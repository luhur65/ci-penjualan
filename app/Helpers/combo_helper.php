<?php

use App\Libraries\ApiClient;
use App\Libraries\ComboFormatter;

if (!function_exists('fetch_combo_status')) {

  function fetch_combo_status(string $grp, string $subgrp = '')
  {
    $cache = \Config\Services::cache();
    $cacheKey = 'fe_combo_' . md5($grp . $subgrp);

    if ($cached = $cache->get($cacheKey)) {
      return $cached;
    }

    $client = new ApiClient();

    $apiUrl = '/parameter/combo?grp=' . rawurlencode($grp)
      . '&subgrp=' . rawurlencode($subgrp);

    try {

      $response = $client->get($apiUrl);

      if ($response->getStatusCode() !== 200) {
        return [];
      }

      $data = json_decode($response->getBody(), true) ?: [];
    } catch (\Exception $e) {

      log_message('error', "fetch_combo_status: " . $e->getMessage());
      return [];
    }

    // simpan array ke cache
    $cache->save($cacheKey, $data, 86400);

    return $data;
  }
}


if (!function_exists('combo_status')) {

  function combo_status(string $grp, string $subgrp = '')
  {
    $data = fetch_combo_status($grp, $subgrp);

    return ComboFormatter::combo($data);
  }
}


if (!function_exists('combo_status_default')) {

  function combo_status_default(string $grp, string $subgrp = '')
  {
    $data = fetch_combo_status($grp, $subgrp);

    return ComboFormatter::default($data);
  }
}


if (!function_exists('combo_status_first')) {

  function combo_status_first(string $grp, string $subgrp = '')
  {
    $data = fetch_combo_status($grp, $subgrp);

    return ComboFormatter::first($data);
  }
}
