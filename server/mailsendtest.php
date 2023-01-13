<?php
namespace Mediaio;

use Mediaio\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;
require '../vendor/autoload.php';

require __DIR__ .'/../vendor/phpmailer/phpmailer/src/Exception.php';
require __DIR__ .'/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ .'/../vendor/phpmailer/phpmailer/src/SMTP.php';

class MailService
{

    function sendContactMail()
    {
        $name = "Attru";
        $email = "foldes.artur@gmail.com";
        $subject = "kaka";
        $content = "hemol bemol jdahdsjahsjahsjahjshaxsjhasjhajko 
        hasznos indó xd";

        require_once __DIR__ . '/../Config.php';
        $recipientArray = explode(",", Config::RECIPIENT_EMAIL);

        require_once __DIR__ . '/../vendor/autoload.php';
        $mail = new PHPMailer(true);

        // Comment the following lines of code till $mail->Port to send
        // mail using phpmail instead of smtp.
        $mail->isSMTP();
        //Enable SMTP debugging
        //SMTP::DEBUG_OFF = off (for production use)
        //SMTP::DEBUG_CLIENT = client messages
        //SMTP::DEBUG_SERVER = client and server messages
        $mail->SMTPDebug = SMTP::DEBUG_OFF;

        //Set the hostname of the mail server
        $mail->Host = Config::SMTP_HOST;

        //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
        $mail->Port = Config::SMTP_PORT;

        //Set the encryption mechanism to use - STARTTLS or SMTPS
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;

        //Set AuthType to use XOAUTH2
        $mail->AuthType = 'XOAUTH2';

        //Fill in authentication details here
        //Either the gmail account owner, or the user that gave consent
        $oauthUserEmail = Config::OAUTH_USER_EMAIL;
        $clientId = Config::OAUTH_CLIENT_ID;
        $clientSecret = Config::OAUTH_SECRET_KEY;

        //Obtained by configuring and running get_oauth_token.php
        //after setting up an app in Google Developer Console.
        $refreshToken = Config::REFRESH_TOKEN;

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
                    'userName' => $oauthUserEmail,
                ]
                )
            );

        // Recipients
        $mail->setFrom(Config::SENDER_EMAIL, $name);
        $mail->addReplyTo($email, $name);

        $mail->addAddress(Config::RECIPIENT_EMAIL, Config::RECIPIENT_EMAIL);

        $mail->Subject = $subject;

        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->msgHTML($content);

        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';

        if (!$mail->send()) {
            $output = json_encode(array('type'=>'error', 'text' => '<b>'.$from.'</b> is invalid.'));
            $output = json_encode(array('type'=>'error', 'text' => 'Server error. Please mail vincy@phppot.com'));
        } else {
            $output = json_encode(array('type'=>'message', 'text' => 'Thank you, I will get back to you shortly.'));
        }
        return $output;
    }
    
}
MailService::sendContactMail();