<?php


class ControllerPortal extends Controller {
	

	public function actionTest()
	{
		return View::render('test', array('world' => 'world'));
	}

}