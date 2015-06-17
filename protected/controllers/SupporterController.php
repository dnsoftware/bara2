<?php

class SupporterController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}

    // Получение курса рубля к доллару и евро с Центробанка
    public function actionGetCbrKurs()
    {
        $content = file_get_contents('http://www.cbr.ru/scripts/XML_daily.asp?date_req='.date('d/m/Y'));
        $content = iconv('Windows-1251', 'UTF-8', $content);
        //deb::dump($content);

        preg_match_all('|<NumCode>([0-9]+)</NumCode>[\n\t\r\s ]*<CharCode>([A-Z]+)</CharCode>[\n\t\r\s ]*<Nominal>([0-9]+)</Nominal>[\n\t\r\s ]*<Name>(.+)</Name>[\n\t\r\s ]*<Value>(.+)</Value>[\n\t\r\s ]*|siU', $content, $matches);

        $kurs_array = array();
        foreach($matches[1] as $mkey=>$mval)
        {
            $kurs_array[$matches[2][$mkey]] = $matches[5][$mkey];
        }
        deb::dump($kurs_array);

        Options::setOption('kurs_usd', str_replace(",", ".", $kurs_array['USD']));
        Options::setOption('kurs_eur', str_replace(",", ".", $kurs_array['EUR']));

    }

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}