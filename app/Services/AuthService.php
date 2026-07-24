<?php

namespace App\Services;

use App\Models\UserModel;

class AuthService
{
    /** Shared password for self-registered users, sent via access email */
    public const DEFAULT_PASSWORD = 'twin26bonus';

    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function isAuthenticated(): bool
    {
        return !empty(session()->get('user_id'));
    }

    public function getCurrentUserId(): int
    {
        return (int) session()->get('user_id');
    }

    public function getCurrentUser(): ?array
    {
        $userId = $this->getCurrentUserId();
        if (!$userId) return null;
        return $this->userModel->find($userId);
    }

    public function login(string $email, string $password): array
    {
        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return ['success' => false, 'error' => 'Invalid email or password.'];
        }

        if ($user['status'] === 'suspended') {
            return ['success' => false, 'error' => 'Your account has been suspended.'];
        }

        if ($user['status'] === 'inactive') {
            return ['success' => false, 'error' => 'Your account is inactive.'];
        }

        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Invalid email or password.'];
        }

        $this->userModel->updateLastLogin($user['id']);

        session()->set([
            'user_id'   => $user['id'],
            'user_name' => $user['name'],
            'user_email'=> $user['email'],
            'user_role' => $user['role'],
        ]);

        return ['success' => true, 'user' => $user];
    }

    public function register(array $data): array
    {
        $existing = $this->userModel->findByEmail($data['email']);
        if ($existing) {
            return ['success' => false, 'error' => 'This email is already registered.'];
        }

        $userId = $this->userModel->insert([
            'email'         => $data['email'],
            'password_hash' => password_hash(self::DEFAULT_PASSWORD, PASSWORD_BCRYPT),
            'name'          => $data['name'],
            'role'          => 'user',
            'status'        => 'active',
        ]);

        if (!$userId) {
            return ['success' => false, 'error' => 'Registration failed. Please try again.'];
        }

        // Create empty settings row for user
        $settingsModel = new \App\Models\UserSettingModel();
        $settingsModel->insert([
            'user_id'      => $userId,
            'openai_model' => 'gpt-4.1-nano',
        ]);

        $user = $this->userModel->find($userId);

        $this->sendAccessEmail($user);

        return ['success' => true, 'user' => $user];
    }

    protected function sendAccessEmail(array $user): void
    {
        $html = view('emails/access_email', [
            'email'    => $user['email'],
            'password' => self::DEFAULT_PASSWORD,
            'loginUrl' => base_url('login'),
        ]);

        try {
            (new MailgunService())->send(
                $user['email'],
                $user['name'],
                'TwinProfit HQ - Product Access Login Information',
                $html
            );
        } catch (\Throwable $e) {
            // email failure must not block registration
            log_message('error', 'Access email failed for ' . $user['email'] . ': ' . $e->getMessage());
        }
    }

    public function logout(): void
    {
        session()->destroy();
    }
}
