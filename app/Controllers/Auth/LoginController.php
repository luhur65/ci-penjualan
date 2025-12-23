<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
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
        $userModel = new User();
        $user = $userModel->where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Username tidak ditemukan');
        }

        if (password_verify($password, $user['password'])) {

            $response = $client->post($this->apiConfig->apiURL . '/token', [
                'form_params' => [
                    'username' => $username,
                    'password' => $password,
                ]
            ]);

            $body = json_decode($response->getBody());

            // Simpan Token dari API ke SESSION Frontend
            session()->set([
                'user_data'     => [
                    'id'       => $user['id'],
                    'fullname' => $user['fullname'],
                    'email'    => $user['email'],
                    'username' => $user['username'],
                ],
                'isLoggedIn'    => true,
                'accessToken'   => $body->access_token,
                'refreshToken'  => $body->refresh_token,
                'token_expires_at' => time() + $body->expires_in,
            ]);

            return redirect()->to('/dashboard')->with('success', 'Login berhasil!');

        } else {

            return redirect()->back()->withInput()->with('error', 'Password salah');
        }
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
            // Jika kosong, berarti Session CI4 sudah expired (hilang).
            // Tidak perlu tanya ke API Backend, percuma.
            // Langsung bunuh session dan kirim sinyal 400 ke Frontend.
            session()->destroy();
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Sesi sudah habis, silakan login kembali.',
            ]);
        }

        $client = new ApiClient();
        $response = $client->post('/refresh-token', [
            'refresh_token' => session()->get('refreshToken')
        ]);

        // Cek jika API menolak refresh token (misal refresh token juga sudah expired)
        if ($response->getStatusCode() !== 200) {
            // Hancurkan session lokal dan kirim sinyal 401 ke JS
            session()->destroy();
            return $this->response->setStatusCode(404)->setJSON([
                'access_token' => null,
                'refresh_token' => null,
                'expires_in' => null,
            ]);
        }

        $body = json_decode($response->getBody());

        session()->set([
            'accessToken'   => $body->access_token,
            'refreshToken'  => $body->refresh_token,
            'token_expires_at' => time() + $body->expires_in,
        ]);

        // PENTING: Return JSON ke Frontend (JS)
        return $this->response->setJSON([
            'access_token' => $body->access_token,
            'refresh_token' => $body->refresh_token,
            'expires_in' => $body->expires_in,
        ]);
        
    }
}
