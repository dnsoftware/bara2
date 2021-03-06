<?php

class AdminkaModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'adminka.models.*',
			'adminka.components.*',
		));

	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
            if (Yii::app()->user->checkAccess('Admin'))
            {
                return true;
            }
		}

    	return false;
	}
}
