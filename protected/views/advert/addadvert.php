<?
    // Загрузчик файлов http://hayageek.com/docs/jquery-upload-file.php
    Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.uploadfile.js');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.md5.js');
    Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/uploadfile.css');
?>

<style>
    .radio-listitem
    {
        cursor: pointer; background-color: #dddddd;
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
//deb::dump(Yii::app()->session['mainblock']);
//deb::dump(Yii::app()->session['addfield']);
?>
<form id="addform" onsubmit="addformsubmit(); return false;">

<div class="form-row">
    <label id="lbl-client_name" class="add-form-label"><?= Notice::model()->getAttributeLabel('client_name');?>:</label>

    <div class="add-input-block">
        <div class="input-field-border" id="input-error-client_name">
        <input class="form-input-text" type="text" name="mainblock[client_name]" id="client_name" value="<?= htmlspecialchars($this->getMainblockValue($model, 'client_name'), ENT_COMPAT);?>">
        </div>
        <div class="input-error-msg"></div>
    </div>
</div>

<div class="form-row">
    <label id="lbl-client_email" class="add-form-label"><?= Notice::model()->getAttributeLabel('client_email');?>:</label>

    <div class="add-input-block">
        <div class="input-field-border" id="input-error-client_email">
        <input class="form-input-text" type="text" name="mainblock[client_email]" id="client_email" value="<?= htmlspecialchars($this->getMainblockValue($model, 'client_email'), ENT_COMPAT);?>">
        </div>
        <div class="input-error-msg"></div>
    </div>
</div>

<div class="form-row">
    <label id="lbl-client_phone" class="add-form-label"><?= Notice::model()->getAttributeLabel('client_phone');?>: </label>

    <div class="add-input-block">
        <div class="input-field-border" id="input-error-client_phone">
        <input class="form-input-text" type="text" name="mainblock[client_phone]" id="client_phone" value="<?= htmlspecialchars($this->getMainblockValue($model, 'client_phone'), ENT_COMPAT);?>">
        </div>
        <div class="input-error-msg"></div>
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


        <span onclick="$('.selrub').change();" style="cursor: pointer; text-decoration: underline;">Обновить</span>
    </div>
</div>


<div class="form-row">
    <label id="lbl-notice_type_id" class="add-form-label"><?= Notice::model()->getAttributeLabel('notice_type_id');?>:</label>
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
<div id="div_props" style="margin: 0px; margin-top: 30px; clear: both">

</div>



<input type="text" name="mainblock[uploadfiles]" id="uploadfiles" readonly value="<?= $this->getMainblockValue($model, 'uploadfiles');?>" style="width: 900px; display: none">
<?
$uploadmainfile = $this->getMainblockValue($model, 'uploadmainfile')
?>
<input type="text" name="mainblock[uploadmainfile]" id="uploadmainfile" readonly value="<?= $uploadmainfile;?>" style="width: 200px; display: none">

<div class="form-row">

    <label id="lbl-image" class="add-form-label">Фото:</label>

    <div style="margin-left: 188px;">
    <div id="fileuploader">Upload</div>
    </div>

    <div id="fileuploader_list" style="margin-left: 188px;">
        <?
        $uploadfiles_array = Notice::getImageArray($this->getMainblockValue($model, 'uploadfiles'), $this->getMainblockValue($model, 'uploadmainfile'));

        if(count($uploadfiles_array) > 0)
        {
            foreach($uploadfiles_array as $ukey=>$uval)
            {
                $imageclass = "otherfileborder";
                if($uval == $uploadmainfile)
                {
                    $imageclass = "mainfileborder";
                }
            ?>
            <div class="ajax-file-upload-statusbar" id="oldload_<?= md5($uval);?>" style="width: 500px;">

                <div class="ajax-file-upload-image">
                    <img src="/tmp/<?= $uval;?>" md5id="<?= md5($uval);?>" class="<?= $imageclass;?>" fileload_id="<?= $uval;?>" style="height: 80px; width: auto;" height="80" width="0">
                </div>
                <div class="ajax-file-upload-filename">
                    <? //тут название файла ?>
                </div>
                <div class="ajax-file-upload-progress" style="">
                    <div style="width: 100%;" class="ajax-file-upload-bar ajax-file-upload-<?= md5($uval);?>"></div>
                </div>
                <div class="ajax-file-upload-red ajax-file-upload-abort ajax-file-upload-<?= md5($uval);?>" style="display: none;">Abort</div>

                <div class="ajax-file-upload-red ajax-file-upload-cancel ajax-file-upload-<?= md5($uval);?>" style="display: none;">Cancel</div>

                <div class="ajax-file-upload-green" style="display: none;">Done</div>

                <div class="ajax-file-upload-green" style="display: none;">Download</div>

                <div class="ajax-file-upload-red old_load_delete" delfile="<?= $uval;?>" style="">Delete</div>

            </div>
            <?
            }
        }
        ?>

    </div>

</div>

<script>

    $('.otherfileborder, .mainfileborder').click(
        function()
        {
            $('.mainfileborder').attr('class', 'otherfileborder');
            $(this).attr('class', 'mainfileborder');
            $('#uploadmainfile').val($(this).attr('fileload_id'));
        }
    );


    // Удаление подстроки с именем удаленного файла и вычисление нового заглавного изображения
    function changeFileListAfterDelete(deleted_file)
    {
        //alert(deleted_file);
        filelist = $('#uploadfiles').val().replace(deleted_file+';', '');
        $('#uploadfiles').val(filelist);
        nextmain = $('.otherfileborder').first();
        if($('.otherfileborder').length == 0)
        {
            nextmain = $('.mainfileborder').first();
        }
        nextmain.attr('class', 'mainfileborder');
        $('#uploadmainfile').val(nextmain.attr('fileload_id'));
    }

    $("#fileuploader").uploadFile({
        url:"<?= Yii::app()->request->baseUrl;?>/index.php?r=advert/upload",
        fileName:"myfile",
        multiple:true,
        showDelete:true,
        showDone: false,
        returnType:"json",
        allowedTypes:"jpg,png,gif,jpeg",
        maxFileCount:5,
        showFileCounter: false,
        onSuccess:function(files,data,xhr)
        {
            //files: list of files
            //data: response from server
            //xhr : jquer xhr object
            //alert(files[0]);
            $('#uploadfiles').val($('#uploadfiles').val()+data[0]+';');
            image = $('[md5id = '+ $.md5(files[0])+']');
            image.attr('fileload_id', data[0]);
            if($('#uploadmainfile').val().length < 2)
            {
                $('#uploadmainfile').val(data[0]);
                image.attr('class', 'mainfileborder');
            }
            image.click(
                function()
                {
                    $('.mainfileborder').attr('class', 'otherfileborder');
                    $(this).attr('class', 'mainfileborder');
                    $('#uploadmainfile').val($(this).attr('fileload_id'));
                }
            );
        },

        deleteCallback: function (data, pd) {
            //console.log(data);

            for (var i = 0; i < data.length; i++) {
                $.post("<?= Yii::app()->request->baseUrl;?>/index.php?r=advert/uploaddelete", {op: "delete",name: data[i]},
                    function (resp,textStatus, jqXHR) {
                        changeFileListAfterDelete(data[0]);
                    });
            }
            pd.statusbar.hide(); //You choice.

        }
    });


    $('.old_load_delete').click(function ()
    {
        delfile = $(this).attr('delfile');
        delfile_id = $.md5($(this).attr('delfile'));

        $.ajax({
            type: 'POST',
            url: '/index.php?r=/advert/uploaddelete',
            data: 'op=delete&name='+delfile,
            success: function(msg){
                $('#oldload_'+delfile_id).remove();
                changeFileListAfterDelete(delfile);
            }
        });
    });


</script>

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

<div class="form-row">
    <label id="lbl-title" class="add-form-label"><?= Notice::model()->getAttributeLabel('title');?>:</label>
    <div class="add-input-block">
        <div class="input-field-border" id="input-error-title">
            <input class="form-input-text" type="text" name="mainblock[title]" id="title" value="<?= htmlspecialchars($this->getMainblockValue($model, 'title'));?>">
        </div>
        <div class="input-error-msg"></div>
    </div>
</div>


<div class="form-row">
    <label id="lbl-notice_text" class="add-form-label"><?= Notice::model()->getAttributeLabel('notice_text');?>:</label>
    <div class="add-input-block">
        <div class="input-field-border" id="input-error-notice_text">
            <textarea style="width: 500px; height: 100px;" name="mainblock[notice_text]" id="title"><?= $this->getMainblockValue($model, 'notice_text');?></textarea>
        </div>
        <div class="input-error-msg"></div>
    </div>
</div>

<div class="form-row">
    <label id="lbl-cost" class="add-form-label"><?= Notice::model()->getAttributeLabel('cost');?>:</label>
    <div class="add-input-block">
        <div class="input-field-border" id="input-error-cost">
            <input class="form-input-text" type="text" name="mainblock[cost]" id="cost" value="<?= htmlspecialchars($this->getMainblockValue($model, 'cost'), ENT_COMPAT);?>" style="width: 70px;">


            <?
            $cost_valuta = $this->getMainblockValue($model, 'cost_valuta');
            //deb::dump($cost_valuta);
            ?>
            <select name="mainblock[cost_valuta]" id="cost_valuta">
                <?
                foreach (Options::$valutes as $vkey=>$vval)
                {
                    ?>
                    <option <?= $this->getSelectedAttr($cost_valuta, $vkey);?> value="<?= $vkey;?>"><?= $vkey;?></option>
                <?
                }
                ?>
            </select>
        </div>
        <div class="input-error-msg"></div>
    </div>
</div>


<div id="status" style="clear: both;"></div>

<div class="form-row">
    <input type="submit" name="" value="Добавить">
</div>

</form>

<script>

$('#select_country').change(function ()
{
    $.ajax({
        type: 'POST',
        url: '/index.php?r=/advert/get_html_regions',
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
        url: '/index.php?r=/advert/get_html_towns',
        data: 'reg_id='+$(this).val(),
        success: function(msg){
            $('#select_town').html(msg);
        }
    });
});

// fromwhere - Откуда вызов. auto - вызов автоматом при перезагрузке страница
// hand - при ручном выборе рубрики
$('.selrub').change(function (fromwhere)
{
    $.ajax({
        type: 'POST',
        url: '/index.php?r=/advert/getrubriksprops',
        data: 'r_id='+this.value,
        success: function(msg){
            $('#div_errors').html('');
            $('#div_errors').css('display', 'none');

            $('#div_props').html(msg);
        }
    });

    $.ajax({
        type: 'POST',
        url: '/index.php?r=/advert/get_notice_types',
        data: 'r_id='+this.value,
        success: function(msg){
            $('#notice_type_id').html(msg);
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
    if (props_hierarhy[jobj.attr('id')]['childs_selector'] !== undefined)
    {
        CascadeNullRelatePropsSession($('#r_id').val(), jobj.attr('id'));

        CascadeNullRelateProps(jobj, n_id);

        $.each (props_hierarhy[jobj.attr('id')]['childs_selector'], function (index, value) {
//console.log(jobj.attr('id'));
            parent_ps_id = 0;
            if (props_hierarhy[jobj.attr('id')] !== undefined)
            {
                parent_ps_id = $('#'+props_hierarhy[jobj.attr('id')]['field_value_id']).val();
            }

            get_props_list_functions['f'+props_hierarhy[index]['vibor_type']](index, props_hierarhy[index]['parent_selector'], n_id, parent_ps_id);

            $('#div_'+index).css('display', 'block');
        });
    }
}

// Каскадное обнуление зависимых свойств
function CascadeNullRelateProps(jobj, n_id)
{
    if (props_hierarhy[jobj.attr('id')]['childs_selector'] !== undefined)
    {
        $.each (props_hierarhy[jobj.attr('id')]['childs_selector'], function (index, value, n_id) {
            //console.log(index+' = '+$('#'+index).attr('id'));
            //alert('f'+props_hierarhy[index]['vibor_type']);

            $('#div_'+index).css('display', 'none');

            $('#'+index+'-display').val('');
            $('#'+index).val('');
            $('#'+index+'-span').html('');
            $('#div_'+index+'_list').html('');

            CascadeNullRelateProps($('#'+index), n_id);
            //get_props_list_functions['f'+props_hierarhy[index]['vibor_type']]('', '');
        });

    }
}


// Обнуляем зависимых потомков, сохраненных в сессионном массиве
function CascadeNullRelatePropsSession(r_id, parent_field_id)
{
    $.ajax({
        type: "POST",
        url: '/index.php?r=/advert/cascade_null_relate_props_session',
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

function addformsubmit()
{
    var form_data = $('#addform').serialize();

    $.ajax({
        type: "POST",
        url: '/index.php?r=/advert/addnew',
        data: form_data,
        dataType: 'json',
        error: function(msg) {
            $('#status').text('Ошибка JSON').slideDown('slow');
        },
        success: function(msg) {
            $('.input-field-border').css('border', '');
            $('.input-error-msg').html('');

            $('.input-error-prop').css('border', '');
            $('.input-error-prop-msg').html('');


            //alert(msg['status']);
            if(msg['status'] == 'error')
            {
                $.each(msg['errors'], function(mkey, mval)
                {
//                console.log($('#input-error-'+mkey));
                    $('#input-error-'+mkey).css('border', '#f00 solid 2px');
                    $('#input-error-'+mkey+' + .input-error-msg').html(mval);
                    //console.log(mkey);
                    //console.log(mval[0]);
                });

                $.each(msg['errors_props'], function(mkey, mval)
                {
//                console.log($('#input-error-'+mkey));
                    $('#input-error-prop-'+mkey).css('border', '#f00 solid 2px');
                    $('#input-error-prop-'+mkey+' + .input-error-prop-msg').html(mval);
                    //console.log(mkey);
                    //console.log(mval[0]);
                });

                $('#status').html(msg['message']);
            }

            if(msg['status'] == 'ok')
            {
                location.href='/index.php?r=advert/addpreview';
            }
            console.log(msg);
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





















