<?php

namespace App\Services;


use App\Models\Mail;
use Exception;
use App\Config\Env;


class MailService
{

    public function createOrderMailer($email, $saleDetails)
    {

        try {
            $mail = Mail::createMailer();
            $mail->addAddress($email);
            $mail->Subject = 'Thanks you for the order!';
            $msg = <<<HTML
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto;">
    <h2>Thank you for the order</h2>
    <p>Find all the details of this purchase in the "My Orders" section.</p>
HTML;

            foreach ($saleDetails->getItems() as $item) {
                $image = $item['image'];
                echo $image;
                $cid = 'img_' . md5($item['name']);
                echo $cid;
                $name = $item['name'];
                $price = $item['price'];
                $qty = $item['quantity'];

                $msg .= <<<HTML
    <div class="d-flex align-items-center gap-2" style="margin-bottom:10px;">
        <img src="cid:{$cid}"
             alt="{$name}"
             class="rounded"
             style="width: 300px; height: 400px;">
        
        <div class="flex-grow-1 overflow-hidden">
            <div class="fw-semibold text-truncate">
                {$name}
            </div>
        </div>

        <small class="text-muted">
            {$qty} x {$price}€
        </small>
    </div>
HTML;
                $mail->addEmbeddedImage($image, $cid);
            }

            $total = $saleDetails->getTotalAmount();
            $date = $saleDetails->getCreatedAt();

            $msg .= <<<HTML
    <hr/>
    <p>Total: {$total}€</p>
    <p>Order date: {$date}</p>
</div>
HTML;

            $mail->Body = $msg;
            return Mail::sendEmail($mail);
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }

    public function createWelcomeEmail($email, $username, $token)
    {

        $verifyUrl = Env::get("APP_URL") . "email/$token";
        try {
            $mail = Mail::createMailer();
            $mail->addAddress($email, $username);
            $mail->Subject = 'Verify Your Email Address';
            $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto;">
                <h2>Hello ' . htmlspecialchars($username) . ',</h2>
                <p>Thank you for registering. Please click the link below to verify your email address:</p>
                <p><a href="' . $verifyUrl . '" style="padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;">Verify Email</a></p>
                <p>If the button doesn\'t work, copy and paste this URL into your browser:</p>
                <p>' . $verifyUrl . '</p>
                <p>If you didn\'t register on our platform, please ignore this email.</p>
                <p>For security reasons, this link is valid for 24 hours only.</p>
            </div>';
            $mail->AltBody = "Hello $username,\nPlease verify your email: $verifyUrl" . " If you didn't register on our platform, please ignore this email.";
            return Mail::sendEmail($mail);
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }

    public function changeEmail($email, $username, $token)
    {

        $url = Env::get("APP_URL") . "email/confirm/$token";
        try {
            $mail = Mail::createMailer();
            $mail->addAddress($email, $username);
            $mail->Subject = 'Email Change Request';
            $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto;">
    <h2>Hello ' . htmlspecialchars($username) . ',</h2>

    <p>We received a request to change the email address associated with your account.</p>

    <p>Please click the button below to confirm your new email address:</p>

    <p>
        <a href="' . $url . '" 
           style="padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px;">
            Confirm Email Change
        </a>
    </p>

    <p>If the button doesn\'t work, copy and paste this URL into your browser:</p>

    <p>' . $url . '</p>

    <p>For security reasons, this link is valid for 24 hours only.</p>
    <p>If you didn\'t request this email change, please ignore this message or contact support immediately.</p>
</div>';
            $mail->AltBody = "Hello $username,\nPlease verify your email: $url" . " If you didn\'t request this email change, please ignore this message or contact support immediately. For security reasons, this link is valid for 24 hours only.";
            return Mail::sendEmail($mail);
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }

    public function passwordResetEmail($email, $username, $token)
    {
        $resetUrl = Env::get("APP_URL") . "recover-password/$token";
        try {
            $mail = Mail::createMailer();
            $mail->addAddress($email, $username);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto;">
            <h2>Hello ' . htmlspecialchars($username) . ',</h2>

            <p>We received a request to reset your password.</p>

            <p>
                <a href="' . $resetUrl . '" 
                   style="padding: 10px 20px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 5px;">
                    Reset Password
                </a>
            </p>

            <p>This link will expire in 1 hour.</p>

            <p>If you did not request this password reset, you can safely ignore this email.</p>
        </div>';
            $mail->AltBody = "Hello $username,\nPlease verify your email: $resetUrl" . " If you didn\'t request this email change, please ignore this message or contact support immediately. For security reasons, this link is valid for 24 hours only.";
            return Mail::sendEmail($mail);
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            echo "<pre>" . $e->getMessage() . "</pre>";
        }
    }
}
