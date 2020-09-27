<?php

declare(strict_types=1);

namespace Jmoati\Ring\Exception;

use Exception;
use Jmoati\Ring\Serializer\DoorbotNormalizer;
use Throwable;

class DoorbotNormalizerMissing extends Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Please add '.DoorbotNormalizer::class.' to your serializer', $code, $previous);
    }
}
