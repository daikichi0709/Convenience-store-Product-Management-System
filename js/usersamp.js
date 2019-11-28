$(function () {
    $('.push1').click(function () {
        // 関数呼び出し
        ajaxCall1(this);
    });


    function ajaxCall1(target) {
        $.ajax({
            url: 'del_users.php',
            dateType: 'json',
            type: 'POST',
            data: {
                'user_id': $(target).prev('.user_id').val()
            }
        }).done(function (data, status, jqXHR) {
            // コール成功の動作
            console.log(data, status, jqXHR);  // TEST
            alert('削除しました');
        }).fail(function (jqXHR, status, error) {
            // コール失敗の動作
            console.log(jqXHR, status, error);  // TEST
            alert('削除できませんでした');
        });
    }
});
