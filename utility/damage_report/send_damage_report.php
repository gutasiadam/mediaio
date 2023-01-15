<?php
namespace Mediaio;
require_once __DIR__.'/../../Mailer.php';
require_once __DIR__.'/../../Database.php';
use Mediaio\MailService;
use Mediaio\Database;

error_reporting(E_ALL ^ E_NOTICE);
session_start();

//Tárgy átadása a szerviz felhasznalonak:
//Update..
//logolni
$data = json_decode(stripslashes($_POST['data']),true);
$currDate = date("Y/m/d H:i:s");
$sql1="UPDATE `leltar` SET RentBy='service' WHERE UID='".$data['UID']."'";
Database::runQuery($sql1);
$sql2 = ("INSERT INTO `takelog` (`ID`, `takeID`, `Date`, `User`, `Item`, `Event`) VALUES (NULL, '".$data['UID']."', '".$currDate."', '".$_SESSION['UserUserName']."', '".$data["Nev"]."', 'SERVICE')");
Database::runQuery($sql2);
/* Set the subject. */
$subject = 'bejelentett sérülés: '.$data['UID'];

/* Set the mail message body. */
$content = $_SESSION['fullName'].' sérülést jelentett be a <strong>'.$data['Nev'].'</strong> tárgyon! <br> Leírás: '.$data['err_description'];

/* Finally send the mail using MailService */
MailService::sendContactMail('MediaIO-sérülésbejelntő','arpadmedia.io@gmail.com',$subject,$content);
echo 200;




?>