<?
header("Content-type: text/html; charset=utf-8");
?>
<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="ru" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

    <? Yii::app()->getClientScript()->registerCoreScript( 'jquery.ui' );?>

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css" />

    <style type="text/css">
        html { height: 100% }
        body { height: 100%; margin: 0; padding: 0 }
        #map_canvas { height: 100% }
    </style>
    <script type="text/javascript"
            src="http://maps.googleapis.com/maps/api/js?key=<?= Yii::app()->controller->module->map_api_key;?>&sensor=false">
    </script>
    <script type="text/javascript">
        function initialize() {
            var mapOptions = {
                center: new google.maps.LatLng(-34.397, 150.644),
                zoom: 8,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                draggableCursor: 'default'
            };

            var map = new google.maps.Map(document.getElementById("map_canvas"),
                mapOptions);

            google.maps.event.addListener(map, 'rightclick', function() {
            });

            document.oncontextmenu = function() {return false;}

            $('#map_canvas').mouseup(function(e)
                {

                    if (e.button == 2)
                    {
                        var x = e.pageX - this.offsetLeft;
                        var y = e.pageY - this.offsetTop;

                        $('#popupmenu').css('display', 'block');
                        $('#popupmenu').css('left', e.pageX-5);
                        $('#popupmenu').css('top', e.pageY-5);


                        //alert(x +', '+ y);
                    }
                }
            );

            $('#popupmenu').mouseout(
                function (e)
                {
                    $('#popupmenu').css('display', 'none');
                }
            )


        }


    </script>

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body onload="initialize();">

<div class="" id="">
	<div id="header">
		<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
	</div><!-- header -->

	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">
        Style
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>
