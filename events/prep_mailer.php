<?php
$to = $_SESSION['email'];
$subject = 'MediaIO - Esemény hozzáadása';

// Message
$message = '
<html>
<head>
  <title>Arpad Media IO</title>
</head>
<body>
  <h3>Kedves '.$_SESSION['UserUserName'].'!</h3><p>
 Kattints az alábbi linkre, hogy megerősítsd a(z)'.$_POST['title'].' esemény létrehozását</p>
 <table style="border: 1px solid black; width: 50%">
 <tr>
 <th>Esemény neve</th>
 <th>Esemény kezdete</th>
 <th>Esemény vége<td></th>
 </tr>
 <tr>
 <td>'.$_POST['title'].'</h6>'.'</td><td>'.$_POST['start'].'</td><td>'.$_POST['end'].'</td></tr>
 </table>
Kérlek ellenőrizd az az adatokat, mielőtt jóváhagyod az eseményt.
Ha az esemény adatait hibásan adtad meg, <a href="http://80.99.70.46/.git/mediaio/events/prepFinalise.php?secureId='.$secureId.'&mode=del">kattints ide.</a>
<h2><a href="http://80.99.70.46/.git/mediaio/events/prepFinalise.php?secureId='.$secureId.'&mode=add">Esemény hozzáadása.</a></h2>
  <h5>Üdvözlettel: <br> Arpad Media Admin</h5>
</body>
</html>
';

// To send HTML mail, the Content-type header must be set
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=utf-8';

/* Additional headers
$headers[] = 'To: Mary <mary@example.com>, Kelly <kelly@example.com>';
$headers[] = 'From: Birthday Reminder <birthday@example.com>';
$headers[] = 'Cc: birthdayarchive@example.com';*/
mail($to, '=?utf-8?B?'.base64_encode($subject).'?=', $message, implode("\r\n", $headers));
?>