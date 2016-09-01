<?php

// Importing  the library will be used to call the service
require_once 'library/Scribd.php';

interface ScribdConfig 
{
     const API_KEY = 'XXXXXXXXXX'; //replace and put here the api key from scribd, you need to create a scribd account
     const SECRET_KEY = 'XXXXXXXXXXXXX'; // replace and put here the secret key from scribd, you need to create a scribd account
}
/**
* @author Sergio Gayarre Garasa <sergiogayarre@gmail.com>
* @desc This class is an intermediate class to connect with ScribdApi Service
*/

class ScribdService {

    public static $oScribdWs = null;
    static $instance = null;
    public $url;
    public $session_key;
    public $my_user_id;
    public $error;
    public static $aAllowedExtensions = ["doc", "docx", "ppt", "pptx", "pps", "ppsx", "xls", "xlsx", "pdf", "ps", "odt", "odp", "sxw", "sxi", "txt", "rtf", "epub"];

   
    // Un constructor privado evita la creación de un nuevo objeto
    private function __construct() {}
 
    // método singleton
    public static function getInstance()
    {
        if (!isset(self::$instance)) 
            self::$instance = new ScribdService();
        
        self::$oScribdWs = new Scribd(ScribdConfig::API_KEY, ScribdConfig::SECRET_KEY); 
        return self::$instance;
    }

 
    public function getScribdWs() {
        //we obtain the object to be able to call the webservice
        return self::$oScribdWs;   
    }

    /**
      * uploadScribdService calls to a REST API to upload a file to Scribd  Webservice
      * @param string $iFilePath: relative path to file, where is the file placed on disk (absolute path)
      * @param Array $aParams=array('sDocType'=>'pdf','sAccess'=>'private','iRevId'=>null)
      * @return array containing doc_id, access_key, and secret_password, for example to acess to a specific attribute aResult['doc_id']. False in case of error.
     */
   
    public function uploadScribdService($sFilePath, $aParams=array()) {
        if ($this->validFormatToScribd($sFilePath)) {
            try {
                $aResult = $this->getScribdWs()->upload($sFilePath, $aParams['sDocType'], $aParams['sAccess'], $aParams['iRevId']);
            } catch(Exception $e) {
                $aResult = false;
            }
        } else {
            $aResult = false;
        }
       
        return $aResult;
    }


    /**
      * changeSettingsScribdService calls to a REST API to Change Setting on Scribd for an specific document id.
      * @param integer $iDoc_id: relative to doc_id, this is value is obtained when file is uploaded.
      * @param Array $aParams=array('title'=>'Zend framework','description'=>'All about Zend','access'=>'public','license'=>'c', 'showads'=>false,'tag'=>'book programming')
      * @return return a blank response object if succesful, otherwise returns null
    */
   
    public function changeSettingsScribdService($iDocId, $aParams = array()) {
       
    /**
      * @var string sTitle: relative to document title
      * @var string sDescription: relative to document description
      * @var string sDescription: relative to accesibility, it could be prublic or private
      * @var string sLicense of document, "by", "by-nc", "by-nc-nd", "by-nc-sa", "by-nd", "by-sa", "c" or "pd"
      * @var boolean bShowAds default, true, or false
      * @var string sTag: relative to tag associatted with the document, for example books
    */
    
        $sParentalAdvisory = null;
        $sTitle     =   $aParams['sTitle']; 
        $sDescription = $aParams["sDescription"];//@param string description of document
        $sAccess =      $aParams['sAccess']; //@param string title of document, public or private
        $sLicense =     $aParams['sLicence']; //@param string license of document, "by", "by-nc", "by-nc-nd", "by-nc-sa", "by-nd", "by-sa", "c" or "pd"
        $bShowAds =     $aParams['bShowAds']; //@param boolean show_ads default, true, or false
        $sTag =         $aParams['sTag']; // @param string tag, for example books
       
          
        $oResult=null;
        if (!empty($iDocId)) 
                $oResult=$this->getScribdWs()->changeSettings($iDocId, $sTitle, $sDescription, $sAccess, $sLicense, $sParentalAdvisory, $bShowAds, $sTag);

        return $oResult;
    }
   
     /**
       * getSettingsScribdService calls to a REST API to get properties from a specific document id.
       * @param integer $iDocId: relative to the id once the file is uploaded via webservice
       * @return array $aResultSettings: relative to array containing doc_id, title , description , access, tags, show_ads, license, access_key, secret_password
     */
   
    public function getSettingsScribdService($iDocId) {
        $aResultSettings=$this->getScribdWs()->getSettings($iDocId);
       
        return  $aResultSettings;
    }
   
    /**
      * getEmbedUrlFlash calls to a REST API to get the url of a flash object in Scribd
      * @param integer $iDocId: relative to doc_id, this is value is obtained when file is uploaded.
      * @param string $aAccessKey: relative to the access_key value is obtained when file is uploaded.
      * @return string url with a flash object
    */
   
    public function getEmbedUrlFlashScribdService($iDocId, $sAccessKey) {
        return $this->getScribdWs()->getEmbedUrlFlash($iDocId, $sAccessKey);
    } 
   
    /**
      * Validates the url of the file when will be uploaded, getting the extension file
      * @param string $sUrlFile relative to url of the file where will be uploaded-
      * @return bool true or false
    */

   
    public function validFormatToScribd($sUrlFile){
       $ext = end(explode('.', $sUrlFile));
       $result = (in_array ($ext, self::$aAllowedExtensions)) ? true : false;
       
       return $result;
    }
   
    /**
      * Return the whole list of doc_id documents uplodaded to Scrib account
      * @return array
    */

    public function getDocList(){
     
      $docs = $this->getScribdWs()->getList();
     
      $docIds = array();
      if (is_array($docs))
        foreach ($docs as $key => $value)
          $docIds[] = $value['doc_id'];

      return $docIds;
    }

    /**
      * Delete the document from the ScribdService
      * @param string $sDoc_id: relative to doc_id, this is value is obtained when file is uploaded.
      * @return array
    */
    public function deleteDoc($sDocId){
        return $this->getScribdWs()->delete( (int)$sDocId );

    }
} 

?>