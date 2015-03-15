<?php

class ControllerAPI extends Rest
{
	function __construct()
	{

	}

	public function getTest()
	{
		return parent::respond(array(1, 2, 3));
	}
}