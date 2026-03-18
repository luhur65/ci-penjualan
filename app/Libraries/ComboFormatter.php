<?php

namespace App\Libraries;

class ComboFormatter
{

  public static function combo(array $data, bool $usingAll = true)
  {
    $result = [];

    foreach ($data as $row) {

      $result[] = $row['param'] . ':' . $row['parameter'];
    }

    if ($usingAll) {
      array_unshift($result, ':ALL'); // 0:ALL
    }

    return implode(';', $result);
  }


  public static function first(array $data)
  {
    return $data[0]['param'] . ':' . $data[0]['parameter'] ?? '';
  }


  public static function default(array $data)
  {
    foreach ($data as $row) {

      if (($row['default'] ?? '') === 'YA') {

        return $row['param'] . ':' . $row['parameter'];
      }
    }

    return '';
  }
}
