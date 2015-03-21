<?php

class CitymapsModule extends CWebModule
{
    public $map_api_key = 'AIzaSyCDZVU-ndjEj9SwmGKDyO7S6adK1FbbXuU';

    public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'citymaps.models.*',
			'citymaps.components.*',
		));

	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
            //if (Yii::app()->user->checkAccess('Admin'))
            //{
                return true;
            //}
		}

    	return false;
	}
}
