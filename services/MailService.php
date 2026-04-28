<?php

namespace App\Services;


use App\Models\Mail;
use Exception;


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
}
