<?php
namespace Mediaio;
require_once '../server/synologyCommunication.php';

//error_reporting(E_ERROR | E_PARSE);

class nasCommunication extends synologyAPICommunicationManager
{
   private $ProjectrootFolder = null;

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

   function logout()
   {
      synologyAPICommunicationManager::logout();
   }

}

session_start();

if (!isset($_SESSION['nas']) || $_SESSION['nas'] == null) {
   $_SESSION['nas'] = new nasCommunication();
} 


if (isset($_GET['mode'])) {
   switch ($_GET['mode']) {
      case 'setRootFolder':
         $_SESSION['nas']->setRootFolder($_GET['path']);
         echo '200';
         break;
      case 'listDir':
         echo $_SESSION['nas']->listDir($_GET['path']);
         break;
      case 'getLink':
         echo $_SESSION['nas']->getLink($_GET['path']);
         break;
      case 'downloadFile':
         echo $_SESSION['nas']->downloadFile($_GET['path']);
         break;
   }
   exit();
}

