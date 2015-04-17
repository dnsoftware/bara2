
<div style="display: inline;" class="div_range_spr_select" id="div_range_spr_select_<?= $parent_rp_id;?>">
    <select id="range_spr_select_<?= $parent_rp_id;?>" class="props_level" onchange="get_range_spr_select(<?= $rp_id;?>, '<?= $parent_rp_id;?>', '<?= $child_rp_id;?>', this.value)">
    <option value="0">-- <?= $range_spr_rubriks_props_row->name;?> --</option>
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

