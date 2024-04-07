<?php
namespace Mediaio;

require_once '../server/synologyCommunication.php';

use Mediaio\synologyAPICommunicationManager;


class NasCommunication
{
    private $apiConnection = null;
    private $ProjectrootFolder = null;

    function __construct()
    {
        $this->apiConnection = new synologyAPICommunicationManager();
    }

    function checkLogin()
    {
        if ($this->apiConnection->getSid() == null) {
            $this->apiConnection->obtainSID();
        }
    }

    function setRootFolder($path)
    {
        $this->ProjectrootFolder = $path;
    }

    function listDir($path)
    {
        $this->checklogin();

        if ($path == null) {
            return 500;
        }


        $url = '/webapi/entry.cgi?api=SYNO.FileStation.List&version=2&method=list&folder_path=' . urlencode($path) . '&additional=%5B%22real_path%22%2C%22owner%2Ctime%22%5D';
        $response = $this->apiConnection->runRequest($url, array(), "GET");
        return $response;
    }

    function downloadFile($path)
    {
        $this->checklogin();

        if ($path == null) {
            return 500;
        }

        $url = '/webapi/entry.cgi?api=SYNO.FileStation.Download&version=2&method=download&path=' . urlencode($path). '&mode=download';
        $response = $this->apiConnection->runRequest($url, array(), "GET");
        
        return $response;
    }

/*     function uploadFile($path)
    {
        $this->checklogin();

        if ($path == null) {
            return 500;
        }

        $url = '/webapi/entry.cgi?api=SYNO.FileStation.Upload&version=2&method=upload&path=' . $path;
        $response = $this->apiConnection->runRequest($url, array(), "GET");
        return $response;
    } */

    // Deconstructor which logs out the user
    function __destruct()
    {
        $this->apiConnection->logout();
    }
}


$nas = new NasCommunication();

if (isset($_GET['mode'])) {
    switch ($_GET['mode']) {
        case 'setRootFolder':
            $nas->setRootFolder($_GET['path']);
            echo '200';
            break;
        case 'listDir':
            echo $nas->listDir($_GET['path']);
            break;
        case 'downloadFile':
            echo $nas->downloadFile($_GET['path']);
            break;
    }
    exit();
}