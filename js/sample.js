$(function () {
    $('.push').click(function () {
        // 関数呼び出し
        ajaxCall();
    });


    function ajaxCall() {
        $.ajax({
            url: 'del_product.php',
            dateType: 'json',
            type: 'POST',
            data: {
                'item_id': $('.item_id').val()
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
