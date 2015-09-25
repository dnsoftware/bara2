<div style="text-align: left; padding-left: 14px;">
<?
include(Yii::getPathOfAlias('webroot')."/banners/yandex/top_horizont.php");
?>
</div>

<h1 style="clear: both;"><?= $mainblock['title'];?></h1>


<?

$this->renderPartial('_advertpage', array(
    'mainblock'=>$mainblock,
    'addfield'=>$addfield,
    'uploadfiles_array'=>$this->uploadfiles_array,
    'mainblock_data'=>$this->mainblock_data,
    'addfield_data'=>$this->addfield_data,
    'options'=>$this->options
));

?>


<script>
    Galleria.loadTheme('/js/galleria/themes/classic/galleria.classic.min.js');
    Galleria.run('.galleria', {
        width: 500,
        height: 400,
        //imageCrop: 'landscape'
        lightbox: true,
        //overlayBackground: '#ffffff'
        showImagenav: true,

    })

</script>