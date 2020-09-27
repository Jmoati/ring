<?php

declare(strict_types=1);

namespace Jmoati\Ring\Serializer;

use Jmoati\Ring\Model\Doorbot;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class DoorbotNormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        return
            !array_key_exists(__CLASS__, $context[__CLASS__])
            && Doorbot::class.'[]' === $type
            && array_key_exists('doorbots', $data);
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $context[__CLASS__] = true;
        $result = [];

        foreach ($data['doorbots'] as $doorbots) {
            $result[] = $this->denormalizer->denormalize($doorbots, Doorbot::class, $format);
        }

        return $result;
    }
}
