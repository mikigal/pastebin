<?php

namespace App\Utils;

class Utils {

    private static $alphabet = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    public static function getRandomString(string $length) {
        $result = "";
        while (strlen($result) < $length)
            $result .= Utils::$alphabet[rand(1, strlen(Utils::$alphabet) - 1)];

        return $result;
    }
}