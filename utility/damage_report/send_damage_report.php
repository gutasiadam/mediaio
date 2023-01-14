<?php
namespace Mediaio;
require_once __DIR__.'/../../Mailer.php';
require_once __DIR__.'/../../Database.php';
use Mediaio\MailService;
use Mediaio\Database;

error_reporting(E_ALL ^ E_NOTICE);
session_start();
$data = json_decode(stripslashes($_POST['data']),true);
/* Set the subject. */
$subject = 'bejelentett sérülés: '.$data['UID'];

/* Set the mail message body. */
$content = $_SESSION['fullName'].' sérülést jelentett be a <strong>'.$data['Nev'].' ('.$data['UID'].')</strong> tárgyon! <br> Leírás: '.$data['err_description'];

/* Finally send the mail using MailService */
MailService::sendContactMail('MediaIO-sérülésbejelntő','arpadmedia.io@gmail.com',$subject,$content);
echo 200;


?>