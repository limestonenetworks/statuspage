<?php

class Twitter {
	var $tkey;
	var $tsecret;
	var $oauth_token;
	var $oauth_token_secret;
	var $testing = false;
	var $twitterObj;

	function __construct() {
		global $config;

		if (empty($config['twitter_key']) || empty($config['twitter_secret'])) throw new Exception('Twitter consumer key and secret must be specified in the configuration file');
		if (empty($config['twitter_oauth_token']) || empty($config['twitter_oauth_token_secret'])) throw new Exception('Twitter token and secret must be specified in the configuration file');

		$this->tkey = $config['twitter_key'];
		$this->tsecret = $config['twitter_secret'];
		$this->oauth_token = $config['twitter_oauth_token'];
		$this->oauth_token_secret = $config['twitter_oauth_token_secret'];

		require_once('twitteroauth/twitteroauth.php');
		$this->twitterObj = new TwitterOAuth($this->tkey, $this->tsecret, $this->oauth_token, $this->oauth_token_secret);
	}

	function tweet($message) {
		if (empty($message)) throw new Exception('You must specify a message to tweet');

		if ($this->testing) $message = '(TESTING ONLY) '. $message;
		$message = $this->prepareMessage($message);

		$result = $this->twitterObj->post('statuses/update', array('status' => $message));

		if ($this->testing) echo "Twitter result was:";
		if ($this->testing) print_r($result);
		if ($this->testing) echo "\n";

		if ($resultArray['http_code'] == 200) return true;
		return false;
		
	}

	private function prepareMessage($message, $limit = 137) {
		if ($this->testing) $limit -= 15;

		if (strlen($message) > $limit) {
			$words = str_word_count($message, 2);
			$pos = array_keys($words);
			$message = substr($message, 0, $pos[$limit]) . '...';
		}

		if ($this->testing) echo "Final twitter message: {$message}\n";

		return $message;
	}
}

?>
