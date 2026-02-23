<?php

namespace App\Services;

use CodeIgniter\Encryption\Encryption;

class EncryptionService
{
    protected $encrypter;

    public function __construct()
    {
        $this->encrypter = \Config\Services::encrypter();
    }

    public function encryptKey(string $plainKey): string
    {
        return base64_encode($this->encrypter->encrypt($plainKey));
    }

    public function decryptKey(string $encryptedKey): string
    {
        try {
            return $this->encrypter->decrypt(base64_decode($encryptedKey));
        } catch (\Throwable $e) {
            return '';
        }
    }

    public function maskKey(string $plainKey): string
    {
        if (strlen($plainKey) < 12) {
            return str_repeat('•', strlen($plainKey));
        }
        $visible = substr($plainKey, 0, 7);
        $last4   = substr($plainKey, -4);
        $masked  = str_repeat('•', 8);
        return $visible . $masked . $last4;
    }

    public function isValidOpenAIKey(string $key): bool
    {
        return (bool) preg_match('/^sk-[A-Za-z0-9\-_]{20,}$/', $key);
    }
}
