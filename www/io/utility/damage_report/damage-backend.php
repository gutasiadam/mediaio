<?php
namespace Mediaio;
require_once __DIR__.'/../../Mailer.php';
require_once __DIR__.'/../../Database.php';
use Mediaio\MailService;
use Mediaio\Database;

error_reporting(E_ALL ^ E_NOTICE);
session_start();

if (isset($_POST['method'])){
    if($_POST['method']=='get_user_items'){
        $sql = "SELECT Nev, UID FROM `leltar` WHERE RentBy = '".$_POST['userName']."'";
        $result = Database::runQuery($sql);

        //for each result row, put it in a sjon array, and then echo it
        $resultArray = [];
        while($row = $result->fetch_assoc()) {
            array_push($resultArray, $row);
        }
        echo json_encode($resultArray);
        exit();
    }
    if($_POST['method']=='announceDamage'){

        $subject = 'Sérülés - '.$_POST['userItems'];
       /* Set the mail message body. */
        $content = $_SESSION['fullName'].' sérülést jelentett be a(z) <strong>'.$_POST['userItems'].'</strong> tárgyon! <br>Leírás: '.$_POST['description'];
        //Add link to e-mail
        $content .= '<br><button><a href="https://io.arpadmedia.hu/uploads/images/'.$_POST['zip-file'].'.zip">Képek letöltése</a></button>
                ';
        /* Finally send the mail using MailService */
        MailService::sendContactMail('MediaIO-sérülésbejelntő','arpadmedia.io@gmail.com',$subject,$content);
        echo 200;
        exit();
    }
}







?>