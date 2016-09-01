<?php 
require_once ('ScribdService.php');

// Report simple running errors
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//we create the object
$scribdService = ScribdService::getInstance();

$filePathAbsolute = "C:/xampp/htdocs/scribd/fileexample/parlita01.txt"; //replace this and put the absolute filepath to be uploaded from

//we try to upload an example file, remember to set uo the access key and secret key from scribd otherwise will not be working.
$result = $scribdService->uploadScribdService($filePathAbsolute, ['sDocType'=>'txt','sAccess'=>'private','iRevId'=>null]);

echo "El documento subido tiene el identificador" . $result["doc_id"];

?>
