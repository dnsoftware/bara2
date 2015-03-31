<div id="div_props_sprav_range">

    <div id="div_props_sprav_item_<?= $pt_id;?>">
        <?
        foreach ($props_spav_records as $pskey=>$psval)
        {
            $this->renderPartial('_props_sprav_item_row', array('model'=>$psval));
        }
        ?>
    </div>

</div>
