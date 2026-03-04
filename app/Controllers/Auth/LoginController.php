<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Libraries\ApiClient;

class LoginController extends BaseController
{
    protected $apiConfig;

    public function __construct()
    {
        $this->apiConfig = new \Config\Api();
    }

    /**
     * Display login form
     */
    public function index()
    {
        // Safety check: Clear session if state is inconsistent (isLoggedIn true but user null)
        // This fixes the redirect loop issue caused by previous session structure
        if (session()->get('isLoggedIn') && !session()->get('user_data')) {
            session()->destroy();
            return redirect()->to('/login');
        }

        // Redirect to dashboard if already logged in
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    /**
     * Process login authentication
     */
    public function authenticate()
    {
        // $session = session();
        $client = \Config\Services::curlrequest();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Validation rules
        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', 'Username dan password harus diisi');
        }

        // TODO: Implement user authentication logic here
        // $userModel = new User();
        // $user = $userModel->where('username', $username)->first();

        // if (!$user) {
        //     return redirect()->back()->withInput()->with('error', 'Username tidak ditemukan');
        // }

        // if (password_verify($password, $user['password'])) {
        $response = $client->post($this->apiConfig->apiURL . '/token', [
            'form_params' => [
                'username' => $username,
                'password' => $password,
            ],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'http_errors' => false, // Jangan lempar exception untuk status code 4xx/5xx, biar bisa kita tangani manual
        ]);

        // Cek status code dan body response dari API
        $statusCode = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);

        // Cek jika API mengembalikan error (misal 401 Unauthorized)
        if ($statusCode !== 200) {
            return redirect()->back()
                ->withInput()
                ->with('error', $body['messages']['error'] ?? 'Login gagal, silakan coba lagi.');
        }

        // Simpan Token dari API ke SESSION Frontend
        session()->set([
            'user_data' => [
                'id' => $body['user']['id'],
                'username' => $body['user']['username'],
                'fullname' => $body['user']['fullname'],
                'email' => $body['user']['email'],
                'roles' => $body['user']['roles'],
            ], // Simpan data user yang dikirim API (jika ada)
            // 'menu' => $body['menu'], // Simpan menu yang dikirim API (jika ada)
            'menu' => $this->replaceUrlDomain($body['menu']), // Ganti domain menu dari API ke domain sekarang;
            'isLoggedIn'    => true,
            'accessToken'   => $body['access_token'],
            'refreshToken'  => $body['refresh_token'],
            'token_expires_at' => time() + $body['expires_in'],
            'permissions' => $body['permissions'],
        ]);

        // return redirect()->to('/dashboard')->with('success', 'Login berhasil!');
        return redirect()->to('/dashboard');

        // } else {

        //     return redirect()->back()->withInput()->with('error', 'Password salah');
        // }
    }

    /**
     * Logout user
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Logout berhasil');
    }

    /*
     * Refresh token
     */
    public function refreshToken()
    {
        $refreshToken = session()->get('refreshToken');

        if (!$refreshToken) {
            session()->destroy();
            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Sesi sudah habis, silakan login kembali.',
            ]);
        }

        try {
            $client = new ApiClient();
            $response = $client->post('/refresh-token', [
                'refresh_token' => $refreshToken
            ]);

            $body = json_decode($response->getBody());

            session()->set([
                'accessToken'      => $body->access_token,
                'refreshToken'     => $body->refresh_token,
                'token_expires_at' => time() + $body->expires_in,
            ]);

            return $this->response->setJSON([
                'access_token'  => $body->access_token,
                'refresh_token' => $body->refresh_token,
                'expires_in'    => $body->expires_in,
            ]);
        } catch (\Throwable $e) {

            // LOG biar kelihatan di writable/logs
            log_message('error', 'Refresh FE gagal: ' . $e->getMessage());

            session()->destroy();

            return $this->response->setStatusCode(401)->setJSON([
                'status' => 'error',
                'message' => 'Refresh token tidak valid atau sudah expired'
            ]);
        }
    }

    public function resetPasswordForm()
    {
        // Get the token and user parameters from the query string
        $token = $this->request->getGet('token');
        $user = $this->request->getGet('user');

        // You can now use the $token and $user values, for example, to check if the token is valid
        if ($token && $user) {
            // Do something with the token and user, like checking if the token is valid and exists
            return view('auth/reset-password', ['token' => $token, 'user' => $user]);
        } else {
            // Handle the case when parameters are missing
            return redirect()->to('/error')->with('error', 'Invalid reset link.');
        }
    }

    private function replaceUrlDomain($menu)
    {
        $new_domain = current_url(true)->getScheme() . '://' . current_url(true)->getHost();

        // Ganti localhost menjadi domain sekarang
        return str_replace(['http://localhost', 'https://localhost'], $new_domain, $menu);
    }
}
