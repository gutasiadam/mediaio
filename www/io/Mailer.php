<?php

namespace Mediaio;
require_once __DIR__ .'/vendor/autoload.php';
require_once __DIR__ .'/Config.php';

/*use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Model\SendSmtpEmail;*/
use Brevo\Client\Configuration;

// Configure API key authorization: api-key
// $config = Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');
// // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// // $config = Brevo\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('api-key', 'Bearer');
// // Configure API key authorization: partner-key
// // Uncomment below to setup prefix (e.g. Bearer) for API key, if needed
// // $config = Brevo\Client\Configuration::getDefaultConfiguration()->setApiKeyPrefix('partner-key', 'Bearer');

// $apiInstance = new Brevo\Client\Api\TransactionalEmailsApi(
//     // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
//     // This is optional, `GuzzleHttp\Client` will be used as default.
//     new GuzzleHttp\Client(),
//     $config
// );

// //$sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail(); // \Brevo\Client\Model\SendSmtpEmail | Values to send a transactional email
// $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail([
//     'sender' => ['email' => 'arpadmedia.io@gmail.com', 'name' => 'Árpád Média IO'],
//     'to' => [['email' => $to]], //, 'name' => 'Adam Gutasi'
//     'htmlContent' => $content,
//     'subject' => $subject,
// ]);
// try {
//     $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
//     print_r($result);
// } catch (Exception $e) {
//     echo 'Exception when calling TransactionalEmailsApi->sendTransacEmail: ', $e->getMessage(), PHP_EOL;
// }

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Europe/Budapest');

/**
 * Handles sending e-mails
 */

class MailService
{

    static function sendContactMail($name,$to,$subject,$content)
    {
        $jsonString = file_get_contents(__DIR__.'/server/mailCredentials.json');

        $data = json_decode($jsonString);

        $apiKey = $data->ApiKey;
        $config = \Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);

        $apiInstance = new \Brevo\Client\Api\TransactionalEmailsApi(
            // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
            // This is optional, `GuzzleHttp\Client` will be used as default.
            new \GuzzleHttp\Client(),
            $config
        );
        
        //$sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail(); // \Brevo\Client\Model\SendSmtpEmail | Values to send a transactional email
        $sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail([
            'sender' => ['email' => 'arpadmedia.io@gmail.com', 'name' => 'Árpád Média IO'],
            'to' => [['email' => $to]], //, 'name' => 'Adam Gutasi'
            'htmlContent' => $content,
            'subject' => $subject,
        ]);
        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
            print_r($result);
        } catch (\Exception $e) {
            echo 'Exception when calling TransactionalEmailsApi->sendTransacEmail: ', $e->getMessage(), PHP_EOL;
        }
        return $result;
    }

    static function sendContactMailWithAttachment(){

    }

    
}