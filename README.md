Owly API PHP Client Library - owly-api-php
===========================================

A PHP Class to interface with the Owly API to generate short trackable URLs

Report all bugs/features at
http://redmine.invokemedia.com/projects/owly-api-client-php

What is Ow.ly?
==============

A URL Shortener service which is heavily used by Hootsuite.

Generating an API Key
=====================

Ow.ly is completely separated from HootSuite. The only thing that an API key can be associated with is a Twitter account.

To generate an api key
Log in to ow.ly using a twitter account
then go to:  http://ow.ly/user/{your_twitter_username}?t=apikey - this will auto-generate an API key that will be tied to the Twitter account.

Requirements
============

PHP 5 (5.3 ideally)
PHP Curl extension

Usage
=====

Currently the API methods that have been implemented in this class
- url/shorten

<pre>
$longUrl = 'http://www.github.com';
$owly = OwlyApi::factory(array('key' => 'Your Owly API Key'));
try {
	$url = $owly->shorten($longUrl);
} catch(Exception $e) {
	// Catch any API errors here
}
</pre>
 
The class heavily utilizes exceptions to throw any errors it gets from the API. 
It is highly recommended to do a try-catch wrapper when using any of the methods.

Ow.ly API Documentation
-----------------------

Documentation of the actual REST-based API can be found in the url below
http://ow.ly/api-docs

Tips & Best Practice
====================

1. Always cache/save the generated ow.ly URL on your end. Try to NOT generate the URL at every request as the service is not 100% reliable. Attempting to generate for the same URL over and over again can result in a HTTP ERROR 500.