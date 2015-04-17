<form onsubmit="return false;">


<select id="select_subrub" class="selrub" onchange="">
    <option>--- выберите категорию  ---</option>
    <?
    foreach ($rub_array as $rkey=>$rval)
    {
        ?>
        <option disabled style="color:#000; font-weight: bold;" value="<?= $rval['parent']->r_id;?>"><?= $rval['parent']->name;?></option>
        <?
        foreach ($rval['childs'] as $ckey=>$cval)
        {
            ?>
            <option value="<?= $cval->r_id;?>">&nbsp;<?= $cval->name;?></option>
        <?
        }
    }
    ?>
</select>

<div style="color: #f00; display: none;" id="div_errors">

</div>

<div id="div_props" style="margin: 10px;">

</div>


</form>

<script>

$('.selrub').change(function ()
{
$.ajax({
type: 'POST',
url: '/index.php?r=/advert/getrubriksprops',
data: 'r_id='+this.value,
success: function(msg){
$('#div_errors').html('');
$('#div_errors').css('display', 'none');

$('#div_props').html(msg);
}
});
});

</script>