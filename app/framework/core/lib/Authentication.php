<?php


class Authentication extends AuthenticationQueries{

	private $pdo, $config;

	function __construct()
	{
		$this->config 	= Config::getInstance(appcore\APP_CONFIG);
		$this->pdo 		= Database::getInstance();
	}

	public function isAuthenticated()
	{
		if ( ! (array_key_exists('APP_USER_ID', $_SESSION) && array_key_exists('APP_USER_LOGGED_IN', $_SESSION) && $_SESSION['APP_USER_LOGGED_IN'] === true) )
		{
			return false;
		}

		if ( $this->session_expired($_SESSION['APP_SESSION_TOKEN']) )
		{
			return false;
		}

		return true;
	}

	public function authenticate($user_email, $password)
	{
		$hash 				= $this->getPasswordHash($user_email);
		$session_token		= sha1($_SERVER['HTTP_USER_AGENT'] . time() . $user_email);

		if ( ! $hash ) //User does not exist
			return false;

		if ( crypt($password, $hash) === $hash )
		{
			//Correct password, log user in
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}

			$_SESSION['APP_USER_ID']			= $this->getUserId($user_email);
			$_SESSION['APP_USER_LOGGED_IN']		= true;
			$_SESSION['APP_SESSION_TOKEN']		= $session_token;
			$this->saveSession($_SESSION['APP_USER_ID'], $session_token)

			return true;
		}

		return false;
	}

	private function session_expired($session_token)
	{
		$stmt	= $this->pdo()->prepare($this->queries[__FUNCTION__]);

		$stmt->bindParam('session_token', $session_token);

		if ( ! $stmt->execute() )
		{
			throw new AppRuntimeException("MySQL ERROR Unable to fetch session token. {$stmt->errorInfo()[1]}");
		}

		$result = $stmt->fetch(PDO::FETCH_COLUMN);

		if ( ! strlen($result) )
		{
			return true;
		}

		if ( time() - strtotime($result) > 60*60*24 )
		{
			return true;
		}

		return false;
	}

	private function saveSession($id, $session_token)
	{
		$stmt	= $this->pdo()->prepare($this->queries[__FUNCTION__]);
		$stmt->bindParam(':id', $id);
		$stmt->bindParam(':session_token', $session_token);

		if ( ! $stmt->execute() )
		{
			throw new AppRuntimeException("MySQL ERROR Unable to save session token. {$stmt->errorInfo()[1]}");
		}
	}

	public function getUserId($user_email)
	{
		$stmt	= $this->pdo()->prepare($this->queries[__FUNCTION__]);
		$stmt->bindParam(':user_email', $user_email);

		if ( ! $stmt->execute() )
		{
			throw new AppRuntimeException("MySQL ERROR Unable to fetch user id user {$user_email}. {$stmt->errorInfo()[1]}");
		}

		$id = $stmt->fetch(PDO::FETCH_COLUMN);

		if ( ! strlen($id) )
		{
			return false;
		}

		return $id;
	}

	public function createUser($user_email, $password)
	{
		//Check that email isn't already registered
		$id 		= $this->getUserId($user_email);

		if ( $id !== false )
		{
			throw new CreateUserException("There is already an account registered with that email");
		}

		if ( strlen($password) < 8 )
		{
			throw new CreateUserException("Password MUST be at least 8 characters long");
		}

		if ( ! strlen(preg_replace('/[^0-9]/', '', $password)) )
		{
			throw new CreateUserException("Password MUST cointainer at least 1 number");
		}

		$salt 		= $this->generateBlowfishSalt();
		$pwd_hash	= crypt($password, $salt);
		$stmt		= $this->pdo()->prepare($this->queries[__FUNCTION__]);

		$stmt->bindParam(':user_email', $user_email);
		$stmt->bindParam(':password_hash', $pwd_hash);

		if ( ! $stmt->execute() )
		{
			throw new AppRuntimeException("MySQL Error! Unable to create new user. {$stmt->errorInfo()[1]}");
		}

		return $this->pdo()->lastInsertId();
	}

	public function updateEmail($old_email, $new_email)
	{
		//Does account exist?
		if ( $this->getUserId($old_email) !== false )
		{
			throw new AppRuntimeException("This email address is not registered!");
		}

		$stmt = $this->pdo()->prepare($this->queries[__FUNCTION__]);
		$stmt->bindParam(':new_user_email', $new_email);
		$stmt->bindParam(':user_email', $old_email);

		if ( ! $stmt->execute() )
		{
			throw new AppRuntimeException("MySQL Error! Unable to create new user. {$stmt->errorInfo()[1]}");
		}

		return true;
	}

	public function endSession()
	{
		unset($_SESSION['APP_USER_ID']);
		unset($_SESSION['APP_USER_LOGGED_IN']);
		unset($_SESSION['APP_SESSION_TOKEN']);

		return true;
	}

	private function getPasswordHash($user_email)
	{
		$stmt	= $this->pdo()->prepare($this->queries[__FUNCTION__]);
		$stmt->bindParam(':user_email', $user_email);

		if( ! $stmt->execute() )
		{
			throw new AppRuntimeException("Unable to grab hash. Query failed to execute: " . $stmt->errorInfo()[1]);
		}

		$result = $stmt->fetch(PDO::FETCH_COLUMN);

		if ( ! strlen($result) )
			return false;

		return $result;
	}

	private function generateBlowfishSalt()
	{
	    $salt 		= "";
	    $salt_chars = array_merge(range('A','Z'), range('a','z'), range(0,9));

	    for($i=0; $i < 22; $i++) { 
	    	//We need 22 aplhanumeric characters to trigger cryp to use Blowfish encryption
			$salt = $salt . $salt_chars[array_rand($salt_chars)];
	    }

	    return sprintf('$2a$%02d$', 10) . $salt;
	}
}