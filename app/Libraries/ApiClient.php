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

  protected function request($method, $endpoint, $data = null, $token = null)
  {
    try {
      $options = [
        'headers' => [
          'Accept'       => 'application/json',
          // 'Content-Type' => 'application/json',
          'Accept-Encoding' => 'gzip',
        ],
      ];

      // If token is not explicitly provided, try to get it from the session
      if ($token === null && session()->has('accessToken')) {
        $token = session()->get('accessToken');
      }

      if ($token && strpos($endpoint, 'refresh-token') === false) {
        $options['headers']['Authorization'] = 'Bearer ' . $token;
      }

      if ($data !== null) {
        $options['form_params'] = $data;
      }

      $response = $this->client->$method($this->baseUri . $endpoint, $options);
      // \dd($this->baseUri . $endpoint, $options, $response->getBody());

      // RETURN RAW RESPONSE OBJECT
      return $response;
    } catch (\Throwable $th) {
      if (
        $th instanceof \CodeIgniter\HTTP\Exceptions\HTTPException
        && $th->getCode() === 401
      ) {

        // refresh token request
        // $newToken = $this->client->post($this->baseUri . "/refresh-token", [
        //   "form_params" => ["refresh_token" => session()->get('refreshToken')]
        // ]);
        $newToken = $this->client->post($this->baseUri . "/refresh-token", [
          'headers' => [
            'Accept' => 'application/json'
          ],
          'form_params' => [
            'refresh_token' => session()->get('refreshToken')
          ]
        ]);

        $json = json_decode($newToken->getBody(), true);
        session()->set([
          'accessToken'  => $json['access_token'],
          'refreshToken' => $json['refresh_token'],
        ]);

        // ulang request
        return $this->request($method, $endpoint, $data);
      }

      // if ($response->getStatusCode() === 401) {
      //   return $this->refreshTokenAndRetry($method, $endpoint, $data);
      // }

      throw $th;
    }
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

  protected function refreshTokenAndRetry($method, $endpoint, $data = null)
  {
    $refreshToken = session()->get('refreshToken');
    if (!$refreshToken) {
      throw new \Exception('Refresh token not found.');
    }

    $response = $this->client->post('/refresh-token', [
      'form_params' => ['refresh_token' => $refreshToken]
    ]);

    $json = json_decode($response->getBody(), true);

    session()->set([
      'accessToken'  => $json['access_token'] ?? '',
      'refreshToken' => $json['refresh_token'] ?? $refreshToken,
    ]);

    return $this->request($method, $endpoint, $data, session()->get('accessToken'));
  }
}
