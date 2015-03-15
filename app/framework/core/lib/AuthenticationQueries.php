<?php

class AuthenticationQueries {

	private $tables		= array(
								'users'				=> 'users',
								'user_sessions'		=> 'user_sessions'
								);

	public $queries 	= array(
									'newUser'			=>	"INSERT INTO {$this->tables['users']} (`user_email`, `password_hash`)}",

									'getPasswordHash'	=>	"SELECT `password_hash` FROM {$this->tables['users']} 
															 WHERE `user_email` = :user_email ",

									'getUserId'			=>	"SELECT `id` FROM {$this->tables['users']}
															WHERE `user_email` = :user_email",

									'saveSession'		=>	"INSERT INTO {$this->tables['user_sessions']} (`id`, `session_token`, `timestamp`)
															VALUES(:id, :session_token, CURRENT_TIMESTAMP)",

									'updateEmail'		=>	"UPDATE `{$this->tables['users']}`
															SET `user_email` = :new_user_email
															WHERE `user_email` = :user_email"
								)
}