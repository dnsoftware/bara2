<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/filter/titulrubriks.css');

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/filter/titulrubriks.js', CClientScript::POS_END);

?>
<div class="rub-block">
    <h2>Выберите нужную категорию объявлений:</h2>
    <ul class="rub-list">
    <?
    // Патч для сортировки согласно тепловой карты
    $hotrub_array[60] = array();
    $hotrub_array[23] = array();
    $hotrub_array[1] = array();
    $hotrub_array[17] = array();
    $hotrub_array[41] = array();
    $hotrub_array[57] = array();
    $hotrub_array[19] = array();
    $hotrub_array[38] = array();
    $hotrub_array[49] = array();
    $hotrub_array[66] = array();

    foreach($rub_array as $rkey=>$rval)
    {
        $hotrub_array[$rkey] = $rval;
    }

    $hotrub_array[60]['alt'] = 'Объявления о личных вещах';
    $hotrub_array[23]['alt'] = 'Объявления о бытовой электронике';
    $hotrub_array[1]['alt'] = 'Объявления о транспорте';
    $hotrub_array[17]['alt'] = 'Объявления о товарах для дома и дачи';
    $hotrub_array[41]['alt'] = 'Объявления о хобби и отдыхе';
    $hotrub_array[57]['alt'] = 'Объявления для бизнеса';
    $hotrub_array[19]['alt'] = 'Объявления об услугах';
    $hotrub_array[38]['alt'] = 'Объявления о работе';
    $hotrub_array[49]['alt'] = 'Объявления о недвижимости';
    $hotrub_array[66]['alt'] = 'Объявления о животных';
    // Конец патча

    foreach($hotrub_array as $rkey=>$rval)
    {
    ?>
        <li>
            <a title="<?= $rval['alt'];?>" class="atitulrub" href="/<?= $url_parts[1]."/".$rval['parent']->transname;?>">
            <div class="titulrub">
                <div>
                <img alt="<?= $rval['alt'];?>" src="/images/rubicons/<?= $rval['parent']->r_id;?>.png">
                </div>

                <?= $rval['parent']->name;?>
            </div>
            </a>

            <div class="drop">
            <?
            foreach($rval['childs'] as $ckey=>$cval)
            {
            ?>
                <a class="sub" href="/<?= $url_parts[1]."/".$cval->transname;?>"><?= $cval->name;?></a>
            <?
            }
            ?>
            </div>
        </li>
    <?
    }
    ?>
    </ul>
    <span id="allcat_1">
        <span id="allcat_2">
            <span id="display_allrub">Все категории</span>
        </span>
    </span>
</div>


