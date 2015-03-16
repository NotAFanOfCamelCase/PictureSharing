<?php

function myAppConfig()
{
	return array(
					'debug'				=>	true,
					'it_email'			=>	'granados.carlos91@gmail.com',
					'app_name'			=>	'PhotoShare',
					'error_controller'	=>	'portal',
					'error_action'		=>	'hard',
					'default_controller'=>	'portal',
					'default_action'	=>	'test',
					'db'				=>	array(
													'host'			=> 	'',
													'username'		=>	'',
													'password'		=>	'',
													'database'		=>	''
											),
					'log'				=>	realpath(__DIR__ . '/../logs/application/'),
					'uploads'			=>	realpath(__DIR__ . '/../uploads/')
				);
}