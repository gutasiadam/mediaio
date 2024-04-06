<?php
namespace Mediaio;

require_once '../server/synologyCommunication.php';

use Mediaio\synologyAPICommunicationManager;


class NasCommunication
{
    private $apiConnection = null;

    function __construct()
    {
        $this->apiConnection = new synologyAPICommunicationManager();
    }

    function getRootFolder($path = "/")
    {
        if ($this->apiConnection->getSid() == null) {
            $this->apiConnection->obtainSID();
        }

        $url = '/webapi/entry.cgi?api=SYNO.FileStation.List&version=2&method=list_share&additional=%5B%22real_path%22%2C%22owner%2Ctime%22%5D';
        //$url = "/webapi/entry.cgi?api=SYNO.FileStation.List&version=2&method=list&additional=%5B%22owner%22%2C%22time%22%2C%22perm%22%2C%22type%22%5D&folder_path=" . urlencode($path);
        $response = $this->apiConnection->runRequest($url, array(), "GET");
        return $response;
    }

    // Deconstructor which logs out the user
    function __destruct()
    {
        $this->apiConnection->logout();
    }
}


$nas = new NasCommunication();

if (isset($_GET['mode'])) {
    switch ($_GET['mode']) {
        case 'getRootFolderData':
            echo $nas->getRootFolder();
            break;
    }
    exit();
}