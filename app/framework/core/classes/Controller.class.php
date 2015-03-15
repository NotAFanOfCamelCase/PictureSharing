<?php

class Controller {

	public static function actionExists($action_name, $method)
	{
		if ( ! method_exists(get_called_class(), 'action'. $action_name) )
		{
			return false;
		}

		return 'action'. $action_name;
	}
}