<?php

namespace App\Services;

use Config\Mailgun as MailgunConfig;

class MailgunService
{
    protected MailgunConfig $config;

    public function __construct()
    {
        $this->config = config('Mailgun');
    }

    public function send(string $toEmail, string $toName, string $subject, string $html): bool
    {
        $key    = base64_decode($this->config->key);
        $domain = base64_decode($this->config->domain);
        $from   = $this->config->fromName . ' <' . $this->config->fromEmail . '>';
        $to     = $toName . ' <' . $toEmail . '>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:' . $key);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_URL, 'https://api.mailgun.net/v3/' . $domain . '/messages');
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'from'    => $from,
            'to'      => $to,
            'subject' => $subject,
            'html'    => $html,
        ]);

        $result    = curl_exec($ch);
        $httpCode  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        if ($result === false || $httpCode >= 400) {
            log_message('error', 'Mailgun send failed (HTTP ' . $httpCode . '): ' . ($result !== false ? $result : $curlError));
            return false;
        }

        return true;
    }
}
