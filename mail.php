<?php
// Multiple recipients
$to = 'foldes.artur@gmail.com'; // note the comma

// Subject
$subject = 'MediaIO - Teszt rendszerüzenet';

// Message
$message = '
<html>
<head>
  <title>Arpad Media IO</title>
</head>
<body>
  <h3>Kedves Artúr!</h3><p></br>Ez az e-mail a MEDIAIO szolgáltatás segítségével lett elküldve. A következőben tartalmaz egy teszt HTML dokumentumot:</p>
  <table>
    <tr>
      <th>Person</th><th>Day</th><th>Month</th><th>Year</th>
    </tr>
    <tr>
      <td>A</td><td>10th</td><td>August</td><td>1970</td>
    </tr>
    <tr>
      <td>B</td><td>17th</td><td>August</td><td>1973</td>
    </tr>
  </table>
  <p>Kérem ne vegye figyelembe ezt az e-mailt, ez csak egy próba.</p>
</body>
</html>
';

// To send HTML mail, the Content-type header must be set
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-type: text/html; charset=iso-8859-1';

/* Additional headers
$headers[] = 'To: Mary <mary@example.com>, Kelly <kelly@example.com>';
$headers[] = 'From: Birthday Reminder <birthday@example.com>';
$headers[] = 'Cc: birthdayarchive@example.com';*/
$headers[] = 'Bcc: gutasi.guti@gmail.com';
// Mail it
mail($to, $subject, $message, implode("\r\n", $headers));
?>