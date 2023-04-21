<?php
namespace Mediaio;

use Mediaio\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;
require_once __DIR__ .'./vendor/autoload.php';
require_once __DIR__ .'./Config.php';

require_once __DIR__ .'/vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ .'/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ .'/vendor/phpmailer/phpmailer/src/SMTP.php';

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

class MailService
{

    static function sendContactMail($name, $to,$subject,$content)
    {
        $myfile = fopen("mailLog.txt", "w");
        //require_once __DIR__ . '/../Config.php';
        $recipientArray = explode(",", $to);
        $mail = new PHPMailer();
        //$mail->SMTPDebug = SMTP::DEBUG_LOWLEVEL;

        // Comment the following lines of code till $mail->Port to send
        // mail using phpmail instead of smtp.
        $mail->isSMTP();
        //Enable SMTP debugging
        //SMTP::DEBUG_OFF = off (for production use)
        //SMTP::DEBUG_CLIENT = client messages
        //SMTP::DEBUG_SERVER = client and server messages
        $mail->SMTPDebug = SMTP::DEBUG_OFF;


        //Set the hostname of the mail server
        $mail->Host = 'smtp.gmail.com';

        //Set the SMTP port number:
        // - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
        // - 587 for SMTP+STARTTLS
        $mail->Port = 465;

        //Set the encryption mechanism to use:
        // - SMTPS (implicit TLS on port 465) or
        // - STARTTLS (explicit TLS on port 587)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;

        //Set AuthType to use XOAUTH2
        $mail->AuthType = 'XOAUTH2';

        //Start Option 1: Use league/oauth2-client as OAuth2 token provider
        //Fill in authentication details here
        //Either the gmail account owner, or the user that gave consent
        $email = 'arpadmedia.io@gmail.com';
        $clientId = '335212318485-38buk9a080tub4o6evjgdqap8vd9t0rp.apps.googleusercontent.com';
        $clientSecret = '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';

        //Obtained by configuring and running get_oauth_token.php
        //after setting up an app in Google Developer Console.
        $refreshToken = '1//096dHfSMj84jQCgYIARAAGAkSNwF-L9IrOERj0Idt-ZMV5Y4_B4o982k1rPad6cOTexH6sIBj80UIgaTq_CZrrPpiMqTCCVjd-EI';

        //Create a new OAuth2 provider instance
        $provider = new Google(
            [
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
            ]
        );

        //Pass the OAuth provider instance to PHPMailer
        $mail->setOAuth(
            new OAuth(
                [
                    'provider' => $provider,
                    'clientId' => $clientId,
                    'clientSecret' => $clientSecret,
                    'refreshToken' => $refreshToken,
                    'userName' => $email,
                ]
            )
        );
        // Recipients
        $mail->setFrom(Config::SENDER_EMAIL, $name);
        $mail->addReplyTo('arpad.media@gmail.com', 'Árpád Média');

        $mail->addAddress($to);

        $mail->Subject = $subject;

        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->msgHTML($content);

        if (!$mail->send()) {
            fwrite(json_encode(array('type'=>'error', 'text' => '<b>'.$from.'</b> is invalid.')));
            fclose($myfile);
        } else {
            $output = json_encode(array('type'=>'message', 'text' => 'OK.'));
        }
        return $output;
    }
    
}