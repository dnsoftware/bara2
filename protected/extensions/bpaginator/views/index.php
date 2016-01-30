<style>
    .bpaginator a
    {
        padding: 3px 7px 3px 7px;
        text-decoration: none;
        display: inline-block;
        border: #ddd solid 1px;
        border-left: none;
        margin: 0;
        color: #008CC3;
        font-size: 16px;
    }

    .bpaginator a:hover
    {
        background-color: #ddd;
    }

    .bpaginator .bpactive
    {
        background-color: #ddd;
    }

    .bpaginator .bpaleft
    {
        border-left: #ddd solid 1px;
    }

    .bpaginator .bpdeactleft
    {
        padding: 3px 7px 3px 7px;
        text-decoration: none;
        display: inline-block;
        border: #ddd solid 1px;
        margin: 0;
        color: #ddd;
        font-size: 16px;
    }

    .bpaginator .bpdeactright
    {
        padding: 3px 7px 3px 7px;
        text-decoration: none;
        display: inline-block;
        border: #ddd solid 1px;
        border-left: none;
        margin: 0;
        color: #ddd;
        font-size: 16px;
    }


</style>

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