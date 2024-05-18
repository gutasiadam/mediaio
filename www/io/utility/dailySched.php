<?php
// A file which runs daily 
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

    static function plannedTakeoutReminder()
    {
        try {
            $takeoutsJSON = itemDataManager::getPlannedTakeouts();
            $takeouts = json_decode($takeoutsJSON, true);
            $takeouts = $takeouts['events'];

            foreach ($takeouts as $takeout) {
                // Check if the takeout starts in less than 24 hours
                $startTime = strtotime($takeout['StartTime']);
                $tomorrow = strtotime('tomorrow') + 86400; // Tomorrow 11:59:59 PM
                $timeDifference = $tomorrow - $startTime;

                if ($timeDifference <= 86400 && $timeDifference >= 0 && $takeout['eventState'] == 0) {
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
            $takeoutsJSON = itemDataManager::getPlannedTakeouts();
            $takeouts = json_decode($takeoutsJSON, true);
            $takeouts = $takeouts['events'];

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
            echo 'Not initiated takeout disabled successfully!';
        } catch (\Exception $e) {
            // Log the error
            error_log($e->getMessage());
        }
    }


    /*static function projectDeadlineReminder() // NEEDS TO BE IMPLEMENTED
    {
        $projectsJSON = ProjectMailer::getProjects();
        $projects = json_decode($projectsJSON, true);

        foreach ($projects as $project) {
            // Check if the project deadline is tomorrow
            $deadline = strtotime($project['Deadline']);
            $tomorrow = strtotime('tomorrow');
            $timeDifference = $deadline - $tomorrow;

            if ($timeDifference < 86400 && $timeDifference > 0) {
                // Get the members of the project
                $membersJSON = ProjectMailer::getProjectMembers($project['ID']);
                $members = json_decode($membersJSON, true);

                // Get the project manager
                $managerJSON = ProjectMailer::getProjectManager($project['ID']);
                $manager = json_decode($managerJSON, true);

                // Details
                $projectName = $project['Name'];
                $managerName = $manager['firstName'] . ' ' . $manager['lastName'];

                // Get current time
                $currentTime = date("Y-m-d H:i:s");
                if ($currentTime < $project['Deadline']) {
                    $message = "A(z) " . $projectName . " projekt a határideje " . $project['Deadline'] . ".\n\n";
                } else {
                    $message = "A(z) " . $projectName . " projekt határideje lejárt.\n\n";
                }

                // Send an email to all members
                foreach ($members as $member) {
                    $email = $member['emailUsers'];
                    $name = $member['firstName'];

                    $message .= "Kedves " . $name . "! \n" . $message;
                    MailService::sendContactMail($email, 'Projekt határidő emlékeztető', $message);
                }
            }
        }
    }*/
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