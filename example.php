<?php
	/**
	 * Example file on how to use the Owly PHP API Client 
	 */
	require_once('OwlyApi.php');
	$owly = OwlyApi::factory( array('key' => '{my-api-key}') );
	$sourceUrl = 'http://invokemedia.com/';
	
	try {
		$shortenedUrl = $owly->shorten($sourceUrl);
	} catch(Exception $e) {
		echo 'Error found in API:' . $e->getMessage() . "<br />/n";
		$shortenedUrl = "";
	}
	echo 'Shortened URL:' . $shortenedUrl . "<br />/n";
?>
