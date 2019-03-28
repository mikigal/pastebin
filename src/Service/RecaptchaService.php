<?php

namespace App\Service;

class RecaptchaService {
    public function checkCaptcha(string $response) {
        $request = file_get_contents( "https://www.google.com/recaptcha/api/siteverify?secret=" . getenv("RECAPTCHA_SECRET") . "&response=$response");
        return json_decode($request)->success;
    }
}