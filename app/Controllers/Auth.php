<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Models\AuditLogModel;

class Auth extends BaseController
{
    protected AuthService $authService;
    protected AuditLogModel $auditLog;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->auditLog    = new AuditLogModel();
    }

    public function login(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($this->authService->isAuthenticated()) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login', [
            'title' => 'Sign In — TwinProfit HQ',
        ]);
    }

    public function attemptLogin(): \CodeIgniter\HTTP\RedirectResponse
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $result = $this->authService->login(
            $this->request->getPost('email'),
            $this->request->getPost('password')
        );

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['error']);
        }

        $this->auditLog->log($result['user']['id'], 'auth.login');

        return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $result['user']['name'] . '!');
    }

    public function register(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($this->authService->isAuthenticated()) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register', [
            'title' => 'Create Account — TwinProfit HQ',
        ]);
    }

    public function attemptRegister(): \CodeIgniter\HTTP\RedirectResponse
    {
        $rules = [
            'name'             => 'required|min_length[2]|max_length[100]',
            'email'            => 'required|valid_email',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'password_confirm' => ['matches' => 'Passwords do not match.'],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $result = $this->authService->register([
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
        ]);

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['error']);
        }

        $this->auditLog->log($result['user']['id'], 'auth.register');

        return redirect()->to('/dashboard')->with('success', 'Welcome to TwinProfit HQ, ' . $result['user']['name'] . '!');
    }

    public function logout(): \CodeIgniter\HTTP\RedirectResponse
    {
        $userId = session()->get('user_id');
        if ($userId) {
            $this->auditLog->log($userId, 'auth.logout');
        }
        $this->authService->logout();
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }
}
