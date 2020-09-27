<?php

declare(strict_types=1);

namespace Jmoati\Ring\Exception;

use Exception;
use Throwable;

class ThumbnailNotFound extends Exception
{
    public function __construct(int $doorbotId, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('The thumbnail for %d was not found', $doorbotId), $code, $previous);
    }
}
