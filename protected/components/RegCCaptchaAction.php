<?php
class RegCCaptchaAction extends CCaptchaAction
{
    /**
     * Generates a new verification code.
     * @return string the generated verification code
     */

    public function run()
    {
        if(isset($_GET[self::REFRESH_GET_VAR]))  // AJAX request for regenerating code
        {
            $code=$this->getVerifyCode(true);
            echo CJSON::encode(array(
                'hash1'=>$this->generateValidationHash($code),
                'hash2'=>$this->generateValidationHash(strtolower($code)),
                // we add a random 'v' parameter so that FireFox can refresh the image
                // when src attribute of image tag is changed
                'url'=>$this->getController()->createUrl($this->getId(),array('v' => uniqid())),
            ));
        }
        else
            $this->renderImage($this->getVerifyCode(true));
        Yii::app()->end();
    }

/*
    protected function generateVerifyCode()
    {
        if($this->minLength<3)
            $this->minLength=3;
        if($this->maxLength>20)
            $this->maxLength=20;
        if($this->minLength>$this->maxLength)
            $this->maxLength=$this->minLength;
        $length=rand($this->minLength,$this->maxLength);

        // Тут указываем символы которые будут
        // выводится у нас на капче.
        $letters='йцкнгшзхфвпрлджчмтб';
        $vowels='уеаояию';
        $code='';
        for($i=0;$i<$length;++$i)
        {
            $code.=$letters[rand(0, strlen($letters)-1)];
        }
        return $code;
    }
*/


}