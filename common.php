<?php

$slash = (strstr(ini_get('extension_dir'), '/'))?"/":"\\"; //Windows 與Unix 的斜線方向不同，需要考慮到
$includePath = dirname(__FILE__).$slash.'library';
ini_set('include_path', $includePath); //動態設定php.ini
//這邊是在設定程式把Zend Gdata Library 載入程式碼中
/*
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');
*/

require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_Photos');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');


function getCurrentUrl(){
	return sprintf("http://%s/test/zendGdata.php",$_SERVER["HTTP_HOST"]);
}

function getHtmlPageUrl($qStr){
	if(count($qStr) > 0){
		$queryString = '?' . http_build_query($qStr);
	}
	return sprintf("http://%s/test/zendHtmlGdata.php",$_SERVER["HTTP_HOST"]) . $queryString;
}

function getAuthSubUrl()
{
    // the $next variable should represent the URL of the PHP script 
    // an example implementation for getCurrentUrl is in the sample code
    $next = getCurrentUrl(); 
    $scope = 'http://picasaweb.google.com/data';
    $secure = false;
    $session = true;
    return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure,
        $session);
}

function getAuthSubHttpClient()
{
    if (!isset($_SESSION['sessionToken']) && !isset($_GET['token']) ){
        echo '<a href="' . getAuthSubUrl() . '">Login!</a>';
        exit;
    } else if (!isset($_SESSION['sessionToken']) && isset($_GET['token'])) {
        $_SESSION['sessionToken'] =
            Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
    }
    $client = Zend_Gdata_AuthSub::getHttpClient($_SESSION['sessionToken']);
    return $client;
}

// In version 1.5+, you can enable a debug logging mode to see the
// underlying HTTP requests being made, as long as you're not using
// a proxy server
// $gp->enableRequestDebugLogging('/tmp/gp_requests.log');

function getAlbumIdFromUri($uri){
	$uriAry = explode("/", $uri);
	return end($uriAry);
}

function getPhoto($photoEntry, $isPrint = true){
	$camera = "";
	$contentUrl = "";
	$firstThumbnailUrl = "";

	$albumId = $photoEntry->getGphotoAlbumId()->getText();
	$photoId = $photoEntry->getGphotoId()->getText();

	if ($photoEntry->getExifTags() != null && 
		$photoEntry->getExifTags()->getMake() != null &&
		$photoEntry->getExifTags()->getModel() != null) {

		$camera = $photoEntry->getExifTags()->getMake()->getText() . " " . 
				  $photoEntry->getExifTags()->getModel()->getText();
	}

	if ($photoEntry->getMediaGroup()->getContent() != null) {
	  $mediaContentArray = $photoEntry->getMediaGroup()->getContent();
	  $contentUrl = $mediaContentArray[0]->getUrl();
	}

	$desc = $photoEntry->getMediaGroup()->getDescription();

	if ($photoEntry->getMediaGroup()->getThumbnail() != null) {
	  $mediaThumbnailArray = $photoEntry->getMediaGroup()->getThumbnail();
	  $firstThumbnailUrl = $mediaThumbnailArray[0]->getUrl();
	}

	if(trim($desc) != ""){
		//echo "AlbumID: " . $albumId . "<br />\n";
		//echo "PhotoID: " . $photoId . "<br />\n";
		//echo "Camera: " . $camera . "<br />\n";
		//echo "Content URL: " . $contentUrl . "<br />\n";
		//echo "First Thumbnail: " . $firstThumbnailUrl . "<br />\n";
		if($isPrint === true){
			echo "<br />\n";
			echo "<img src=\"" . str_replace("s72","s800",$firstThumbnailUrl) . "\" title=\"" . $desc . "\" ><br />\n";
			echo "Description: " . $desc . "<br />\n";
			echo "<br />\n"; 
		}
	}
}
