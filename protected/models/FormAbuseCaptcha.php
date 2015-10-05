<?php
/**
 * Created by PhpStorm.
 * User: daemon
 * Date: 03.10.15
 * Time: 13:22
 */

class FormAbuseCaptcha extends CFormModel
{
    public $n_id;
    public $class;
    public $type;
    public $message;
    public $verifyCode;

    public function rules()
    {
        return array(
            array('n_id, class, type, message', 'required'),
            array('message', 'length', 'min' => 10),
            array('verifyCode', 'captcha')
        );
    }

    public function attributeLabels()
    {
        return array(
            'message'=>'Сообщение',
            'verifyCode'=>'Код проверки',
        );
    }
}