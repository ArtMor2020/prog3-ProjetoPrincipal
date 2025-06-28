<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use App\Filters\AuthFilter;

class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf'     => CSRF::class,
        'toolbar'  => DebugToolbar::class,
        'honeypot' => Honeypot::class,
        'invalidchars' => \CodeIgniter\Filters\InvalidChars::class,
        'secureheaders' => \CodeIgniter\Filters\SecureHeaders::class,
        'auth'     => AuthFilter::class,
    ];
}