<?php

require_once ("common.php");

/*
$serviceName = Zend_Gdata_Photos::AUTH_SERVICE_NAME;
$user = "your_user_id";
$pass = "your_user_passwd";

$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $serviceName);

var_dump($client);
die();
*/

session_start();
//$albumId = $_GET['album_id'];
$userId = $_GET['user_id'];

// update the second argument to be CompanyName-ProductName-Version
$gp = new Zend_Gdata_Photos();

try {
    $userFeed = $gp->getUserFeed($userId);
    foreach ($userFeed as $albumEntry) {
		$qStr = array();
		echo $albumEntry->id->text . "<br />";
		$albumId = getAlbumIdFromUri($albumEntry->id->text);
		$qStr['album_id'] = $albumId;
		$qStr['user_id'] = $userId;
		$qStr['all'] = 'N';
		echo '<a href="' . getHtmlPageUrl($qStr) . '" target="_blank">' . $albumEntry->title->text . '</a><br />';
		$qStr['all'] = 'Y';
		echo '<a href="' . getHtmlPageUrl($qStr) . '" target="_blank">' . $albumEntry->title->text . '(ALL)</a><br />';
    }
} catch (Zend_Gdata_App_HttpException $e) {
    echo "Error: " . $e->getMessage() . "<br />\n";
    if ($e->getResponse() != null) {
        echo "Body: <br />\n" . $e->getResponse()->getBody() . 
             "<br />\n"; 
    }
    // In new versions of Zend Framework, you also have the option
    // to print out the request that was made.  As the request
    // includes Auth credentials, it's not advised to print out
    // this data unless doing debugging
    // echo "Request: <br />\n" . $e->getRequest() . "<br />\n";
} catch (Zend_Gdata_App_Exception $e) {
    echo "Error: " . $e->getMessage() . "<br />\n"; 
}

die();

$entry = new Zend_Gdata_Photos_AlbumEntry();
$entry->setTitle($gp->newTitle("New album"));
$entry->setSummary($gp->newSummary("This is an album."));

$createdEntry = $gp->insertAlbumEntry($entry);

die();


?>
</body>
</html>