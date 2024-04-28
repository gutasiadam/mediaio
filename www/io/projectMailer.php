<?php
namespace Mediaio;

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Core.php';
require_once __DIR__ . '/Mailer.php';
use Mediaio\Core;
use Mediaio\Database;
use Mediaio\MailService;

error_reporting(E_ERROR | E_PARSE);

session_start();


class ProjectMailer
{
    public static function sendMail($to, $subject, $message)
    {
        MailService::sendContactMail('Média IO - projektek', $to, $subject, $message);
    }

    public static function sendNewProjectMail($project_id, $member)
    {
        $connection = Database::runQuery_mysqli();
        // Send an email to the new member
        $sql = "SELECT `email`, `firstName` AND  FROM `users` WHERE `idUsers`=" . $member . ";";
        $result = $connection->query($sql);
        $email = $result->fetch_assoc()['email'];
        $name = $result->fetch_assoc()['firstName'];

        $sql = "SELECT `Name`,`managerUID` FROM `projects` WHERE `ID`=" . $_POST['id'] . ";";
        $result = $connection->query($sql);
        $projectName = $result->fetch_assoc()['Name'];
        $managerUID = $result->fetch_assoc()['managerUID'];

        $sql = "SELECT `firstName`, `lastName` FROM `users` WHERE `idUsers`=" . $managerUID . ";";
        $result = $connection->query($sql);
        $managerName = $result->fetch_assoc()['lastName'] . " " . $result->fetch_assoc()['firstName'];

        $connection->close();

        //E-mail küldése a felhasználónak
        $message = '
                    <html>
                    <head>
                    <title>Arpad Media IO</title>
                    </head>
                    <body>
                    <h3>Kedves ' . $name . '!</h3>
                    <p>Hozzá lettél adva a "' . $projectName . '" projekthez.</p>

                    <i>Ez egy tájékoztató üzenet, kérlek ne válaszolj rá!</i>
                    <h5>Projekt vezető: <br> ' . $managerName . '</h5>
                    </body>
                    </html>
                    ';
        $subject = "Hozzá lettél adva a " . $projectName . " projekthez.";

        self::sendMail($email, $subject, $message);
    }

    public static function sendProjectDeadlineMailToAll($project_id)
    {
        $connection = Database::runQuery_mysqli();

        // Get the project name and deadline
        $sql = "SELECT `Name`, `Deadline` FROM `projects` WHERE `ID`=" . $project_id . ";";
        $result = $connection->query($sql);
        $project = $result->fetch_assoc();
        $projectName = $project['Name'];
        $deadline = $project['Deadline'];

        // Get the members of the project
        $sql = "SELECT `idUsers` FROM `projectmembers` WHERE `ProjectID`=" . $project_id . ";";
        $result = $connection->query($sql);
        $members = $result->fetch_all();

        $subject = "Projekt határidő emlékeztető";

        // Get current time
        $currentTime = date("Y-m-d H:i:s");
        if ($currentTime < $deadline) {
            $message = "A(z) " . $projectName . " projekt a határideje " . $deadline . ".\n\n";
        } else {
            $message = "A(z) " . $projectName . " projekt határideje lejárt.\n\n";
        }

        // Send an email to all members
        foreach ($members as $member) {
            $sql = "SELECT `email`, `firstName` FROM `users` WHERE `idUsers`=" . $member[0] . ";";
            $result = $connection->query($sql);
            $email = $result->fetch_assoc()['email'];
            $name = $result->fetch_assoc()['firstName'];

            $message .= "Kedves " . $name . "! \n" . $message;
            self::sendMail($email, $subject, $message);
        }

        $connection->close();
    }

}