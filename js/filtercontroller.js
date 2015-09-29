/**
 * Created by daemon on 25.09.15.
 */

function changeFilterReload(action)
{
    $.ajax({
        type: 'get',
        url: action,
        data: $('#form_filter').serialize(),
        success: function(msg){

            $('#form_search_filter').html(msg);

        }
    });
}
