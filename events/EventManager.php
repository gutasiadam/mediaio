<?php 
namespace Mediaio;
use Mediaio\Core;
use Mediaio\Database;
use Mediaio\MailService;
require_once __DIR__.'/../Core.php';
require_once __DIR__.'/../Database.php';
require_once __DIR__.'/../Mailer.php';


class EventManager{
    const ip_address='192.168.0.24';
    static function loadEvents(){

    $data = array();
    $query = "SELECT * FROM events ORDER BY id";
    $result = Database::runQuery($query);
    foreach($result as $row){
         $data[] = array(
          'id'   => $row["id"],
          'title'   => $row["title"],
          'start'   => $row["start_event"],
          'end'   => $row["end_event"],
          'backgroundColor' => $row["borderColor"],
          'borderColor' => $row["borderColor"]
        );
    }

    return json_encode($data);
    }

    static function prepareNewEvent($postData){
        $log=fopen('Logger.txt','w');
        $date = date("Y-m-d");
        $userName= $postData["username"];
        function generateRandomString($length = 10) {
            return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
        }
        
        $secureId = generateRandomString();
        $query = "SELECT secureId FROM eventrep WHERE secureId = '$secureId' ";
        $result = Database::runQuery($query);

        //Preventing double randomString generation
        if($result){
            if ($result->num_rows > 0) {
                fwrite($log,"New secureId needed.");
                while($result->num_rows == 0){
                $secureId = generateRandomString();
                $result = Database::runQuery($query);
            }
        }else{ 
            echo "Error in ".$query;
        }

    }
        fwrite($log,"Found acceptable secureID.");
        fwrite($log,"inserting event to prep queue.");
        $query = "
        INSERT INTO eventprep 
        (title, date_Created, start_event, end_event, borderColor, secureId, user) 
        VALUES ('".$postData['title']."', '".$date."', '".$postData['start']."', 
        '".$postData['end']."', '".$postData['type']."', '".$secureId."', '".$userName."')";
        $result=Database::runQuery($query);
        if($result){
            fwrite($log,"forming mail.");
            $content='
            <html>
            <head>
              <title>Arpad Media IO</title>
            </head>
            <body>
              <h3>Kedves '.$userName.'!</h3><p>
             Kattints az alábbi linkre, hogy megerősítsd a(z)'.$postData['title'].' esemény létrehozását</p>
             <table style="border: 1px solid black; width: 50%">
             <tr>
             <th>Esemény neve</th>
             <th>Esemény kezdete</th>
             <th>Esemény vége<td></th>
             </tr>
             <tr>
             <td>'.$postData['title'].'</h6>'.'</td><td>'.$postData['start'].'</td><td>'.$postData['end'].'</td></tr>
             </table>
            Kérlek ellenőrizd az az adatokat, mielőtt jóváhagyod az eseményt. Ezek a linkek csak a belső Wifin működnek!!
            Ha az esemény adatait hibásan adtad meg, <a href="192.168.0.24/.git/mediaio/events/EventManager.php?secureId='.$secureId.'&mode=del">kattints ide ❌</a>
            <h2><a href="192.168.0.24/.git/mediaio/events/EventManager.php?secureId='.$secureId.'&mode=add">Esemény hozzáadása ✔</a></h2>
              <h5>Üdvözlettel: <br> Arpad Media Admin</h5>
            '.EventManager::ip_address.'
            </body>
            </html>
            ';
            
           try{
            fwrite($log,"mailing now.");
            MailService::sendContactMail('MediaIO',$_SESSION['email'],'Esemény hozzáadása - '.$postData['title'],$content);
            }catch (Exception $e){
                fwrite($log, 'Caught exception: '.$e->getMessage()."\n");
            }
            fwrite($log,"Mailing completed.");
            fclose($log);
            return 1;
           
        }else{
            fwrite($log,"failed.");
            fclose($log);
            return 0;
        }
    }
    static function finalizeEvent(){
        $secureId = $_GET['secureId'];
        if($_GET['mode']=="add"){
            $query = "SELECT title, start_event, end_event, borderColor FROM `eventprep` WHERE secureId = '$secureId'";
            $result = Database::runQuery($query);
            if ($result and $result->num_rows == 1){
                foreach($result as $row){
                    $eventTitle=$row["title"];
                    $eventStart=$row["start_event"];
                    $eventEnd=$row["end_event"];
                    $eventColor=$row["borderColor"];
            }
            $sql1 = "INSERT INTO events (title, start_event, end_event, borderColor) VALUES ('".$eventTitle."','".$eventStart."',
            '".$eventEnd."','".$eventColor."')"; 
            $sql2= "DELETE FROM eventprep WHERE secureId = '".$secureId."';";
            //echo $sql1; echo $sql2;
            $res = Database::runQuery($sql1);
            $res = Database::runQuery($sql2);
            if($res){
                echo "<h1><strong>Sikeresen megerősítetted az eseményt! 🎉</strong></h1>";}
            } 
            else{
                echo "<h1>Az esemény kódja érvénytelen! Nem lehet, hogy már megerősítetted?</h1>";}
            }

            if($_GET['mode']=="del"){
                $query = "DELETE FROM eventprep WHERE secureId = '$secureId'";
                $res = Database::runQuery($query);
                if ($res){   
                    echo "<h1>Törölve.</h1>";
                }else{
                    echo "<h1>Hiba.</h1>";
                }
            }
        
        return;
    }
    static function deleteEvent(){
         $query = "DELETE from events WHERE id='".$_POST['id']."'";
         $res = Database::runQuery($query);
         return;
    }

    static function rescheduleEvent(){

    }

}

if(isset($_POST['o'])){
    if($_POST['o']=='prepare'){
        $postData=array('title'=>$_POST['title'],'start'=>$_POST['start'],'end'=>$_POST['end'],'type'=>$_POST['type'],
        'username'=>$_SESSION['UserUserName']);
        echo EventManager::prepareNewEvent($postData);
    }
    if($_POST['o']=='delete'){
        $postData=array('title'=>$_POST['title'],'start'=>$_POST['start'],'end'=>$_POST['end'],'type'=>$_POST['type'],
        'username'=>$_SESSION['UserUserName']);
        echo EventManager::deleteEvent();
    }
}
if(isset($_GET['mode'])){
    EventManager::finalizeEvent();
}
if(isset($_GET['o'])){
    if($_GET['o']='load'){
        echo EventManager::loadEvents();
    }
}

?>