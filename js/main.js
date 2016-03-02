function user_message_init(getusermessageform_url)
{
    $('#user_message').click( function(event){
        item = $(this);
        event.preventDefault(); // ������a�� ��a��a����� �o�� �������a
        $('#modal_usermessage_overlay').fadeIn(400, // ��a�a�a ��a��o �o�a���a�� ������ �o��o���
            function(){

                $.ajax({
                    url: getusermessageform_url,
                    method: "post",
                    data:{},
                    // ��������� ��������� ���������� �������
                    success: function(data){
                        $('#modal_usermessage_content').html(data);
                    }
                });

                $('#abuse_window').css('display', 'none');

                $('#modal_usermessage')
                    .css('display', 'block') // ����a�� � �o�a���o�o o��a display: none;
                    .animate({opacity: 1, top: '50%'}, 200); // ��a��o ����a����� ��o��a��o��� o��o�������o �o �����a���� ����

            });
    });

    /* �a������ �o�a���o�o o��a, ��� ���a�� �o �� �a�o� �o � o��a��o� �o����� */
    $('#modal_usermessage_close, #modal_usermessage_overlay').click( function(){ // �o��� ���� �o �������� ��� �o��o���
        $('#modal_usermessage')
            .animate({opacity: 0, top: '45%'}, 200,  // ��a��o ������ ��o��a��o��� �a 0 � o��o�������o ����a�� o��o �����
            function(){ // �o��� a���a���
                $(this).css('display', 'none'); // ���a�� ��� display: none;
                $('#modal_usermessage_overlay').fadeOut(400); // �����a�� �o��o���
            }
        );
    });
}
