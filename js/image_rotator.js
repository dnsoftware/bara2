/**
 * Created by daemon on 25.11.15.
 */

function RotateImage(image, photo_filename_md5, fileload_id)
{
    image.before('<div id="rotate_'+photo_filename_md5+'" style="width: 16px; height: 16px; background-image: url(/images/icons/reload.gif);position: relative; left: 0px; top: 0px; float: left;"></div>');

    rotate = $('#rotate_'+photo_filename_md5);
    rotate.click(function(){
        $.ajax({
            type: 'POST',
            url: '/advert/rotateimage',
            data: 'file='+fileload_id,
            success: function(msg){
            image = $('[md5id = '+ photo_filename_md5+']');
            var src = '/tmp/'+fileload_id + "?" + Math.random();
            image.removeAttr('src');//
            image.attr('src', src);
        }
    });

    });

}
