<?php
namespace Mediaio;


require_once __DIR__ .'./vendor/autoload.php';
require_once __DIR__ .'./Config.php';

use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Model\SendSmtpEmail;

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Europe/Budapest');

class MailService
{

    static function sendContactMail($name,$to,$subject,$content)
    {
        echo $to;
        // Read the JSON file into a string
        $jsonString = file_get_contents('utility/mailCredentials.json');

        // Decode the JSON string into a PHP object
        $data = json_decode($jsonString);

        // Access the field using object syntax
        $apiKey = $data->mailApiKey;
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
        $apiInstance = new TransactionalEmailsApi(null, $config);

        $sendSmtpEmail = new SendSmtpEmail([
            'sender' => ['email' => 'arpadmedia.io@gmail.com', 'name' => 'Árpád Média IO'],
            'to' => [['email' => $to]], //, 'name' => 'Adam Gutasi'
            'htmlContent' => $content,
            'subject' => $subject,
        ]);

        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
            //print_r($result);
            $output = json_encode(array('type'=>'message', 'text' => 'OK.'));
        } catch (Exception $e) {
            $output = json_encode(array('type'=>'error', 'text' => $e->getMessage()));
        }
        return $output;
    }
    
}