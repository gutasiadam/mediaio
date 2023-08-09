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

        //echo 200;
        //print out the POST data
        echo json_encode($_POST);

        //upload the recieved image to the server
        $target_dir = __DIR__."/../../uploads/";
        $target_file = $target_dir . basename($_FILES["file"]["name"][0]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image

        $check = getimagesize($_FILES["file"]["tmp_name"][0]);
        if($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;

        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
        // Check if file already exists
        if (file_exists($target_file)) {

            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
        // Check file size
        if ($_FILES["file"]["size"][0] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        // Allow certain file formats

        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {

            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error

        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file

        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"][0], $target_file)) {
                echo "The file ". basename( $_FILES["file"]["name"][0]). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }

        $subject = 'Sérülés bejelentés';
       /* Set the mail message body. */
        $content = $_SESSION['fullName'].' sérülést jelentett be a <strong>'.$_POST['userItems'].'</strong> tárgyon! <br> Leírás: '.$_POST['description'];

        //If the image upload was successful, add the image to the email embedded with base64
        if($uploadOk == 1){
            $content = $content.'<br><img src="cid:'.basename($_FILES["file"]["name"][0]).'">';
        }
        /* Finally send the mail using MailService */
        MailService::sendContactMail('MediaIO-sérülésbejelntő','arpadmedia.io@gmail.com',$subject,$content);
        exit();
    }
}







?>