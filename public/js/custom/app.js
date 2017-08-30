$(function () {

    Wind.use('artDialog', function () {
        $('a.js-ajax-delete,a.js-ajax-dialog-btn').off('click');
        $(document).on('click', 'a.js-ajax-delete,a.js-ajax-dialog-btn',function (e) {
            e.preventDefault();
            var $_this = this,
                $this = $($_this),
                href = $this.data('href'),
                msg = $this.data('msg');
            href = href ? href : $this.attr('href');
            art.dialog({
                title: false,
                icon: 'question',
                content: msg ? msg : '确定要删除吗？',
                follow: $_this,
                close: function () {
                    $_this.focus();; //关闭时让触发弹窗的元素获取焦点
                    return true;
                },
                okVal: "确定",
                ok: function () {
                    $.getJSON(href).done(function (data) {
                        if (data.state === 'success') {
                            if (data.referer) {
                                location.href = data.referer;
                            } else {
                                reloadPage(window);
                            }
                        } else if (data.state === 'fail') {
                            //art.dialog.alert(data.info);
                            //alert(data.info);//暂时处理方案
                            art.dialog({
                                content: data.info,
                                icon: 'warning',
                                ok: function () {
                                    this.title(data.info);
                                    return true;
                                }
                            });
                        }
                    });
                },
                cancelVal: '关闭',
                cancel: true
            });
        });
    });
});