<?php


class ControllerPortal extends Controller {
	

	public function actionTest()
	{
		return View::render('index', array('world' => 'world'));
	}

	public function actionHard()
	{
		return View::render('test', array('world' => 'John'));
	}

}