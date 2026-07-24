<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Mailgun extends BaseConfig
{
    /** set via .env (mailgun.*); key and domain base64-encoded */
    public string $key       = '';
    public string $domain    = '';
    public string $fromName  = '';
    public string $fromEmail = '';
}
