<?php

declare(strict_types=1);

namespace Jmoati\Ring\Model;

class Authentication
{
    public string $accessToken;
    public string $refreshToken;
    public string $tokenType;
}
