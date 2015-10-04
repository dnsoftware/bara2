<?php
/**
 * Created by PhpStorm.
 * User: daemon
 * Date: 03.10.15
 * Time: 13:22
 */

class FormWriteAuthor extends CFormModel
{
    public $n_id;
    public $name;
    public $email;
    public $message;
    public $verifyCode;

    public function rules()
    {
        return array(
            array('n_id, name, email, message', 'required'),
            array('name', 'length', 'max'=>128, 'min' => 2),
            array('email', 'email'),
            array('message', 'length', 'min' => 10),
            array('verifyCode', 'captcha')
        );
    }

    public function attributeLabels()
    {
        return array(
            'name'=>'Ваше имя',
            'email'=>'Ваш e-mail',
            'message'=>'Сообщение',
            'verifyCode'=>'Код проверки',
        );
    }
}