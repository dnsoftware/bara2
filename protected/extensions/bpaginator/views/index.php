<?
$urlasset = Yii::app()->assetManager->publish( Yii::getPathOfAlias('ext.bpaginator.css').'/bpaginator.css');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.$urlasset);
?>

<?php


if($this->kolpages > 1)
{
?>
<div class="<?= $this->css_class;?>">
    <?

    if($start_page > 1)
    {
        ?><a class="bpaleft" style="border-right: none;" href="<?= $this->page_url;?><?= $this->page_substr;?>=1">I<</a><?
    }
    else
    {
        ?><span class="bpdeactleft" style="border-right: none;">I<</span><?
    }

    if($start_page > 1)
    {
    ?><a class="bpaleft" href="<?= $this->page_url;?><?= $this->page_substr;?>=<?= $prev_page;?>"><<</a><?
    }
    else
    {
    ?><span class="bpdeactleft"><<</span><?
    }

    for($i=$start_page; $i<=$end_page; $i++)
    {
        $active_class = "";
        if($i == $this->page)
        {
            $active_class = " bpactive";
        }
    ?><a class="<?= $active_class;?>" href="<?= $this->page_url;?><?= $this->page_substr;?>=<?= $i;?>"><?= $i;?></a><?
    }

    if($end_page < $this->kolpages)
    {
        ?><a class="" href="<?= $this->page_url;?><?= $this->page_substr;?>=<?= $next_page;?>">>></a><?
    }
    else
    {
        ?><span class="bpdeactright">>></span><?
    }

    if($end_page < $this->kolpages)
    {
        ?><a class="" style="border-left: none;" href="<?= $this->page_url;?><?= $this->page_substr;?>=<?= $this->kolpages;?>">>I</a><?
    }
    else
    {
        ?><span class="bpdeactright" style="border-left: none;">>I</span><?
    }

    ?>
</div>
<?
}