<?php

/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 9/06/2018
 * Time: 14:40
 */
class Crypto
{

    protected $keys = [];

    public function __construct($keys)
    {
        $this->keys = $keys;
    }

    public function GenKey($bits) {

        if ($bits % 8 != 0) {
            throw new Exception("Bitlength should be a multiple of 8");
        }
        $key = openssl_random_pseudo_bytes($bits / 8);
        return bin2hex($key);

    }

    public function Encrypt($data) {

        if (count($this->keys) == 0) {
            throw new RuntimeException("No keys loaded");
        }

        $key = end($this->keys);

        if (in_array($key['cipher'], openssl_get_cipher_methods()))
        {

            // generate random iv
            $ivlen = openssl_cipher_iv_length($key['cipher']);
            $iv = openssl_random_pseudo_bytes($ivlen);

            $ciphertext = openssl_encrypt($data, $key['cipher'], $key['key'], $options=0, $iv);

            return bin2hex(chr($key['id']) . $iv . $ciphertext);

        } else {

            throw new RuntimeException("Cipher not supported\n" . implode(" ", openssl_get_cipher_methods()));

        }

    }

    public function Decrypt($data) {

        $bin = hex2bin($data);

        $key_id = ord(substr($bin, 0, 1));

        $key = null;
        foreach ($this->keys as $k) {
            if ($k['id'] == $key_id) {
                $key = $k;
                break;
            }
        }

        if ($key === null) {
            throw new Exception("Specified key not found");
        }

        $iv_length = openssl_cipher_iv_length($key['cipher']);
        $iv = substr($bin, 1, $iv_length);
        $encrypted = substr($bin, 1 + $iv_length);

        return openssl_decrypt($encrypted, $key['cipher'], $key['key'], 0, $iv);

    }

}