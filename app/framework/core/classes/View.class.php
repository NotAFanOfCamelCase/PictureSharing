<?php

class View {

	private static function validateView($name)
	{
		$view_path = appcore\APP_VIEWS . '/' . $name . '.php';

		if ( ! file_exists($view_path) )
		{
			throw new AppRuntimeException("Unable to render view {$name}! File does not exist");
		}

		return $view_path;
	}

	public static function render($view, $page_vars = array(), $response_code=200, $content_type='text/html') 
	{
		extract($page_vars);

		ob_start();

		require_once self::validateView($view);

		$renderedView = ob_get_clean();

		return array('payload' => $renderedView, 'code' => $response_code, 'content_type' => $content_type);
	}
}