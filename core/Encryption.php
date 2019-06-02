<?php

/**
 * VCWeb Networks <https://www.vcwebnetworks.com.br/>
 *
 * @author    Vagner Cardoso <vagnercardosoweb@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @copyright 31/05/2019 Vagner Cardoso
 */

namespace Core {

    /**
     * Class Encryption
     *
     * @package Core
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
                throw new \InvalidArgumentException(
                    'Encryption empty key.', E_ERROR
                );
            }
        }

        /**
         * @param mixed $value
         * @param bool $serialize
         *
         * @return string|bool
         */
        public function encrypt($value, bool $serialize = true)
        {
            $ivlenght = openssl_cipher_iv_length($this->cipher);
            $iv = random_bytes($ivlenght);

            $value = \openssl_encrypt(
                $serialize ? serialize($value) : $value,
                $this->cipher, $this->key, 0, $iv
            );

            if ($value === false) {
                return false;
            }

            $mac = $this->hash($iv = base64_encode($iv), $value);
            $json = json_encode(compact('iv', 'value', 'mac'));

            if (json_last_error() !== JSON_ERROR_NONE) {
                return false;
            }

            return base64_encode($json);
        }

        /**
         * @param string $iv
         * @param mixed $value
         *
         * @return string
         */
        protected function hash(string $iv, string $value): string
        {
            return hash_hmac('sha256', $iv.$value, $this->key);
        }

        /**
         * @param string $payload
         * @param bool $unserialize
         *
         * @return string|bool
         */
        public function decrypt(string $payload, bool $unserialize = true)
        {
            $payload = $this->getJsonPayload($payload);
            $iv = base64_decode($payload['iv']);
            $decrypted = \openssl_decrypt(
                $payload['value'], $this->cipher, $this->key, 0, $iv
            );

            if ($decrypted === false) {
                return false;
            }

            return $unserialize
                ? unserialize($decrypted)
                : $decrypted;
        }

        /**
         * @param string $payload
         *
         * @return array|bool
         */
        protected function getJsonPayload(string $payload)
        {
            $payload = json_decode(base64_decode($payload), true);

            if (!$this->validPayload($payload)) {
                return false;
            }

            if (!$this->validMac($payload)) {
                return false;
            }

            return $payload;
        }

        /**
         * @param array $payload
         *
         * @return bool
         */
        protected function validPayload(array $payload)
        {
            return is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac']) &&
                strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length($this->cipher);
        }

        /**
         * @param array $payload
         *
         * @return bool
         */
        protected function validMac(array $payload)
        {
            $bytes = random_bytes(16);
            $calculated = $this->calculateMac($payload, $bytes);

            return hash_equals(
                hash_hmac('sha256', $payload['mac'], $bytes, true),
                $calculated
            );
        }

        /**
         * @param array $payload
         * @param string $bytes
         *
         * @return string
         */
        protected function calculateMac(array $payload, $bytes)
        {
            return hash_hmac(
                'sha256', $this->hash($payload['iv'], $payload['value']), $bytes, true
            );
        }
    }
}
