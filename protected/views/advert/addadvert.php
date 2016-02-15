<?
    // Загрузчик файлов http://hayageek.com/docs/jquery-upload-file.php
    Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.uploadfile.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.md5.js');
    Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/uploadfile.css');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.maskedinput.min.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/image_rotator.js');
?>


<style>
    .radio-listitem
    {
        cursor: pointer; background-color: #dddddd; padding: 1px 2px; margin: 1px; display: inline-block;
    }
    .radio-listitem:hover
    {
        cursor: pointer; background-color: #fff;
    }

    .add_hideinput
    {
        display: none;
    }

    .add_hideselector, .add_hidevibortype
    {
        display: none;
    }

    .prop_name
    {
        width: 188px;
        float: left;
    }

    .prop_block
    {
        margin-top: 5px; margin-bottom: 20px;
    }

    .tbl-prop-name
    {
        width: 1px; padding: 0px; margin: 0px;
    }

    .addnot-field-selected
    {
        color: #006600; font-size: 16px; font-weight: bold; cursor: pointer;
    }

    .mainfileborder
    {
        border: #bd362f solid 3px;
        cursor: pointer;
    }
    .otherfileborder
    {
        border: #ffffff solid 3px;
        cursor: pointer;
    }
    .form-row
    {
        width: 100%; clear: both;
    }
    .add-form-label
    {
        width: 188px;
        float: left;
    }
    .form-input-text
    {
        width: 200px;;
    }
    .selrub
    {
        width: 220px;
    }
    .add-form-select
    {
        width: 220px;
    }
    .add-input-block
    {
        float: left;
    }
    .input-field-border
    {
        width: auto; float: left;
    }
    .input-error-msg
    {
        color: #f00; float: left; clear: left;
    }
    .input-error-prop
    {
        display: table-cell;
    }
    .input-error-prop-msg
    {
        color: #f00;
    }

</style>

<?
$title = "Новое объявления";
if(Yii::app()->controller->action->id == 'advert_edit')
{
    $title = "Редактирование объявления";
}

?>

<?
$mytown_id = Yii::app()->request->cookies->contains('geo_mytown') ?
    Yii::app()->request->cookies['geo_mytown']->value : 0;
$myregion_id = Yii::app()->request->cookies->contains('geo_myregion') ?
    Yii::app()->request->cookies['geo_myregion']->value : 0;
$mycountry_id = Yii::app()->request->cookies->contains('geo_mycountry') ?
    Yii::app()->request->cookies['geo_mycountry']->value : 0;

//deb::dump($_SESSION);

?>

<div style="text-align: center;">

    <h1 style="font-size: 16px; margin-bottom: 30px;"><?= $title;?></h1>

    <?
    if(Yii::app()->controller->action->id == 'advert_edit'
        && isset($_GET['republic']) && intval($_GET['republic']) == 1)
    {
    ?>
    <div style="font-size: 14px; color: #f00; margin-bottom: 20px;">
        Для активации объявления необходимо заполнить все необходимые данные!
    </div>
    <?
    }
    ?>

</div>

<form id="addform" onsubmit="addformsubmit(<?= $n_id;?>); return false;">

<input type="hidden" name="mainblock[n_id]" id="notice_id" value="<?= $n_id;?>">

<div class="form-row">
    <label id="lbl-client_name" class="add-form-label"><?= Notice::model()->getAttributeLabel('client_name');?>:</label>

    <div class="add-input-block">
        <div class="input-field-border" id="input-error-client_name">
        <?
        $client_name = htmlspecialchars($this->getMainblockValue($model, 'client_name'), ENT_COMPAT);
        if($client_name == '' && Yii::app()->user->id > 0)
        {
            $client_name = htmlspecialchars(Yii::app()->user->model()->privat_name);
        }
        ?>
        <input class="form-input-text" type="text" name="mainblock[client_name]" id="client_name" value="<?= $client_name;?>">
        </div>
        <div class="input-error-msg"></div>
    </div>
</div>

<div class="form-row">
    <label id="lbl-client_email" class="add-form-label"><?= Notice::model()->getAttributeLabel('client_email');?>:</label>

    <div class="add-input-block">
        <div class="input-field-border" id="input-error-client_email">
        <?
        if(Yii::app()->user->id > 0)
        {
        ?>
            <?= Yii::app()->user->email;?>
            <input class="form-input-text" type="text" name="mainblock[client_email]" id="client_email" value="<?= Yii::app()->user->email;?>" readonly style="display: none;">
        <?
        }
        else
        {
        ?>
            <input class="form-input-text" type="text" name="mainblock[client_email]" id="client_email" value="<?= htmlspecialchars($this->getMainblockValue($model, 'client_email'), ENT_COMPAT);?>">
        <?
        }
        ?>
        </div>
        <div class="input-error-msg"></div>
    </div>
</div>


<div class="form-row" style="margin-top: 30px; ">
    <label id="lbl-client_phone" class="add-form-label"><?= Notice::model()->getAttributeLabel('client_phone');?>: </label>

    <div class="add-input-block">
        <div class="input-field-border" id="input-error-client_phone">
            <?
            //deb::dump(Yii::app()->controller->action->id);
            ?>
            <?
            $client_phone_c_id = $this->getMainblockValue($model, 'client_phone_c_id');
            $client_phone = trim($this->getMainblockValue($model, 'client_phone'));
            if(intval($client_phone_c_id) == 0 && trim($client_phone) == '')
            {
                $client_phone_c_id = $mycountry_id;
            }
            if($client_phone == '' && count($user_phones) > 0)
            {
                $client_phone = current($user_phones)->phone;
            }
            ?>


            <table>
                <tr>
                    <td style="width: auto;">

                        <?
                        $hand_input_phone_style = "";
                        if(count($user_phones) > 0 )
                        {
                            $list_input_phone_display = "display: block;";
                            $hand_input_phone_style = "display: none;";
                            if($this->getMainblockValue($model, 'client_phone') != '')
                            {
                                $list_input_phone_display = "display: none;";
                                $hand_input_phone_style = "display: block;";
                            }
                            ?>
                            <div id="list_input_phone" style="<?= $list_input_phone_display;?>">
                                <nobr>
                                    <select id="select_user_phones">
                                        <?
                                        foreach($user_phones as $ukey=>$uval)
                                        {
                                            $selected = " ";
                                            if($client_phone_c_id == $uval['c_id'] && $this->getMainblockValue($model, 'client_phone') == $uval['phone'])
                                            {
                                                $selected = " selected ";
                                            }
                                            ?>
                                            <option <?= $selected;?> value="<?= $uval['ph_id'];?>" c_id="<?= $uval['c_id'];?>" phone="<?= $uval['phone'];?>"><?= $countries_array[$uval['c_id']]." ".$uval['phone'];?></option>
                                        <?
                                        }
                                        ?>
                                    </select>

                                    <span id="send_check_phone_change" style="border-bottom: #000 dotted 1px; cursor: pointer;" onclick="SendCheckPhoneChange();">Изменить номер телефона</span>
                                </nobr>
                            </div>

                            <?
                            //$hand_input_phone_style = "display: none;";
                        }
                        ?>
                        <script>
                            $('#select_user_phones').change(function(){
                                $('#select_country_code').val($('#select_user_phones option:selected').attr('c_id'));
                                $('#select_country_code').change();
                                $('#client_phone').val($('#select_user_phones option:selected').attr('phone'));
                            });
                        </script>



                        <div id="hand_input_phone" style="<?= $hand_input_phone_style;?>">
                            <div style="width: 800px;">
                                <nobr><span style="border-bottom: #000 dotted 1px; cursor: pointer;" id="span_country"><?= $countries_array[$client_phone_c_id];?></span>

                                    <select id="select_country_code" name="mainblock[client_phone_c_id]" style="display: none;">
                                        <?
                                        foreach($countries_array as $ckey=>$cval)
                                        {
                                            ?>
                                            <option <?= $this->getSelectedAttr($client_phone_c_id, $ckey);?> value="<?= $ckey;?>"><?= $cval;?></option>
                                        <?
                                        }
                                        ?>
                                    </select>

                                    <?
                                    ?>
                                    <input class="form-input-text" style="width: 100px;" type="text" name="mainblock[client_phone]" id="client_phone" value="<?= $client_phone;?>">

                                    <?
                                    $send_check_phone_button_display = 'none';
                                    if($client_phone_c_id == Yii::app()->params['russia_id'])
                                    {
                                        $send_check_phone_button_display = 'inline';
                                    }
                                    ?>
                                    <input type="button" style="display: <?= $send_check_phone_button_display;?>;" id="send_check_phone_button" onclick="SendCheckPhone();" value="Подтвердить телефон">

                                    <?
                                    if(count($user_phones) > 0)
                                    {
                                        ?>
                                        <span id="select_phone_from_list" style="border-bottom: #000 dotted 1px; cursor: pointer;" >Выбрать телефон из списка</span>
                                    <?
                                    }
                                    ?>

                                </nobr>
                            </div>
                        </div>


                    </td>
                </tr>
                <tr>
                    <td >
                        <div id="send_check_phone_error" style="color: #f00;"></div>
                        <div id="send_check_phone_ok" style="color: #299e12;"></div>

                        <div id="send_check_code" style="border: #999 solid 1px; display: none; padding: 5px;">
                            На указанный номер отправлено СМС  с кодом подтверждения<br>
                            Введите его в окно подтверждения и нажмите ОК<br>
                            <input type="text" id="check_code_field">
                            <input type="button" value="OK" onclick="SendCheckPhoneKod();">
                        </div>

                    </td>
                </tr>
            </table>

        </div>
        <div class="input-error-msg"></div>
    </div>




</div>



<div class="form-row">

    <label id="lbl-client_coord" class="add-form-label">Местоположение:</label>
    <div class="add-input-block">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td  style="margin: 0px; padding: 0px;">
                    <div class="input-field-border" id="input-error-c_id">

                        <select class="add-form-select" name="mainblock[c_id]" id="select_country">
                            <?
                            $c_id = intval($this->getMainblockValue($model, 'c_id'));
                            if($c_id == 0)
                            {
                                $c_id = $mycountry_id;
                            }
                            Countries::displayCountryList($c_id);

                            ?>
                        </select>
                    </div>
                    <div class="input-error-msg"></div>
                </td>
                <td>
                    <div class="input-field-border" id="input-error-reg_id">
                        <select class="add-form-select" name="mainblock[reg_id]" id="select_region">
                            <?
                            $reg_id = intval($this->getMainblockValue($model, 'reg_id'));
                            if($reg_id == 0)
                            {
                                $reg_id = $myregion_id;
                            }
                            Regions::displayRegionList($c_id, $reg_id);
                            ?>
                        </select>
                    </div>
                    <div class="input-error-msg"></div>
                </td>
                <td>
                    <div class="input-field-border" id="input-error-t_id">
                        <select class="add-form-select" name="mainblock[t_id]" id="select_town" >
                            <?
                            $t_id = intval($this->getMainblockValue($model, 't_id'));
                            if($t_id == 0)
                            {
                                $t_id = $mytown_id;
                            }
                            Towns::displayTownList($reg_id, $t_id);
                            ?>
                        </select>
                    </div>
                    <div class="input-error-msg"></div>
                </td>
            </tr>
        </table>

    </div>
</div>


<div class="form-row">
<?
$r_id = $this->getMainblockValue($model, 'r_id')
?>
<label id="lbl-r_id" class="add-form-label"><?= Notice::model()->getAttributeLabel('r_id');?>:</label>
    <div class="add-input-block">
        <div class="input-field-border" id="input-error-r_id">
        <select name="mainblock[r_id]" id="r_id" class="selrub" onchange="">
        <option <?= $this->getSelectedAttr($r_id, "");?> value="">--- выберите категорию  ---</option>
        <?
        foreach ($rub_array as $rkey=>$rval)
        {
            ?>
            <option disabled style="color:#000; font-weight: bold;" value="<?= $rval['parent']->r_id;?>"><?= $rval['parent']->name;?></option>
            <?
            foreach ($rval['childs'] as $ckey=>$cval)
            {
                ?>
                <option <?= $this->getSelectedAttr($r_id, $cval->r_id);?> value="<?= $cval->r_id;?>">&nbsp;<?= $cval->name;?></option>
            <?
            }
        }
        ?>
        </select>
        </div>
        <div class="input-error-msg"></div>


        <span onclick="$('.selrub').change();" style="cursor: pointer; text-decoration: underline; display: none;">Обновить</span>
    </div>
</div>


<?
/*
?>
<div class="form-row">
    ----------------Удалить<label id="lbl-notice_type_id" class="add-form-label"><?= Notice::model()->getAttributeLabel('notice_type_id');?>:</label>
    <div class="add-input-block">
<?
//$notice_type_id = $this->getMainblockValue($model, 'notice_type_id');
//deb::dump($notice_type_id);
?>
        <div class="input-field-border" id="input-error-notice_type_id">
        <select name="mainblock[notice_type_id]" id="notice_type_id">
    <?
        $notice_type_id = $this->getMainblockValue($model, 'notice_type_id');
        NoticeTypeRelations::displayNoticeTypeList(intval($r_id), $notice_type_id);
    ?>
        </select>
        </div>
        <div class="input-error-msg"></div>
    </div>
</div>
<?
*/
?>
<?
 /*
?>
<div class="form-row">
    <input type="submit" name="" value="Добавить">
</div>
<?
*/
?>

<div style="color: #f00; display: none;" id="div_errors">

</div>


<? // Блок куда подгружаются данные по свойствам объявы ?>
<div id="div_ajax_loader_icon" style="clear: both; text-align: center; margin-top: 50px; margin-bottom: 30px; display: none;">
    <img src="/images/ajaxload.gif">
</div>

<div id="div_props" style="margin: 0px; margin-top: 30px; clear: both">

</div>





<div class="form-row">
    <label id="lbl-client_expire_period" class="add-form-label"><?= Notice::model()->getAttributeLabel('expire_period');?>:</label>
    <div class="add-input-block">
        <div class="input-field-border" id="input-error-expire_period">
            <select name="mainblock[expire_period]" id="expire_period">
                <?
                $expire_period = intval($this->getMainblockValue($model, 'expire_period'));
                foreach (Notice::$expire_period as $ekey=>$eval)
                {
                    ?>
                    <option <?= $this->getSelectedAttr($expire_period, $ekey);?> value="<?= $ekey;?>"><?= $ekey." ".$eval;?></option>
                <?
                }
                ?>
            </select>
        </div>
        <div class="input-error-msg"></div>
    </div>
</div>

<div class="form-row" id="div_title">
    <label id="lbl-title" class="add-form-label"><?= Notice::model()->getAttributeLabel('title');?>:</label>
    <div class="add-input-block">
        <div class="input-field-border" id="input-error-title">
            <input class="form-input-text" style="width: 600px;" type="text" name="mainblock[title]" id="title" value="<?= htmlspecialchars($this->getMainblockValue($model, 'title'));?>">
        </div>
        <div class="input-error-msg"></div>
    </div>
</div>


<div class="form-row">
    <label id="lbl-notice_text" class="add-form-label"><?= Notice::model()->getAttributeLabel('notice_text');?>:</label>
    <div class="add-input-block">
        <div class="input-field-border" id="input-error-notice_text">
            <textarea style="width: 600px; height: 100px;" name="mainblock[notice_text]" id="notice_text"><?= $this->getMainblockValue($model, 'notice_text');?></textarea>
        </div>
        <div class="input-error-msg"></div>
    </div>
</div>

<div class="form-row">
    <label id="lbl-cost" class="add-form-label"><?= Notice::model()->getAttributeLabel('cost');?>:</label>
    <div class="add-input-block">
        <div class="input-field-border" id="input-error-cost">

            <?
            $checked = " ";
            $disabled = " ";
            if($this->getMainblockValue($model, 'cost_nodisplay_tag'))
            {
                $checked = " checked ";
                $disabled = " disabled ";
            }

            $cost = htmlspecialchars($this->getMainblockValue($model, 'cost'), ENT_COMPAT);
            if(ceil($cost) == floor($cost))
            {
                $cost = floor($cost);
            }
            ?>

            <input <?= $disabled;?> class="form-input-text" type="text" name="mainblock[cost]" id="cost" value="<?= $cost;?>" style="width: 70px;">


            <?
            $cost_valuta = $this->getMainblockValue($model, 'cost_valuta');
            //deb::dump($cost_valuta);
            ?>
            <select <?= $disabled;?> name="mainblock[cost_valuta]" id="cost_valuta">
                <?
                foreach (Options::$valutes as $vkey=>$vval)
                {
                    ?>
                    <option <?= $this->getSelectedAttr($cost_valuta, $vkey);?> value="<?= $vkey;?>"><?= $vkey;?></option>
                <?
                }
                ?>
            </select>

            <input type="hidden" id="cost_nodisplay_tag" name="mainblock[cost_nodisplay_tag]" value="<?= $this->getMainblockValue($model, 'cost_nodisplay_tag');?>">

            <input id="cost_nodisplay_check" type="checkbox" <?= $checked;?> > не указывать
        </div>
        <div class="input-error-msg"></div>
    </div>
</div>




<script type="text/javascript">

    $('#cost_nodisplay_check').click(function(){
        //console.log($(this).prop('checked'));
        if($(this).prop('checked'))
        {
            $('#cost_nodisplay_tag').val(1);
            $('#cost').prop('disabled', true);
            $('#cost_valuta').prop('disabled', true);
        }
        else
        {
            $('#cost_nodisplay_tag').val(0);
            $('#cost').prop('disabled', false);
            $('#cost_valuta').prop('disabled', false);
        }
    });

    jQuery(function($){
//        $("#client_phone").mask("999 999-99-99");

        $.mask.definitions['x'] = "[0-9]";
        $.mask.definitions['9'] = "";
        $("#client_phone").mask(mask_array[$('#select_country_code').val()]);
        if($('#select_country_code').val() == <?= Yii::app()->params['russia_id'];?>)
        {
            $("#client_phone").mask('9xx xxx-xx-xx');
        }
        //$("#client_phone").attr({'placeholder':'9__ ___ __ __'});

    });

    var mask_array = new Array();
    <?
    foreach($mask_array as $mkey=>$mask)
    {
    ?>
    mask_array[<?= $mkey;?>] = '<?= $mask;?>';
    <?
    }
    ?>

    $('#select_country_code').change(function()
    {
        $("#client_phone").mask(mask_array[$('#select_country_code').val()]);
        if($('#select_country_code').val() == <?= Yii::app()->params['russia_id'];?>)
        {
            $("#client_phone").mask('9xx xxx-xx-xx');
        }

        $('#span_country').html($('#select_country_code option:selected').html());
        $('#span_country').css('display', 'inline');
        $('#select_country_code').css('display', 'none');
        if($('#select_country_code').val() == <?= Yii::app()->params['russia_id'];?>)
        {
            $('#send_check_phone_button').css('display', 'inline');
        }
        else
        {
            $('#send_check_phone_button').css('display', 'none');
        }

    });

    $('#span_country').click(function(){
        $('#select_country_code').css('display', 'inline');
        $('#span_country').css('display', 'none');
    });


    function SendCheckPhone()
    {
        $.ajax({
            type: "POST",
            url: '<?= Yii::app()->createUrl('user/registration/checkphonesms');?>',
            data: 'c_id='+$('#select_country_code').val()+'&phone='+$('#client_phone').val(),
            //dataType: 'json',
            error: function(msg) {

            },
            success: function(msg) {
                if(msg == 'empty')
                {
                    $('#send_check_phone_error').html('Укажите номер телефона!');
                    $('#send_check_code').css('display', 'none');
                }
                else if(msg == 'inbase')
                {
                    $('#send_check_phone_error').html('Укажите другой телефон. Такой телефон уже есть в базе!');
                    $('#send_check_code').css('display', 'none');

                }
                else if(msg == 'youinbase')
                {
                    $('#send_check_phone_error').html('Данный телефон уже был подтвержден ранее!');
                    $('#send_check_code').css('display', 'none');

                }
                else if(msg == 'send')
                {
                    $('#send_check_phone_ok').html('');
                    $('#send_check_phone_error').html('');
                    $('#send_check_code').css('display', 'block');

                    $('#select_country_code').css('background-color', '#ccc');
                    $('#client_phone').css('background-color', '#ccc');

                    $('#send_check_phone_button').css('display', 'none');


                }
                else if(msg.indexOf('timeout-') + 1)
                {
                    $('#send_check_phone_error').html('Запросить код повторно можно будет через '+msg.replace('timeout-', '')+' секунд. ');
                }
                else if(msg == 'bytehand_error')
                {
                    $('#send_check_phone_error').html('Проблема с отправкой SMS!');
                }
            }
        });
    }

    function SendCheckPhoneKod()
    {
        $.ajax({
            type: "POST",
            url: '<?= Yii::app()->createUrl('user/registration/checkphonekod');?>',
            data: 'phone='+$('#client_phone').val()+'&code='+$('#check_code_field').val(),
            //dataType: 'json',
            error: function(msg) {

            },
            success: function(msg) {
                if(msg == 'ok')
                {
                    $('#send_check_phone_error').html('');
                    $('#send_check_phone_ok').html('Номер телефона подтвержден!');

                    $('#select_country_code').css('background-color', '#ccc');
                    $('#client_phone').css('background-color', '#ccc');

                    $('#send_check_code').css('display', 'none');
                    $('#send_check_phone_button').css('display', 'none');
                }

                if(msg == 'bad')
                {
                    $('#send_check_phone_ok').html('');
                    $('#send_check_phone_error').html('Неверный код!');
                }
            }
        });
    }


    function SendCheckPhoneChange()
    {
        //$('#span_country').css('display', 'inline');
        $('#hand_input_phone').css('display', 'block');
        $('#select_country_code').css('display', 'none');
        $('#list_input_phone').css('display', 'none');

        $('#select_country_code').css('background-color', '#fff');
        $('#client_phone').css('background-color', '#fff');

        $('#send_check_code').css('display', 'none');

        if($('#select_country_code').val() == <?= Yii::app()->params['russia_id'];?>)
        {
            $('#send_check_phone_button').css('display', 'inline');
        }
    }

    $('#select_phone_from_list').click(function(){
        $('#list_input_phone').css('display', 'inline');
        $('#hand_input_phone').css('display', 'none');
        $('#select_user_phones').change();
    });

    <?
    // Если телефон из списка еще не подтвержден - показываем поле ручного ввода телефона и кнопку подтверждения
    if($phonerow = UserPhones::GetPhoneRow(Yii::app()->user->id, $client_phone_c_id, $client_phone))
    {
        if($client_phone_c_id == Yii::app()->params['russia_id'])
        {
            if(!$phonerow->verify_tag )
            {
            ?>
        SendCheckPhoneChange();
        <?
        }
        else
        {
        ?>
        $('#select_phone_from_list').click();
        <?
        }
    }


    }
    ?>

</script>


<div id="status" style="clear: both; font-size: 16px; text-align: center; margin: 10px; margin-top: 50px; margin-bottom: 0px; color: #f00; padding-top: 10px;"></div>

<div class="form-row" style="text-align: center;">
    <?
    if($n_id <= 0)
    {
        $button_text = "Добавить";
    }
    else
    {
        $button_text = "Сохранить";
    }
    ?>
    <input type="submit" id="submitadvert" name="" value="<?= $button_text;?>" style="font-size: 20px; margin: 10px; background-color: #259c1d; color: #fff; border: #268017 solid 1px; padding-bottom: 2px; border-radius: 3px;">
</div>

</form>

<script>

$('#select_country').change(function ()
{
    $.ajax({
        type: 'POST',
        url: '<?= Yii::app()->createUrl('advert/get_html_regions');?>',
        data: 'c_id='+$(this).val(),
        success: function(msg){
            $('#select_town').html('<option value="">Выберите город<option>');
            $('#select_region').html(msg);
        }
    });
});

$('#select_region').change(function ()
{
    $.ajax({
        type: 'POST',
        url: '<?= Yii::app()->createUrl('advert/get_html_towns');?>',
        data: 'reg_id='+$(this).val(),
        success: function(msg){
            $('#select_town').html(msg);
        }
    });
});

// fromwhere - Откуда вызов. auto - вызов автоматом при перезагрузке страница
// hand - при ручном выборе рубрики
$('.selrub').change(function (){

    $.ajax({
        type: 'POST',
        url: '<?= Yii::app()->createUrl('advert/getrubriksprops');?>',
        <?
        $republic_str = "";
        if(isset($_GET['republic']) && $_GET['republic'] == 1)
        {
            $republic_str = "&republic=1";
        }
        ?>
        data: 'r_id='+this.value+'&n_id='+$('#notice_id').val()+'<?= $republic_str;?>',
        success: function(msg){
            $('#div_errors').html('');
            $('#div_errors').css('display', 'none');

            $('#div_props').html(msg);
        }
    });


});


if($('#r_id').val() != '')
{
    $('#r_id').change();
}

// При смене значения - обновляем данные зависимых свойств
function ChangeRelateProps(jobj, n_id)
{
    var field_id = jobj.attr('prop_id');
//console.log(field_id);
    if (props_hierarhy[field_id]['childs_selector'] !== undefined)
    {
        CascadeNullRelatePropsSession($('#r_id').val(), field_id);
        CascadeNullRelateProps(jobj, n_id);

        $.each (props_hierarhy[field_id]['childs_selector'], function (index, value) {
            parent_ps_id = 0;
            if (props_hierarhy[field_id] !== undefined)
            {
                // тут возможно надо будет переделать для checkbox и radio
                parent_ps_id = $('[prop_id='+props_hierarhy[field_id]['field_value_id']+']').val();
            }

            get_props_list_functions['f'+props_hierarhy[index]['vibor_type']](index, props_hierarhy[index]['parent_selector'], n_id, parent_ps_id);

            $('#div_'+index).css('display', 'block');
        });
    }
}

// Каскадное обнуление зависимых свойств
function CascadeNullRelateProps(jobj, n_id)
{
    var field_id = jobj.attr('prop_id');

    if (field_id !== undefined && props_hierarhy[field_id] !== undefined && props_hierarhy[field_id]['childs_selector'] !== undefined)
    {
        $.each (props_hierarhy[field_id]['childs_selector'], function (index, value) {

            //console.log(index+' = '+n_id);

            $('#div_'+index).css('display', 'none');

            if(props_hierarhy[index]['vibor_type'] == 'photoblock')
            {
                $('#uploadfiles').val('');
                $('#uploadmainfile').val('');
                $('#fileuploader_list').html('');
            }
            else if (props_hierarhy[index]['vibor_type'] == 'checkbox')
            {
                // Доработать при необходимости
            }
            else if (props_hierarhy[index]['vibor_type'] == 'radio')
            {
                // Доработать при необходимости
            }
            else
            {
                $('#'+index+'-display').val('');
                $('#'+index).val('');
                $('#'+index+'-span').html('');
                $('#div_'+index+'_list').html('');
            }

            CascadeNullRelateProps($('[prop_id = '+index+']'), n_id);
        });



    }
}


// Обнуляем зависимых потомков, сохраненных в сессионном массиве
function CascadeNullRelatePropsSession(r_id, parent_field_id)
{
    $.ajax({
        type: "POST",
        url: '<?= Yii::app()->createUrl('advert/cascade_null_relate_props_session');?>',
        data: {
            r_id: r_id,
            parent_field_id: parent_field_id
        },
        error: function(msg) {
            alert('error_cascade_null_relate_props_session');
        },
        success: function(msg) {
            //$('#status').html(msg);
        }
    });
}


// Если установлено значение свойства родителя, значит показываем блоки со свойствами потомками
function DisplayChildsPropsBlock(parent_id)
{
    if (props_hierarhy[parent_id]['childs_selector'] !== undefined)
    {
        $.each (props_hierarhy[parent_id]['childs_selector'], function (index, value) {
            $('#div_'+index).css('display', 'block');
        });
    }
}

// Визуализация блоков в зависимости от значения соотв. свойств + визуализация связанных потомков
function DisplayAfterLoad(field_id)
{
    $('#div_'+field_id).css('display', 'block');
    DisplayChildsPropsBlock(field_id);
}

function addformsubmit(n_id)
{

    var do_action = '<?= Yii::app()->createUrl('advert/addnew');?>';
    var redirect_action = '<?= Yii::app()->createUrl('advert/addpreview');?>';

    if(n_id>0)
    {
        do_action = '<?= Yii::app()->createUrl('advert/saveedit');?>';
        redirect_action = '<?= Yii::app()->createUrl('usercab/adverts');?>';
    }

    var form_data = $('#addform').serialize();

    $.ajax({
        type: "POST",
        url: do_action,
        data: form_data,
        dataType: 'json',
        error: function(msg) {
            //$('#status').text('Ошибка JSON');
            $('#status').text('Ошибка. Отключите свой блокиратор рекламы (Adblock, Adguard и т.п.) для нормального функционирования формы подачи объявлений и сайта.');
        },
        success: function(msg) {
            $('.input-field-border').css('border', '');
            $('.input-error-msg').html('');

            $('.input-error-prop').css('border', '');
            $('.input-error-prop-msg').html('');


            //alert(msg['status']);
            if(msg['status'] == 'error')
            {
                if(msg['errors'] != null)
                {
                    $.each(msg['errors'], function(mkey, mval)
                    {
//                console.log($('#input-error-'+mkey));
                        $('#input-error-'+mkey).css('border', '#f00 solid 2px');
                        $('#input-error-'+mkey+' + .input-error-msg').html(mval);
                        //console.log(mkey);
                        //console.log(mval[0]);
                    });
                }

                if(msg['errors_props'] != null)
                {
                    $.each(msg['errors_props'], function(mkey, mval)
                    {
                        $('#div_'+mkey).css('display', 'block');
                        $('#input-error-prop-'+mkey).css('border', '#f00 solid 2px');
                        $('#input-error-prop-'+mkey+' + .input-error-prop-msg').html(mval);
                        //console.log(mkey);
                        //console.log(mval[0]);
                    });
                }

                $('#status').html(msg['message']);
            }

            if(msg['status'] == 'ok')
            {
                location.href=redirect_action;
            }
            //console.log(msg);
        }
        /*
        ,
        complete: function() {
            setTimeout(function() {
                $('#status').slideUp('slow');
            }, 3000);
        }
        */
    });
}

</script>

<?
//Yii::app()->clientScript->registerScript('displayprops', 'alert("ddd");', CClientScript::POS_LOAD);
?>





















