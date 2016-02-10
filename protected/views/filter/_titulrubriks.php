<?php


?>
<div class="rub-block">
    <h2>Выберите нужную категорию объявлений:</h2>
    <ul class="rub-list" style="margin-bottom: 0px;">
    <?
    foreach($rub_array as $rkey=>$rval)
    {
    ?>
        <li>
            <a href="/<?= $url_parts[1]."/".$rval['parent']->transname;?>"><?= $rval['parent']->name;?></a>
            <div class="drop" style="display: none;">
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
    <span style="width: 100%; border-bottom: #ddd solid 1px; display: inline-block; margin-bottom: 30px;">
        <span style="display: inline-block; background-color: #fff; font-size: 16px; margin-bottom: -10px; padding-left: 10px; padding-right: 10px;">
            <span id="display_allrub" style="border-bottom: dashed 1px; cursor: pointer;">Все категории</span>
        </span>
    </span>
</div>


<script>
    $('#display_allrub').click(function(){
        if($('.drop').css('display') == 'none')
        {
            $('.drop').css('display', 'block');
        }
        else
        {
            $('.drop').css('display', 'none');
        }
    });
</script>