<?php
/**
 * PHP Client class to interface with Owly's REST-based API 
 * @see http://ow.ly/api-docs
 * 
 * Currently only supports the follow methods
 * shorten - Shorten a URL
 * 
 * Does NOT yet support POST methods
 * 
 * Based on the Zend Ow.ly URL helper by Maxime Parmentier <maxime.parmentier@invokemedia.com>
 * 
 * Support can be logged at https://github.com/invokemedia/owly-api-php
 *
 * @version 	1.0.0
 * @author		Shih Oon Liong <shihoon.liong@invokemedia.com>
 * @created		24/07/2012
 * @copyright	Invoke Media / Biplane
 * @license		http://opensource.org/licenses/mit-license.php MIT
 * 
 * @example example.php
 * 
 */

class OwlyApi {
	const
		BASE_API_URL = '//ow.ly/api/',
		HTTP_METHOD_GET = 'GET',
		HTTP_METHOD_POST = 'POST',
		HTTP_RESPONSE_SUCCESS = 200;
		
	private
		$apiKey = null,
		$version = "1.1",
		$protocol = 'http:',
		$apiCalls = array (
			'url-shorten'		=> 'url/shorten',
			'url-expand'		=> 'url/expand',
			'url-info'			=> 'url/info',
			'url-stats-click'	=> 'url/clickStats',
			'photo-upload'		=> 'photo/upload',
			'doc-upload'		=> 'doc/upload', 
		);
		
	/**
	 * Constructor 
	 * @param array $options An array of options for the class. Possible options
	 *							'key'     - The ow.ly API Key
	 *							'version' - (optional) The version number of the API to use
	 *										By default, it will use version 1.1
	 *							'protocol' - (optional) The protocol to use when talking to
	 *										  the API. By default it will use 'http:'
	 *										  To set it to secure, use 'https:'
	 * @return void
	 */
	public function __construct($options) {
		if ( ! array_key_exists('key', $options) || empty($options['key']) ) {
			throw new Exception('No API Key found');
		}

		$this->apiKey = $options['key'];

		if ( array_key_exists('version', $options) ) {
			$this->version = $options['version'];
		}

		if ( array_key_exists('protocol', $options) ) {
			$this->version = $options['protocol'];
		}
	}
		
	/**
	 * Factory constructor
	 * @see OwlyApi::__constructor
	 * @param type $options
	 * @return OwlyApi 
	 */
	public static function factory($options) {
		$class = __CLASS__;
		$instance = new $class($options);
				
		return $instance;
	}
		
	/**
	 * Get the API Method path to use
	 * @param type $apiCall
	 * @return type 
	 */
	private function getApiMethod($apiCall) {
		$url = $this->protocol . self::BASE_API_URL . $this->version . '/';
		if ( ! empty($apiCall) ) {
			$url .= $this->apiCalls[$apiCall];
		}
				
		return $url;
	}
		
	/**
	 * Given a full URL, returns an ow.ly short URL. 
	 * Currently the API only supports shortening a single URL per API call.
	 * 
	 * @see http://ow.ly/api-docs#shorten
	 * @param type $longUrl The URL to shorten. Must be a valid URL
	 * @return mixed 
	 * @throws Exception 
	 */
	public function shorten($longUrl) {
		$apiUrl = $this->getApiMethod('url-shorten');
		$params = array (
			'longUrl' => $longUrl,
		);
				
		$shortUrl = '';
		try {
			$response = $this->send($apiUrl, $params);
			$shortUrl = $response->shortUrl;
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
				
		return $shortUrl;
    }
		
	/**
	 * Do an API call on Ow.ly
	 * @param type $apiUrl
	 * @return mixed
	 * @throws Exception 
	 */
	private function send($apiUrlCall, $args=array(), $method='GET') {
			
		// Add API Key
		$args['apiKey'] = $this->apiKey;
			
		if ($method == self::HTTP_METHOD_GET) {
			$apiUrl = $apiUrlCall . '?' . http_build_query($args);
		}

		try {
			$ch = curl_init();

			// Set CURL options
			curl_setopt($ch, CURLOPT_URL, $apiUrl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$response = curl_exec($ch);
			$metadata = curl_getinfo($ch);

			if ( $response === false ) {
					throw new Exception('Invalid Response received');
			}

			if( array_key_exists('http_code', $metadata) 
					&& (int)$metadata['http_code'] == self::HTTP_RESPONSE_SUCCESS ) {
				$data = json_decode($response);
				curl_close($ch);
				return $data->results;
			} else {
				$errorMsg = 'Response error - Owly API Error encountered: ' 
						  . '[' . $metadata['http_code'] . '] '
						  . '(Request: ' . $apiUrl . ') ';
				if ( ! empty($response) ) {
					$data = json_decode($response);
					$errorMsg .= $data->error;
				}

				throw new Exception($errorMsg);
			}
		} catch (Exception $e) { 
				throw new Exception($e->getMessage());
		}
	}
}



