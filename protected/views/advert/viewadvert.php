<?
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/jquery.sumoselect.min.js');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/sumoselect.css');

Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/filtercontroller.js');
Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/baraholka.js');
?>

<?
$this->renderPartial('/filter/_search_form', array(
    'rub_array'=>$rub_array,
    'mselector'=>$mselector,
    'm_id'=>$m_id,
));
?>


<div style="text-align: center; padding-left: 0px; margin-top: 10px; height: 120px; width: 1050px;">
<?
$banner_operator = Yii::app()->params['banners_raspred'][0];
include(Yii::getPathOfAlias('webroot')."/banners/".$banner_operator."/top_horizont.php");
?>
</div>


<div style="margin: 10px 0px 10px 0px">
    <?
    //deb::dump($breadcrumbs);
    $url_parts = array();
    $url = '';
    $bread_count = count($breadcrumbs);
    $i=0;
    foreach ($breadcrumbs as $bkey=>$bval)
    {
        $i++;

        if($bval['type']=='subrubrik')
        {
            $url_parts[$bkey-1] = $bval['transname'];
        }
        else
        {
            $url_parts[$bkey] = $bval['transname'];
        }

        $url = implode("/", $url_parts);;
        ?>
        <a  class="baralink" href="/<?= $url;?>"><?= $bval['name'];?></a>
        <?
        if($i != $bread_count)
        {
            echo "<span class='baralink'> > </span>";
        }
    }
    ?>
</div>




<?
$this->renderPartial('_advertpage', array(
    'mainblock'=>$mainblock,
    'addfield'=>$addfield,
    'uploadfiles_array'=>$this->uploadfiles_array,
    'mainblock_data'=>$this->mainblock_data,
    'addfield_data'=>$this->addfield_data,
    'options'=>$this->options,
    'similar_adverts'=>$similar_adverts,
    'similar_photos'=>$similar_photos,
    'subrub_array'=>$subrub_array,
    'towns_array'=>$towns_array,
    'user'=>$user,
    'path_category'=>$path_category,
    'advert_url_category'=>$breadcrumbs[-2]['transname']."/".$breadcrumbs[0]['transname']."/".$path_category,

));



?>



