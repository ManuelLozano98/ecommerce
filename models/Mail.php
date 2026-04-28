<?php

namespace App\Models;

use App\Config\Env;
use PHPMailer\PHPMailer\PHPMailer;

class Mail
{

    public static function createMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = Env::get("MAIL_HOST");
        $mail->SMTPAuth = true;
        $mail->Username = Env::get("MAIL_USERNAME");
        $mail->Password = Env::get("MAIL_PASSWORD");
        $mail->SMTPSecure = Env::get("MAIL_SMTPSECURE");
        $mail->Port = Env::get("MAIL_PORT");
        $mail->setFrom(Env::get("MAIL_USERNAME"), Env::get("MAIL_NAME"));
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        return $mail;
    }

    public static function sendEmail(PHPMailer $mail)
    {
        return $mail->send();
    }
}
