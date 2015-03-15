<?php


class Rest extends Controller{

	public static function respond($payload, $code = 200)
	{
		$payload = json_encode($payload);

		if ( ! $payload )
		{
			throw new AjaxException("Payload is invalid. Unable to encode into json.");
		}

		return array('payload' => $payload, 'code' => $code, 'content_type' => 'application/json');
	}

	public static function actionExists($action_name, $method)
	{
		if ( ! method_exists(get_called_class(), $method . $action_name) )
		{
			return false;
		}

		return $method . $action_name;
	}
}