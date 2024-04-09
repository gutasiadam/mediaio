<?php
namespace Mediaio;

require_once '../server/synologyCommunication.php';


class NasCommunication extends synologyAPICommunicationManager
{
    private $ProjectrootFolder = null;

    function __construct()
    {
        synologyAPICommunicationManager::__construct();
    }

    function checkLogin()
    {
        if (synologyAPICommunicationManager::getSid() == null) {
            synologyAPICommunicationManager::obtainSID();
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
        $response = synologyAPICommunicationManager::runRequest($url, array(), "GET");
        return $response;
    }

    function getLink($path)
    {
        $this->checklogin();

        if ($path == null) {
            return 500;
        }

        $url = '/webapi/entry.cgi?api=SYNO.FileStation.Sharing&version=3&method=create&path=' . urlencode($path);// . '&additional=%5B%22real_path%22%2C%22owner%2Ctime%22%5D';
        $response = synologyAPICommunicationManager::runRequest($url, array(), "GET");
        return $response;
    }


    function downloadFile($path)
    {
        $this->checklogin();

        if ($path == null) {
            return 500;
        }
        
        return synologyAPICommunicationManager::downloadReq($path);
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
    function logout()
    {
        synologyAPICommunicationManager::__destruct();
    }
}


$nas = null;

if (isset($_GET['mode'])) {
    switch ($_GET['mode']) {
        case 'setRootFolder':
            $nas = new NasCommunication();
            $nas->setRootFolder($_GET['path']);
            $nas->logout();
            echo '200';
            exit();
        case 'listDir':
            $nas = new NasCommunication();
            echo $nas->listDir($_GET['path']);
            $nas->logout();
            exit();
        case 'getLink':
            $nas = new NasCommunication();
            echo $nas->getLink($_GET['path']);
            $nas->logout();
            exit();
        case 'downloadFile':
            $nas = new NasCommunication();
            echo $nas->downloadFile($_GET['path']);
            break;
        case 'logout':
            $nas->logout();
            echo '200';
            exit();
    }
}