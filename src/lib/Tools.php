<?php

/**
 * File Name: Tools.php
 * Created by Jacobs <jacobs@anviz.com>.
 * Date: 2016-3-22
 * Time: 9:34
 * Description:
 */

class Tools
{

    public static function uuid($prefix = '')
    {
        $chars = md5(uniqid(mt_rand(), true));

        $uuid = substr($chars, 0, 8) . $prefix;
        $uuid .= substr($chars, 8, 4) . $prefix;
        $uuid .= substr($chars, 12, 4) . $prefix;
        $uuid .= substr($chars, 16, 4) . $prefix;
        $uuid .= substr($chars, 20, 12);

        return $uuid;
    }

    public static function randomkey($length = 8, $type = 0)
    {
        $charType = array(
            '01234567890',
            'abcdefghijklmnopqrstuvwxyz',
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
            '01234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
            '01234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz~!@#$%^&*()_+-|][}{'
        );

        $type = (int)$type;

        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $charType[$type][mt_rand(0, strlen($charType[$type]) - 1)];
        }

        return $string;
    }

    public static function createCommandId()
    {
        $id = '';
        $random = md5(time() . mt_rand(1, 99999999));
        for ($j = 0; $j < 8; $j++) {
            $b = mt_rand(0, strlen($random) - 1);
            if ($random[$b] == "0") {
                $id .= chr(rand(97, 102));
            } elseif (ord($random[$b]) > 102) {
                $id .= chr(rand(97, 102));
            } else {
                $id .= $random[$b];
            }
        }
        return $id;
    }

    /**
     * @Created by Jacobs <jacobs@anviz.com>
     * @Name: encrypt3DES
     * @param $string
     * @param $key
     * @return bool|string
     * @Description:
     */
    public static function encrypt3DES($string, $key)
    {
        if (empty($key))
            return false;

        $key = str_pad($key, 24, "\0", STR_PAD_RIGHT);

        $cipher_alg = MCRYPT_TRIPLEDES;
        $iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher_alg, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $encrypted_string = mcrypt_encrypt($cipher_alg, $key, $string, MCRYPT_MODE_ECB, $iv);

        return $encrypted_string;

    }

    /**
     * @Created by Jacobs <jacobs@anviz.com>
     * @Name: decrypt3DES
     * @param $string
     * @param $key
     * @return bool|string
     * @Description:
     */
    public static function decrypt3DES($string, $key)
    {
        if (empty($key))
            return false;

        $key = str_pad($key, 24, "\0", STR_PAD_RIGHT);

        $cipher_alg = MCRYPT_TRIPLEDES;
        $iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher_alg, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $decrypted_string = mcrypt_decrypt($cipher_alg, $key, $string, MCRYPT_MODE_ECB, $iv);
        return trim($decrypted_string);
    }

    /**
     * @Created by Jacobs <jacobs@anviz.com>
     * @Name: uni2utf8
     * @param $c
     * @return string
     * @Description:
     */
    public static function uni2utf8($c)
    {
        if ($c < 0x80) {
            $utf8char = chr($c);
        } else if ($c < 0x800) {
            $utf8char = chr(0xC0 | $c >> 0x06) . chr(0x80 | $c & 0x3F);
        } else if ($c < 0x10000) {
            $utf8char = chr(0xE0 | $c >> 0x0C) . chr(0x80 | $c >> 0x06 & 0x3F) . chr(0x80 | $c & 0x3F);
        } else {
            $utf8char = "&#{$c};";
        }

        return $utf8char;
    }

    /**
     * @Created by Jacobs <jacobs@anviz.com>
     * @Name: utf82u
     * @param $c
     * @return int
     * @Description:
     */
    public static function utf82u($c)
    {
        switch (strlen($c)) {
            case 1:
                return ord($c);
            case 2:
                $n = (ord($c[0]) & 0x3F) << 6;
                $n += ord($c[1]) & 0x3F;
                return $n;
            case 3:
                $n = (ord($c[0]) & 0x1F) << 12;
                $n += (ord($c[1]) & 0x3F) << 6;
                $n += ord($c[2]) & 0x3F;
                return $n;
            case 4:
                $n = (ord($c[0]) & 0x0F) << 18;
                $n += (ord($c[1]) & 0x3F) << 12;
                $n += (ord($c[2]) & 0x3F) << 6;
                $n += ord($c[3]) & 0x3F;
                return $n;
        }
    }

    /**
     * @Created by Jacobs <jacobs@anviz.com>
     * @Name: utf82uni
     * @param $str
     * @return string
     * @Description:
     */
    public static function utf82uni($str)
    {
        $length = strlen($str);
        $new_string = '';
        for ($i = 0; $i < $length; $i++) {
            $a = ord(substr($str, $i, 1));
            if (($a & 0xE0) == 224) {
                $b = 1;
                $new_string .= pack('v', self::utf82u(substr($str, $i, 3)));
                $i = $i + 2;
            } elseif (($a & 0xC0) == 192) {
                $b = 2;
                $new_string .= pack('v', self::utf82u(substr($str, $i, 2)));
                $i = $i + 1;
            } else {
                $b = 3;
                $new_string .= pack('v', ord(substr($str, $i, 1)));
            }
        }

        return $new_string;
    }

    /**
     * @Created by Jacobs <jacobs@anviz.com>
     * @Name: R
     * @param string $data
     * @return string
     * @Description:
     */
    public static function R($data = '')
    {
        if (is_array($data) || is_object($data))
            $data = json_encode($data);

        return base64_encode($data);
    }
}