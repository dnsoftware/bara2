<?php

class MapController extends Controller
{
    public $layout = '//layouts/map';

	public function actionIndex()
	{
		$this->render('index');
	}
}

