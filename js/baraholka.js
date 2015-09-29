/**
 * Created by daemon on 27.09.15.
 */

function displayHide(obj_id)
{
    if($('#'+obj_id).css('display') == 'none')
    {
        $('#'+obj_id).css('display', 'block');
    }
    else
    {
        $('#'+obj_id).css('display', 'none');
    }

}