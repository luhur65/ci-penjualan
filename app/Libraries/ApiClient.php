<?php

namespace App\Libraries;

class ApiClient
{
  protected $client;
  protected $baseUri;

  public function __construct()
  {
    $this->client = \Config\Services::curlrequest();
    $this->baseUri = config('Api')->apiURL;
  }

  // Tambahkan parameter $isRetry untuk mendeteksi apakah ini percobaan kedua
  protected function request($method, $endpoint, $data = null, $token = null, $isRetry = false)
  {
    $options = [
      'http_errors' => false, // Kunci Utama: Cegah cURL melempar Exception pada error 4xx/5xx
      'headers' => [
        'Accept'          => 'application/json',
        'Accept-Encoding' => 'gzip',
      ],
    ];

    if ($token === null && session()->has('accessToken')) {
      $token = session()->get('accessToken');
    }

    if ($token && strpos($endpoint, 'refresh-token') === false) {
      $options['headers']['Authorization'] = 'Bearer ' . $token;
    }

    if ($data !== null) {
      $options['form_params'] = $data;
    }

    try {
      $response = $this->client->$method($this->baseUri . $endpoint, $options);

      // Jika Backend membalas 401 Unauthorized dan ini BUKAN ulangan request
      if ($response->getStatusCode() === 401 && !$isRetry && session()->has('refreshToken')) {
        return $this->refreshTokenAndRetry($method, $endpoint, $data);
      }

      return $response;
    } catch (\Throwable $th) {
      // Catch ini sekarang murni hanya menangkap error jaringan (misal server BE mati/RTO)
      throw $th;
    }
  }

  private function refreshTokenAndRetry($method, $endpoint, $data)
  {
    // 1. Eksekusi permintaan token baru
    $response = $this->client->post($this->baseUri . '/refresh-token', [
      'http_errors' => false,
      'headers'     => ['Accept' => 'application/json'],
      'form_params' => [
        'refresh_token' => session()->get('refreshToken')
      ]
    ]);

    // 2. Jika Backend mengesahkan refresh token
    if ($response->getStatusCode() === 200) {
      $json = json_decode($response->getBody(), true);

      // Sesuaikan pembacaan key JSON ini dengan format kembalian Backend Anda
      $newAccessToken = $json['access_token'] ?? $json['data']['access_token'] ?? null;
      $newRefreshToken = $json['refresh_token'] ?? $json['data']['refresh_token'] ?? null;

      if ($newAccessToken) {
        session()->set([
          'accessToken'  => $newAccessToken,
          'refreshToken' => $newRefreshToken,
        ]);

        // 3. Ulangi request asli dengan flag $isRetry = true
        return $this->request($method, $endpoint, $data, $newAccessToken, true);
      }
    }

    // 4. Jika refresh token juga gagal/expired, hancurkan sesi dan paksa login
    session()->remove(['accessToken', 'refreshToken']);
    throw new \Exception("Sesi kedaluwarsa sepenuhnya. Silakan login kembali.", 401);
  }

  public function get($endpoint, $token = null)
  {
    return $this->request('get', $endpoint, null, $token);
  }

  public function post($endpoint, $data = [], $token = null)
  {
    return $this->request('post', $endpoint, $data, $token);
  }

  public function put($endpoint, $data = [], $token = null)
  {
    return $this->request('put', $endpoint, $data, $token);
  }

  public function patch($endpoint, $data = [], $token = null)
  {
    return $this->request('patch', $endpoint, $data, $token);
  }

  public function delete($endpoint, $token = null)
  {
    return $this->request('delete', $endpoint, null, $token);
  }
}
