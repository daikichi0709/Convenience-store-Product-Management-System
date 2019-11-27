$(function () {
    $('.push').click(function () {
        // 関数呼び出し
        ajaxCall(this);
    });


    function ajaxCall(target) {
        $.ajax({
            url: 'del_product.php',
            dateType: 'json',
            type: 'POST',
            data: {
                'item_id' : $(target).prev('.item_id').val()
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
