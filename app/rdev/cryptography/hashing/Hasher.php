<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a base cryptographic hasher
 */
namespace RDev\Cryptography\Hashing;
use RuntimeException;
use RDev\Cryptography\Utilities\Strings;

abstract class Hasher implements IHasher
{
    /** @var int The hash algorithm constant used by this hasher */
    protected $hashAlgorithm = -1;
    /** @var Strings The string utility */
    private $strings = null;

    /**
     * @param Strings $strings The string utility
     */
    public function __construct(Strings $strings)
    {
        $this->setHashAlgorithm();
        $this->strings = $strings;
    }

    /**
     * {@inheritdoc}
     */
    public static function verify($hashedValue, $unhashedValue, $pepper = "")
    {
        return password_verify($unhashedValue . $pepper, $hashedValue);
    }

    /**
     * {@inheritdoc}
     */
    public function generate($unhashedValue, array $options = [], $pepper = "")
    {
        $hashedValue = password_hash($unhashedValue . $pepper, $this->hashAlgorithm, $options);

        if($hashedValue === false)
        {
            throw new RuntimeException("Failed to generate hash for algorithm {$this->hashAlgorithm}");
        }

        return $hashedValue;
    }

    /**
     * {@inheritdoc}
     */
    public function needsRehash($hashedValue, array $options = [])
    {
        return password_needs_rehash($hashedValue, $this->hashAlgorithm, $options);
    }

    /**
     * Should set the hash algorithm property to the algorithm used by the concrete class
     */
    abstract protected function setHashAlgorithm();
} 