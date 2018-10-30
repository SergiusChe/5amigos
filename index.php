<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html  lang="ru">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Пять друзей</title>
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
	<style>
		body {
			background: #edeef0;
		}
		.img {
			width: 50px;
			height: 50px;
			border-radius: 50%;
		}
		.center-block {
    			width: 50%;
		}
		.well {
			padding: 5px;
			margin: 5px;	
		}
	</style>
</head>
<body>	
<?php
	
	$app = new App('6736509', 'YbBGWl9TBho3FUekCXak');
	$app->run();

	class App
	{
		public $clientId;
		public $clientSecret;
		public $redirectUri;
		public $authQuery;
		public $logoutQuery;
		public $token;

		public function __construct($clientId, $clientSecret)
		{
			$this->clientId = $clientId;
			$this->clientSecret = $clientSecret;
			$this->redirectUri = "http://$_SERVER[HTTP_HOST]$_SERVER[SCRIPT_NAME]";
			$params = array(
				'client_id' => $this->clientId,
				'display' => 'page',
				'redirect_uri' => $this->redirectUri,
				'scope' => 'friends',
				'response_type' => 'code',
				'v' => '5.87'
			);
			$this->authQuery = 'https://oauth.vk.com/authorize' . '?' . urldecode(http_build_query($params));
			$this->logoutQuery = $this->redirectUri . "?logout=1";
		}

		public function run()
		{
			try 
			{
				if (isset($_GET['logout']))
				{
					setcookie('token', "", time() - 100);
					setcookie('uid', "", time() - 100);

					$authQuery = $this->authQuery;        	
					require_once 'viewAuth.php';
				}
				elseif (isset($_COOKIE['token']))
				{
					$this->token = array (
						'access_token' => $_COOKIE['token'],
						'user_id' => $_COOKIE['uid']
					);

					$user = $this->mdlUser();
					$friends = $this->mdlFriends();
					$logoutQuery = $this->logoutQuery;
					require_once 'viewMain.php';
				}
				elseif (isset($_GET['code']))
				{
					$this->token = $this->mdlToken();
					setcookie('token', $this->token['access_token'], time() + $this->token['expires_in']);
					setcookie('uid', $this->token['user_id'], time() + $this->token['expires_in']);
			
					$user = $this->mdlUser();
					$friends = $this->mdlFriends();
					$logoutQuery = $this->logoutQuery;
					require_once 'viewMain.php';
				}
				else
				{
					$authQuery = $this->authQuery;
					require_once 'viewAuth.php';
				}
			} 
			catch (Exception $e)
			{
				echo $e->getMessage();
			}
		}

		public function mdlUser()
		{
			$params = array(
				'user_ids' => $this->token['user_id'],
				'access_token' => $this->token['access_token'],
				'lang' => 'ru',
				'v' => '5.87'
			);
			$result = @json_decode(get_curl('https://api.vk.com/method/users.get' . '?' . urldecode(http_build_query($params))), true);
			if (!isset($result['response'][0])) throw new Exception('Не могу получить данные пользователя!');
			return $result['response'][0];
		}

		public function mdlToken()
		{
			$params = array(
				'client_id' => $this->clientId,
				'client_secret' => $this->clientSecret,
				'code' => $_GET['code'],
				'redirect_uri' => $this->redirectUri
			);
			$result = @json_decode(get_curl('https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query($params))), true);
			if (!isset($result['access_token'])) throw new Exception('Не могу получить токен!');
			return $result;
		}
		
		public function mdlFriends()
		{
			$params = array(
				'access_token' => $this->token['access_token'],
				'user_id' => $this->token['user_id'],
				'order' => 'random',
				'count' => 5,
				'fields' => 'photo_100',
				'v' => '5.87'
			);
			$result = @json_decode(get_curl('https://api.vk.com/method/friends.get' . '?' . urldecode(http_build_query($params))), true);
			if (!isset($result['response'])) throw new Exception('Не могу получить список друзей!');
			return $result['response'];
		}
	}

	function get_curl($url)
	{
		if(function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$output = curl_exec($ch);
			echo curl_error($ch);
			curl_close($ch);
			return $output;
		}
		else
		{
			return file_get_contents($url);
		}
	}
?>
</body>
</html>
