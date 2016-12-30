<?php

require_once ("common.php");

session_start();
$albumId = $_GET['album_id'];
$userId = $_GET['user_id'];
$allFlag = $_GET['all'];

// update the second argument to be CompanyName-ProductName-Version
$gp = new Zend_Gdata_Photos();

$query = $gp->newAlbumQuery();

$query->setUser($userId);
$query->setAlbumId($albumId);

$albumFeed = $gp->getAlbumFeed($query);
header("Content-Type: text/html; charset=utf-8");
foreach ($albumFeed as $albumEntry) {
	getPhoto($albumEntry, $allFlag);
    //echo $albumEntry->title->text . "<br />\n";
}

?>
</body>
</html>