<div style="display: inline;" id="div_range_spr_select_<?= $rp_id;?>">
    <select id="range_spr_select_<?= $rp_id;?>" class="props_level" onchange="get_range_spr_select(<?= $rp_id;?>, '<?= $child_rp_id;?>', this.value)">
    <option value="0">-- выбрать свойство --</option>
    <?
    foreach ($range_spr as $rkey=>$rval)
    {
        ?>
        <option value="<?= $rval->ps_id;?>"><?= $rval->value;?></option>
    <?
    }
      ?>
    </select>
</div>