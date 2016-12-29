<?php
if (isset($_GET["regId"]) && isset($_GET["message"])) {
    $regId = $_GET["regId"];
    $message = $_GET["message"];
     //var_dump($_GET);
	 
    include_once './GCM.php';
     
    $gcm = new GCM();
 
    $registatoin_ids = array($regId);
    $message = array("price" => $message);
 
    $result = $gcm->send_notification($registatoin_ids, $message);
 
    echo $result;
}else{
	echo 'in else';
}
?>