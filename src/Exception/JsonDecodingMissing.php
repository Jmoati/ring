<?php

declare(strict_types=1);

namespace Jmoati\Ring\Exception;

use Exception;
use Throwable;

class JsonDecodingMissing extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Please add json encoder to your serializer', $code, $previous);
    }
}
