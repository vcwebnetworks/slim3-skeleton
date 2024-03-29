<?php

/*
 * VCWeb Networks <https://www.vcwebnetworks.com.br/>
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @link https://github.com/vagnercardosoweb
 * @license http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 08/06/2021 Vagner Cardoso
 */

namespace Core;

use InvalidArgumentException;
use function openssl_decrypt;
use function openssl_encrypt;

/**
 * Class Encryption.
 *
 * @author Vagner Cardoso <vagnercardosoweb@gmail.com>
 */
class Encryption
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $cipher;

    /**
     * @param string $key
     * @param string $cipher
     */
    public function __construct(string $key, string $cipher = 'AES-256-CBC')
    {
        $this->key = $key;
        $this->cipher = $cipher;

        if (empty($this->key)) {
            throw new InvalidArgumentException(
                'Encryption empty key.'
            );
        }
    }

    /**
     * @param mixed $value
     * @param bool  $serialize
     *
     * @return string|null
     */
    public function encrypt($value, bool $serialize = true): ?string
    {
        $iv = $this->generateRandomBytes(openssl_cipher_iv_length($this->cipher));

        $value = openssl_encrypt(
            $serialize ? serialize($value) : $value,
            $this->cipher, $this->key, 0, $iv
        );

        if (false === $value) {
            return null;
        }

        $mac = $this->hash($iv = base64_encode($iv), $value);
        $json = json_encode(compact('iv', 'value', 'mac'));

        if (JSON_ERROR_NONE !== json_last_error()) {
            return null;
        }

        return base64_encode($json);
    }

    /**
     * @param int $length
     *
     * @throws \Exception
     *
     * @return string
     */
    protected function generateRandomBytes($length = 16)
    {
        if (function_exists('random_bytes')) {
            $bytes = random_bytes($length);
        } else {
            $bytes = openssl_random_pseudo_bytes($length);
        }

        return $bytes;
    }

    /**
     * @param string $iv
     * @param mixed  $value
     *
     * @return string
     */
    protected function hash(string $iv, string $value): string
    {
        return hash_hmac('sha256', $iv.$value, $this->key);
    }

    /**
     * @param string $payload
     * @param bool   $unserialize
     *
     * @return mixed|null
     */
    public function decrypt($payload, bool $unserialize = true)
    {
        $payload = $this->getJsonPayload($payload);

        if (empty($payload['iv'])) {
            return null;
        }

        $iv = base64_decode($payload['iv']);
        $decrypted = openssl_decrypt($payload['value'], $this->cipher, $this->key, 0, $iv);

        if (false === $decrypted) {
            return null;
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    /**
     * @param string $payload
     *
     * @return array|null
     */
    protected function getJsonPayload($payload): ?array
    {
        $payload = json_decode(base64_decode($payload), true);

        if (!$this->validPayload($payload)) {
            return null;
        }

        if (!$this->validMac($payload)) {
            return null;
        }

        return $payload;
    }

    /**
     * @param mixed $payload
     *
     * @return bool
     */
    protected function validPayload($payload): bool
    {
        return is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac'])
            && strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length($this->cipher);
    }

    /**
     * @param array $payload
     *
     * @return bool
     */
    protected function validMac(array $payload): bool
    {
        $bytes = $this->generateRandomBytes(16);
        $calculated = $this->calculateMac($payload, $bytes);

        return hash_equals(
            hash_hmac('sha256', $payload['mac'], $bytes, true),
            $calculated
        );
    }

    /**
     * @param array  $payload
     * @param string $bytes
     *
     * @return string
     */
    protected function calculateMac(array $payload, $bytes): string
    {
        return hash_hmac(
            'sha256',
            $this->hash($payload['iv'], $payload['value']),
            $bytes,
            true
        );
    }
}
