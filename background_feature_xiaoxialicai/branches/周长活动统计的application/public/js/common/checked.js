setTimeout(selectPage, 200);
function selectPage() {
    if (ids.length > 0) {
        $('input[name=ids]').each(function () {
            for (var i in ids) {
                if (ids[i] == $(this).val()) {
                    $(this).iCheck('check');

                }
            }
        });
    }
}