<?php

$this->renderPartial('/default/_admin_menu');

?>
<style>
    .keyword_edit
    {
        color: #005580; cursor: pointer;
    }
    .keyword_del
    {
        color: #005580; cursor: pointer;
    }
    .td_keyword
    {
        border: #999 solid 1px;
    }
</style>

<div style="margin-bottom: 10px; font-weight: bold;">SEO</div>

<div id="seo_form" style="margin-top: 10px; ">

<?
    $this->renderPartial('seo_form', array(
        'r_id'=>$r_id,
        'seokeyword'=>$seokeyword,
        'rub_array'=>$rub_array,
        'keyword'=>$keyword,
        'search_keywords'=>$search_keywords,
        'query_type'=>$query_type
    ));
?>

</div>


<div id="seo_keywords" style="margin-top: 10px; ">

    <?
    $this->renderPartial('seo_keywords', array(
        'r_id'=>$r_id,
        'seokeyword'=>$seokeyword,
        'rub_array'=>$rub_array,
        'keyword'=>$keyword,
        'search_keywords'=>$search_keywords,
        'query_type'=>$query_type
    ));
    ?>

</div>




