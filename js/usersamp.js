$(function () {
    $('.delete').click(function () {
        // 関数呼び出し
        deleAjaxCall1(this);
    });

    function deleAjaxCall1(target) {
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

    $('.lock').click(function () {
        // 関数呼び出し
        lockAjaxCall2(this);
    });

    function lockAjaxCall2(target) {
        $.ajax({
            url: 'lockswitch.php',
            dateType: 'json',
            type: 'POST',
            data: {
                'user_id': $(target).prev('.user_id').val()
            }
        }).done(function (data, status, jqXHR) {
            // コール成功の動作
            console.log(data, status, jqXHR);  // TEST
            alert('ロック切り換えしました');
        }).fail(function (jqXHR, status, error) {
            // コール失敗の動作
            console.log(jqXHR, status, error);  // TEST
            alert('ロック切り換えできませんでした');
        });
    }
});
