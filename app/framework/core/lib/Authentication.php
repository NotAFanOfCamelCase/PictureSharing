<?php


class Authentication extends AuthenticationQueries {

	private $pdo, $config;

	function __construct()
	{
		parent::__construct(); //Setup queries
		$this->config 	= Config::getInstance(appcore\APP_CONFIG);
		$this->pdo 		= PDOFactory::getInstance();
	}

	public function isAuthenticated()
	{
		$this->start_session();

		if ( ! (array_key_exists('APP_USER_ID', $_SESSION) && array_key_exists('APP_SESSION_TOKEN', $_SESSION) && array_key_exists('APP_USER_LOGGED_IN', $_SESSION) && $_SESSION['APP_USER_LOGGED_IN'] === true) )
		{
			return false;
		}

		if ( $this->session_expired($_SESSION['APP_SESSION_TOKEN'], $_SESSION['APP_USER_ID']) )
		{
			return false;
		}

		return true;
	}

	public function getCurrentId()
	{
		if ( isset($_SESSION['APP_USER_ID']) )
		{
			return $_SESSION['APP_USER_ID'];
		}

		return null;
	}

	public function authenticate($user_email, $password, $http_user_agent='nada')
	{
		$hash 				= $this->getPasswordHash($user_email);
		$session_token		= sha1($http_user_agent . time() . $user_email);

		if ( ! $hash ) //User does not exist
			return false;

		if ( crypt($password, $hash) === $hash )
		{

			$this->start_session();

			$_SESSION['APP_USER_ID']			= $this->getUserId($user_email);
			$_SESSION['APP_USER_LOGGED_IN']		= true;
			$_SESSION['APP_SESSION_TOKEN']		= $session_token;
			$this->saveSession($this->getUserId($user_email), $session_token);

			return true;
		}

		return false;
	}

	private function session_expired($session_token, $user_id)
	{
		$stmt	= $this->pdo->prepare($this->queries[__FUNCTION__]);

		$stmt->bindParam(':session_token', $session_token);
		$stmt->bindParam(':user_id', $user_id);

		if ( ! $stmt->execute() )
		{
			throw new AppRuntimeException("MySQL ERROR Unable to fetch session token. {$stmt->errorInfo()[2]}");
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
		$stmt	= $this->pdo->prepare($this->queries[__FUNCTION__]);
		$stmt->bindParam(':id', $id);
		$stmt->bindParam(':session_token', $session_token);

		if ( ! $stmt->execute() )
		{
			throw new AppRuntimeException("MySQL ERROR Unable to save session token. {$stmt->errorInfo()[2]}");
		}
	}

	public function getUserId($user_email)
	{
		$stmt	= $this->pdo->prepare($this->queries[__FUNCTION__]);
		$stmt->bindParam(':user_email', $user_email);

		if ( ! $stmt->execute() )
		{
			throw new AppRuntimeException("MySQL ERROR Unable to fetch user id user {$user_email}. {$stmt->errorInfo()[2]}");
		}

		$id = $stmt->fetch(PDO::FETCH_COLUMN);

		if ( ! strlen($id) )
		{
			return false;
		}

		return $id;
	}

	public function createUser($user_email, $password, $admin=false)
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
		$stmt		= $this->pdo->prepare($this->queries[__FUNCTION__]);

		$stmt->bindParam(':user_email', $user_email);
		$stmt->bindParam(':password_hash', $pwd_hash);

		if ( ! $stmt->execute() )
		{
			throw new AppRuntimeException("MySQL Error! Unable to create new user. {$stmt->errorInfo()[2]}");
		}

		return $this->pdo->lastInsertId();
	}

	public function updateEmail($id, $new_email)
	{
		$stmt = $this->pdo->prepare($this->queries[__FUNCTION__]);
		$stmt->bindParam(':new_user_email', $new_email);
		$stmt->bindParam(':id', $id);

		if ( ! $stmt->execute() )
		{
			throw new AppRuntimeException("MySQL Error! Unable to create new user. {$stmt->errorInfo()[2]}");
		}

		return true;
	}

	public function makeAdmin($id)
	{
		$stmt = $this->pdo->prepare($this->queries[__FUNCTION__]);
		$stmt->bindParam(':id', $id);

		if ( ! $stmt->execute() )
		{
			throw new AppRuntimeException("MySQL Error! Unable to make user id {$id} admin. {$stmt->errorInfo()[2]}");
		}

		return $stmt->rowCount() === 0 ? false : true;
	}

	public function revokeAdmin($id)
	{
		$stmt = $this->pdo->prepare($this->queries[__FUNCTION__]);
		$stmt->bindParam(':id', $id);

		if ( ! $stmt->execute() )
		{
			throw new AppRuntimeException("MySQL Error! Unable to revoke admin privilege for user {$id}. {$stmt->errorInfo()[2]}");
		}

		return $stmt->rowCount() === 0 ? false : true;
	}

	public function start_session()
	{
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}

	public function endSession()
	{
		$this->start_session();

		unset($_SESSION['APP_USER_ID']);
		unset($_SESSION['APP_USER_LOGGED_IN']);
		unset($_SESSION['APP_SESSION_TOKEN']);

		return true;
	}

	private function getPasswordHash($user_email)
	{
		$stmt	= $this->pdo->prepare($this->queries[__FUNCTION__]);
		$stmt->bindParam(':user_email', $user_email);

		if( ! $stmt->execute() )
		{
			throw new AppRuntimeException("Unable to grab hash. Query failed to execute: " . $stmt->errorInfo()[2]);
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

	    for($i = 0; $i < 22; $i++) {
	    	//We need 22 aplhanumeric characters to trigger cryp to use Blowfish encryption
			$salt = $salt . $salt_chars[array_rand($salt_chars)];
	    }

	    return sprintf('$2a$%02d$', 10) . $salt;
	}
}