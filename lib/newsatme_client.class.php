<?php

class NewsAtMe_ClientException extends Exception {}

class NewsAtMe_Client {
  const API_VERSION = 'v1';
  const BASE_URL = 'https://app.newsatme.com/'; 

  var $api;
  var $output;

  function NewsAtMe_Client($api) { $this->__construct($api); }

  function __construct($api) {
    if ( empty($api) ) throw new NewsAtMe_ClientException('Invalid API key');
    try {
      $this->api = $api;
      $response = $this->ping($api);

      if (!$response) {
        throw new NewsAtMe_ClientException('Invalid API key');
      }
    } catch ( Exception $e ) {
      throw new NewsAtMe_ClientException($e->getMessage());
    }
  }

  public function getTags() {
    return $this->request('tags', array(), 'GET');
  }

  public function ping($api_key) {
    $ret = $this->request('ping', array(), 'GET' );
    if ($ret !== false) {
      return $ret['PING'] == 'PONG!';
    } else {
      return false;
    }
  }

  public function getSite() {
    $response = $this->request("site", array(), 'GET'); 
    return $response; 
  }

  public function checkArticleSignature($id, $signature) {
    $response = $this->request("articles/$id/signature", array(), 'GET');
    return $response['signature'] == $signature;
  }

  public function saveArticle($newsatme_post) {
    return $this->request('articles', array(
      'article' => $newsatme_post->attributes_with_signature(),
    ));
  }

  public function deleteArticle($remote_id) {
    return $this->request('articles/' . $remote_id, array('_method' => 'delete'), 'POST');
  }

  public function saveSubscription($email, $id, $title, $url, $tags, $dem_authorized) {
    $response = $this->request('subscription', array(
      'subscription' => array(
        'email' => $email,
        'title' => $title,
        'remote_id' => $id,
        'article_url' => $url,
        'tags_array' => explode(',', $tags),
        'dem_authorized' => $dem_authorized
        )
      )
    );

    return 'ok' == $response['status'] ;
  }

	/**
	 *
	 * @param string $method API method name
	 * @param array $args query arguments
	 * @param string $http GET or POST request type
	 * @param string $output API response format (json,php,xml,yaml). json and xml are decoded into arrays automatically.
	 * @return array|string|NewsAtMe_ClientException
	 */
	function request($method, $args = array(), $http = 'POST', $output = 'json') {
    $this->output = $output;

		$api_version = self::API_VERSION;
		$dot_output = ('json' == $output) ? '' : ".{$output}";

		$url = self::BASE_URL . "api/{$api_version}/{$method}{$dot_output}";

		switch ($http) {
    case 'GET':
      //some distribs change arg sep to &amp; by default
      $sep_changed = false;
      if (ini_get("arg_separator.output")!="&"){
        $sep_changed = true;
        $orig_sep = ini_get("arg_separator.output");
        ini_set("arg_separator.output", "&");
      }

      $url .= '?' . http_build_query($args);

      if ($sep_changed) {
        ini_set("arg_separator.output", $orig_sep);
      }

      $response = $this->http_request($url, array(),'GET');
      break;

    case 'POST':
      $response = $this->http_request($url, $args, 'POST');
      break;

    default:
      throw new NewsAtMe_ClientException('Unknown request type');
    }

		$response_code  = $response['header']['http_code'];
		$body           = $response['body'];

    switch ($output) {

    case 'json':
      $body = json_decode($body, true);
      break;

    case 'php':
      $body = unserialize($body);
      break;
    }

		if ( 200 == $response_code || 201 == $response_code ) {
			return $body;
		} else {
			throw new NewsAtMe_ClientException( "HTTP Code $response_code: $url", $response_code);
		}
	}

  function http_request($url, $fields = array(), $method = 'POST') {

    if ( !in_array( $method, array('POST','GET') ) ) $method = 'POST';
    if ( !isset( $fields['auth_token']) ) $fields['auth_token'] = $this->api;

    //some distribs change arg sep to &amp; by default
    $sep_changed = false;
    if (ini_get("arg_separator.output")!="&"){
      $sep_changed = true;
      $orig_sep = ini_get("arg_separator.output");
      ini_set("arg_separator.output", "&");
    }

    $fields = is_array($fields) ? http_build_query($fields) : $fields;

    if ($sep_changed) {
      ini_set("arg_separator.output", $orig_sep);
    }

    if ( function_exists('curl_init') && function_exists('curl_exec') ) {
      if ( !ini_get('safe_mode') ) {
        set_time_limit(2 * 60);
      }

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);

      if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, $method == 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
      }

      $header = array("Expect:", "X-Newsatme-Auth: " . $this->api); 

      curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2 * 60 * 1000); 

      $response   = curl_exec($ch);
      $info       = curl_getinfo($ch);
      $error      = curl_error($ch);

      curl_close($ch);

    } else {
      throw new NewsAtMe_ClientException("No valid HTTP transport found", -99);
    }

    return array('header' => $info, 'body' => $response, 'error' => $error);
  }
}

?>
