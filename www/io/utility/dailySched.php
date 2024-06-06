<?php
//    A file which runs daily 
//    Includes jobs like automatic email notifications,
//    updating the database, etc.

namespace Mediaio;

// Include the necessary files
require_once '../ItemManager.php';
require_once '../ProjectMailer.php';
require_once '../Mailer.php';
require_once '../Accounting.php';

// Set the time zone to Budapest
date_default_timezone_set('Europe/Budapest');

class DailySchedule
{
    /*
        The following functions are for the takeout system
        ---------------------------------------------------
    */

    static function plannedTakeoutReminder()
    {
        try {
            $takeouts = itemDataManager::getPlannedTakeouts();

            foreach ($takeouts as $takeout) {
                if ($takeout['eventState'] != 0)
                    continue; // Skip if the takeout is already initiated

                // Check if the takeout starts in less than 24 hours
                $startTime = strtotime($takeout['StartTime']);
                $tomorrow = strtotime('now') + 86400; // Tomorrow 06:00:00 AM --> script runs at 06:00:00 AM
                $timeDifference = $tomorrow - $startTime;

                if ($timeDifference <= 86400 && $timeDifference >= 0) {
                    // Get the users details
                    $user = Accounting::getPublicUserInfo($takeout['UserID']);
                    $user = json_decode($user, true);
                    $user = $user[0];

                    // Details
                    $email = $user['emailUsers'];
                    $name = $user['firstName'];

                    // Get the devices
                    $devices = json_decode($takeout['Items'], true);
                    $deviceList = '';
                    foreach ($devices as $device) {
                        $deviceList .= '<li>' . $device['name'] . ' - ' . $device['uid'] . '</li>';
                    }

                    // Send a reminder email to the user
                    $subject = 'Média IO - Előre tervezett elvitel (Holnap)';
                    $message = '
                    <html>
                    <head>
                    <title>Média IO</title>
                    </head>
                    <body>
                    <h3>Kedves ' . $name . '!</h3>
                    <p>Ez egy emlékeztető arról, hogy holnap lesz esedékes egy előre tervezett eszköz elviteled.</p>
                    <p>Kezdési idő: ' . $takeout['StartTime'] . '</p>
                    <p>Leírás: ' . $takeout['Description'] . '</p>
                    <br>
                    <ul>Általad kiválaszott eszközök:' . $deviceList . '</ul>

                    <p>Üdvözlettel, Média IO</p>
                    <i>Ez egy tájékoztató üzenet, kérlek ne válaszolj rá!</i>
                    </body>
                    </html>
                    ';

                    MailService::sendContactMail($email, $subject, $message);
                    echo 'Reminder sent to ' . $email . "\n";
                }
            }
        } catch (\Exception $e) {
            // Log the error
            error_log($e->getMessage());
        }
    }


    static function notInitiatedTakeoutDisable()
    {
        try {
            $takeouts = itemDataManager::getPlannedTakeouts();

            foreach ($takeouts as $takeout) {
                // Check if the takeout is older than 24 hours
                $startTime = strtotime($takeout['StartTime']);
                $currentTime = strtotime('now');
                $timeDifference = $currentTime - $startTime;

                if ($timeDifference > 86400 && $takeout['eventState'] == 0) {
                    // Delete the takeout
                    itemDataManager::disableTakeout($takeout['ID']);

                    // TODO: Send an email to the user if needed
                }
            }
            echo "Not initiated takeout disabled successfully!\n";
        } catch (\Exception $e) {
            // Log the error
            error_log($e->getMessage());
        }
    }

    static function takeoutEndReminder()
    {
        try {
            $takeouts = itemDataManager::getPlannedTakeouts();
            //$takeouts = json_decode($takeoutsJSON, true);

            foreach ($takeouts as $takeout) {
                if ($takeout['eventState'] != 1)
                    continue; // Skip if the takeout is not initiated

                // Check if the takeout starts in less than 24 hours
                $endTime = strtotime($takeout['ReturnTime']);
                $tomorrow = strtotime('now') + 2*86400; // Tomorrow 06:00:00 AM --> script runs at 06:00:00 AM
                $timeDifference = $tomorrow - $endTime;

                if ($timeDifference <= 86400 && $timeDifference >= 0) {
                    // Get the users details
                    $user = Accounting::getPublicUserInfo($takeout['UserID']);
                    $user = json_decode($user, true);
                    $user = $user[0];

                    // Details
                    $email = $user['emailUsers'];
                    $name = $user['firstName'];

                    // Get the devices
                    $devices = json_decode($takeout['Items'], true);
                    $deviceList = '';
                    foreach ($devices as $device) {
                        $deviceList .= '<li>' . $device['name'] . ' - ' . $device['uid'] . '</li>';
                    }

                    // Send a reminder email to the user
                    $subject = 'Média IO - Elvitel vége (Holnap)';
                    $message = '
                    <html>
                    <head>
                    <title>Média IO</title>
                    </head>
                    <body>
                    <h3>Kedves ' . $name . '!</h3>
                    <p>Ez egy emlékeztető arról, hogy holnap esedékes az alábbi eszközök visszahozatala.</p>
                    <br>
                    <ul>Általad kiválaszott eszközök:' . $deviceList . '</ul>
                    <br>
                    <p>Elvitel időpont: ' . $takeout['StartTime'] . '</p>
                    <p>Tervezett visszahozás: ' . $takeout['ReturnTime'] . '</p>
                    <br>
                    <p>Leírás: ' . $takeout['Description'] . '</p>
                    <br>
                    <p>Üdvözlettel, Média IO</p>
                    <i>Ez egy tájékoztató üzenet, kérlek ne válaszolj rá!</i>
                    </body>
                    </html>
                    ';

                    MailService::sendContactMail($email, $subject, $message);
                    echo 'Reminder sent to ' . $email . "\n";
                }
            }
        } catch (\Exception $e) {
            // Log the error
            error_log($e->getMessage());
        }
    }

    /*
        The following functions are for the projectManaging system
        ---------------------------------------------------
    */


    static function projectDeadlineReminder() 
    {
        
    }

    /*
        The following functions are for the admin statistics system
        TODO: Implement the functions
    */

}

// Testing purposes
/*if (isset($_GET['mode'])) {
    switch ($_GET['mode']) {
        case 'plannedTakeoutReminder':
            DailySchedule::plannedTakeoutReminder();
            break;
        case 'notInitiatedTakeoutDisable':
            DailySchedule::notInitiatedTakeoutDisable();
            break;
    }
    exit();
}
*/

// Run the planned takeout reminder
DailySchedule::plannedTakeoutReminder();
DailySchedule::notInitiatedTakeoutDisable();
DailySchedule::takeoutEndReminder();