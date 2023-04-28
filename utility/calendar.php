<?php

require __DIR__ . '/../vendor/autoload.php'; // betöltjük a Google API PHP könyvtárát

putenv('GOOGLE_APPLICATION_CREDENTIALS=credentials.json'); // beállítjuk az elérési útvonalat a credentials.json fájlhoz
$client = new Google_Client();
$client->useApplicationDefaultCredentials();
$client->setScopes(['https://www.googleapis.com/auth/calendar']); // beállítjuk a szükséges jogosultságokat
$client->setAccessType('offline');
// Létrehozunk egy Google_Service_Calendar objektumot a Google Calendar API-hoz való hozzáféréshez
$service = new Google_Service_Calendar($client);

// Frissítjük a Google_Client objektumot az új naptárral
//$client->setAccessToken($client->getAccessToken());

// Lekérdezzük az összes elérhető naptárat
$calendarId = 'jjpdv8bd3u2s2hj9ehnbh19src@group.calendar.google.com';
$optParams = array(
  'maxResults' => 10,
  'orderBy' => 'startTime',
  'singleEvents' => true,
  'timeMin' => date('c'),
);
$results = $service->events->listEvents($calendarId, $optParams);
$events = $results->getItems();

if (empty($events)) {
  print "Nincs találat.\n";
} else {
  print "Események:\n";
  foreach ($events as $event) {
    $start = $event->start->dateTime;
    if (empty($start)) {
      $start = $event->start->date;
    }
    //echo $event->getSummary()." ".$event->start->date." ".$event->description."\n";
    printf("%s (%s) - %s\n\n", $event->getSummary(), $start ,$event->getDescription());
  }
}
