<?php 
set_include_path('F:/Programming/xampp/htdocs/.git/mediaio/server/batch_jobs');
header('Content-type: text/plain');
require_once('F:/Programming/xampp/htdocs/.git/mediaio/PHPMailer/src/PHPMailer.php');
$today = new DateTime(date("Y-m-d H:i:s"));
$todayString=$today->format("Y_m_d_H_i_s");
$log = fopen(get_include_path()."/logs/$todayString.txt", "a");
//$myfile = fopen("testfile.txt", "w")
echo get_include_path()."/logs/$todayString.txt";
fwrite($log, "BATCH-folyamat megkezdése [".$today->format("Y-m-d H:i:s")."]\n");
require '../../PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\SMTP; // A batch loghoz majd.
/*Email küldése a következőkről:
* Több, mint egy hete nem megerősített esemémy
* Több, mint egy hónapja kivett tárgy, ami nem lett visszahozva.
*/
//Mai nap:



//PÉLDA két dátum közti különbségre.
$earlier = new DateTime("2010-07-06");
$diff = $today->diff($earlier)->format("%a");
echo $diff;

//Példa a dátumok különbségére:
//SELECT *, DATE("2021/01/25")-DATE(Date) FROM takelog WHERE (DATE("2021/01/25")-DATE(Date))>0 ORDER BY Date DESC
function BATCH_notify_Unconfirmed_Events($today,$log){

    $batchName="BATCH_notify_Unconfirmed_Events";

    echo "\n".$batchName." service starting, see log for further info.\n";
    fwrite($log, "\n\n//// ".$batchName." //// \n*** Kezdés ***\n\n");

    fwrite($log, "\n".$batchName." csatlakozás az adatbázishoz\n");
    $mysqli = new mysqli("localhost", "root", "umvHVAZ%", "mediaio");

    //echo "\n".$batchName." service starting\n";
   

    $todayFormatted=date_format($today, 'Y-m-d');
    fwrite($log, "\n".$batchName." [Query] - starting\n");
    if ($result = $mysqli->query("SELECT *, (date_Created-'$todayFormatted') FROM eventprep WHERE (date_Created-'$todayFormatted')>0")) {
        //Felhasználóknak elküldeni újra az esemény megerősítéséhez szükséges e-mailt:
        fwrite($log, "\n".$batchName." [Query] - Query returned $result->num_rows rows.\n");
        //printf("Select returned %d rows.\n", $result->num_rows); //Ilyen feltétellel egyező sorok száma
        while($obj = $result->fetch_object()){
            //Minden visszatért objekt ellenőrzése
            echo $obj->date_Created;
            fwrite($log, "\n".$batchName." [Query] - starting\n");
            if ($result2 = $mysqli->query("SELECT emailUsers FROM users WHERE usernameUsers='$obj->user'")){ // Második lekérdezés
                while($obj2 = $result2->fetch_object()){
                fwrite($log, "\n".$batchName." match, sending  E-mail to $obj2->emailUsers....");
                echo $obj2->emailUsers; // Ha megvan az e-mail, kiküldés

                //E-mail
                $to=$obj2->emailUsers;
                $subject = 'Elfelejtetted megerősíteni az eseményt?';
                $message = '
                <html>
                <head>
                <title>Arpad Media IO</title>
                </head>
                <body>
                <h3>Kedves '.$obj->user.'!</h3><p>
                Találtunk egy eseményt, amit már több, mint 2 napja hoztál létre, de még mindig nem erősítettél meg.
                Mit szeretnél tenni az eseménnyel?</p>
                <table style="border: 1px solid black; width: 50%">
                <tr>
                <th>Esemény neve</th>
                <th>Esemény kezdete</th>
                <th>Esemény vége<td></th>
                </tr>
                <tr>
                <td>'.$obj->title.'</h6>'.'</td><td>'.$obj->start_event.'</td><td>'.$obj->end_event.'</td></tr>
                </table>
                Kérlek ellenőrizd az az adatokat, mielőtt jóváhagyod az eseményt.
                <h2><a href="http://80.99.70.46/.git/mediaio/events/prepFinalise.php?secureId='.$obj->secureId.'&mode=del">Törölni szeretném az eseményt.</a></h2>
                <h2><a href="http://80.99.70.46/.git/mediaio/events/prepFinalise.php?secureId='.$obj->secureId.'&mode=add">Esemény hozzáadása.</a></h2>
                <h5>
                Ha figyelmen kívül hagyod az e-mailt, egy hét után automatikusan töröljük a megerősítetlen eseményedet.<br>
                Üdvözlettel: <br> Arpad Media Admin</h5>
                </body>
                </html>
                ';
                $Mail_headers[] = 'MIME-Version: 1.0';
                $Mail_headers[] = 'From: arpadmedia.io@gmail.com';
                $Mail_headers[] = 'Content-type: text/html; charset=utf-8';
                mail($to, '=?utf-8?B?'.base64_encode($subject).'?=', $message, implode("\r\n", $Mail_headers));//E-mail kiküldése
                
                }
            fwrite($log, "...Sent, moving on.\n"); 
            };// Második lekérdezés ( e-mail) vége
            
            };// Összes esemény vége
            fwrite($log, "\n".$batchName." [Query] - finished\n");
        };// Első lekérdezés vége
        fwrite($log, "\n".$batchName." completed.\n\n---------\n\n");
    $mysqli->close();
};

function BATCH_notify_monthItems($today,$log){
    $batchName="BATCH_notify_monthItems";

    echo "\n".$batchName." service starting\n";
    fwrite($log, "\n\n//// ".$batchName." //// \n*** Kezdés ***\n\n");
    
    $mysqli = new mysqli("localhost", "root", "umvHVAZ%", "mediaio");
    if ($mysqli->connect_errno) {
        echo("Connect failed: ".$mysqli->connect_error);
        fwrite($log, $batchName.": Connect failed: ".$mysqli->connect_error);
        exit();
    }
    $todayFormatted=date_format($today, 'Y-m-d');

    //query1: Az összes kinn lévő tárgyak felsorolása
        //query1_obj: 
    //query2: A tárgyak legutolsó OUT időpontja
            //ha igaz, query3: a felhasználó e-mail címének lekérése
            //e-mail

    echo $batchName.": [Query] - SQL:\t [SELECT Nev, RentBy FROM leltar WHERE Status=0]\n";
    fwrite($log, $batchName.": [Query] - SQL:\t [SELECT Nev, RentBy FROM leltar WHERE Status=0]\n");

    if ($query1_result = $mysqli->query("SELECT Nev, RentBy FROM leltar WHERE Status=0")){// Az összes kinn lévő tárgyak felsorolása
        while($query1_obj = $query1_result->fetch_object()){
            // Minden kinn lévő tárgy:
            echo "$batchName: [Query] - \tItem: $query1_obj->Nev \t RentBy: $query1_obj->RentBy\n";
            fwrite($log, "$batchName: [Query] - \tItem: $query1_obj->Nev \t RentBy: $query1_obj->RentBy\n");

            $query2_query=("SELECT Date, User, Event FROM takelog WHERE Item='$query1_obj->Nev' AND User='$query1_obj->RentBy' AND Event='OUT' ORDER BY Date DESC LIMIT 1");
            if($query2_result=$mysqli->query($query2_query)){ //HA VAN olyan tárgy ami megfelel ennek
                while($query2_obj = $query2_result->fetch_object()){

                    echo $batchName.": [Query]\n";
                    fwrite($log, $batchName.": [Query]\n");

                    //Egy tárgyra (feltehetően egy rekord)
                    $query2_item_eventDate=new DateTime($query2_obj->Date); // Új ellenőrizendő dátum
                    //$query2_item_eventDate=$query2_item_eventDate->format('Y-m-d');
                    $diff = date_diff($today, $query2_item_eventDate);
                    //echo $diff;
                    //$diff_S = $diff->format('%d days');
                    $diff_days = $diff->format('%a');

                    echo $batchName.": dateDiff: \t".$query2_item_eventDate->format("Y-m-d")."\t$todayFormatted\t$diff_days";
                    fwrite($log, $batchName.": dateDiff: \t".$query2_item_eventDate->format("Y-m-d")."\t$todayFormatted\t$diff_days");

                    //echo $batchName.": dateDiff:\t".$query2_item_eventDate."\t".$today->format("Y-m-d")."\t".$diff->format('%d days')."\n";
                    if($diff_days>=30){//e-mail kiküldése szükséges
                        //query3: a felhasználó e-mail címének lekérése
                        fwrite($log, "\t\t\t[!]\n\t> 1 Month dateDiff, e-mail send..\n");
                        echo "\t\t\t[!]\n\t> 1 Month dateDiff, e-mail send..\n";

                        if($query3_result=$mysqli->query("SELECT emailUsers FROM users WHERE usernameUsers='$query1_obj->RentBy'")){
                            while($query3_obj = $query3_result->fetch_object()){
                                //E-mail
                                //echo $query3_obj->emailUsers; // Ha megvan az e-mail, kiküldés

                                //E-mail
                                $to=$query3_obj->emailUsers;
                                $subject = "MediaIO - Hol van a(z) $query1_obj->Nev ?";
                                $message = '
                                <html>
                                <head>
                                <title>Arpad Media IO</title>
                                </head>
                                <body>
                                <h3>Kedves '.$query1_obj->RentBy.'!</h3><p>
                                Közel egy hónapja van kinn nálad egy tárgy:</p>
                                <table style="border: 1px solid black; width: 50%">
                                <tr>
                                <th>Tárgy</th>
                                <th>Kivétel dátuma</th>
                                </tr>
                                <tr>
                                <td>'.$query1_obj->Nev.'</h6>'.'</td><td>'.$query2_obj->Date.'</td></tr>
                                </table>
                                Ha továbbra is szükséged van rá, kérlek jelezd azt a vezetőségnek. Ellenkező esetben hozd vissza az iskolába, kérlek!
                                Ha figyelmen kívül hagyod az e-mailt, egy vezetőségi tag személyesen keresni fog téged.<br>
                                Üdvözlettel: <br> Arpad Media AutoAdmin</h5>
                                </body>
                                </html>
                                ';
                                $Mail_headers[] = 'MIME-Version: 1.0';
                                $Mail_headers[] = 'From: arpadmedia.io@gmail.com';
                                $Mail_headers[] = 'Content-type: text/html; charset=utf-8';
                                mail($to, '=?utf-8?B?'.base64_encode($subject).'?=', $message, implode("\r\n", $Mail_headers));//E-mail kiküldése

                                echo "\t$batchName: Mail sent to $to\n";
                                fwrite($log, "\t$batchName: Mail sent to $to\n");
                            }
                        }else{
                            echo "QUERY3 Error";
                            fwrite($log, "QUERY3 Error".$query3_result->error);
                        }
                    }else{
                        echo "\n";
                        fwrite($log, "\n");
                    }
                }
            }else{
                echo "QUERY2 Error";
                fwrite($log, "QUERY2 Error".$query2_result->error);
            }
        }
    }else{
        echo "QUERY1 Error";
        fwrite($log, "QUERY1 Error".$query1_result->error);
    }
    echo "\n".$batchName." Completed.\n\n";
    fwrite($log, "\n".$batchName." Completed.\n\n");

    

    
}
BATCH_notify_Unconfirmed_Events($today,$log); // BATCH_JOB: Megerősítetlen eseményekről e-mailt kiküldeni
//BATCH_JOB: 30 napnál tovább kinn levő tárgyakról e-mailt küldeni a megfelelő személynek.
BATCH_notify_monthItems($today,$log);
//fwrite($log, "\nBatch completed on\t".$endTime->format("Y-m-d H:i:s"));
fwrite($log, "\n\nE-mail küldése az adminnak");
fclose($log);


//Mail to admin:
/* Create a new PHPMailer object. */
$mail = new PHPMailer();
$mail->SMTPOptions = array(
    'ssl' => array(
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => true
    )
    );


$mail->Mailer = "smtp";
$mail->SMTPAuth   = TRUE;
$mail->SMTPSecure = "tls";
$mail->Port       = 587;
$mail->Host       = "smtp.gmail.com";
$mail->Username   = "arpadmedia.io@gmail.com";
$mail->Password   = "xlr8VGA%";
/* Set the mail sender. */
$mail->setFrom('arpadmedia@gmail.com', 'mediaIO cron');
$mail->CharSet = 'UTF-8';
$mail->Encoding = 'base64';
/* Add a recipient. */
$mail->addAddress('gutasi.guti@gmail.com', 'Media Admin');

/* Set the subject. */
$mail->Subject = 'Cron folyamat ['.$today->format("Y/m/d H:i:s").'] elkészült';

/* Set the mail message body. */
$mail->Body = 'Elkészült egy cron Folyamat a mediaio Szerverén.';
$mail->AddAttachment(get_include_path()."/logs/$todayString.txt");

/* Finally send the mail. */
if (!$mail->send())
{
   /* PHPMailer error. */
   echo $mail->ErrorInfo;
}


?>