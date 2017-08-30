GV.DETAIL_ID = 'detail_form';
var _objDetail = null;

function showLoading(cDom) {
    layui.use('layer', function () {
        layui.layer.open({
            type: 3,
            icon: 2,
            resize: false,
            shade: 0.01,
            success: function (e, index) {
                if (cDom && $(cDom).length > 0) {
                    $(e).css('left', ($(cDom).width() / 2 + 16) + 'px');
                    $(e).css('top', ($(cDom).height() / 2 + 16) + 'px');
                    $(e).css('position', 'absolute');
                    $(e).prev().appendTo(cDom);
                    $(e).appendTo(cDom);
                }
            }
        });
    });
}

function showDetailLoading() {
    showLoading('#' + GV.DETAIL_ID);
}

function closeLoading() {
    layui.use('layer', function () {
        layui.layer.closeAll('loading');
    });
}

function closeDetail() {
    if (_objDetail != null) {
        layer.close(_objDetail);
    }
}

function showDetail(cUrl) {
    if (cUrl) {
        if ($('#' + GV.DETAIL_ID).length > 0) {
            _load();
        }
        else {
            var iTop = $('#divBody').position().top;
            var iHeight = $('#divBody').height();
            var iBodyWidth = $(window).width();
            var iWidth = 800;

            if (iWidth > iBodyWidth) {
                iWidth = iBodyWidth;
            }

            var iLeft = iBodyWidth + iWidth;

            layui.use('layer', function () {
                _objDetail = layui.layer.open({
                    title: '',
                    type: 1,
                    id: GV.DETAIL_ID,
                    fixed: false,
                    shade: false,
                    moveOut: false,
                    closeBtn: false,
                    move: false,
                    title: false,
                    offset: [iTop + 'px', iLeft + 'px'],
                    area: [iWidth + 'px', iHeight + 'px'],
                    anim: 2,
                    success: function (e, index) {
                        _load();
                        $(e).css({
                            left: 'auto',
                            zIndex: 999
                        });
                        $(e).css('right', iWidth * -1 - 10 + 'px');
                        $(e).animate({ right: "0px" });
                    }
                });
            });
        }

        function _load() {
            $('#' + GV.DETAIL_ID).empty();
            showDetailLoading();
            $.ajax({
                url: cUrl, dataType: 'html', success: function (ci_strHtml) {
                    try {
                        var objResult = eval('(' + ci_strHtml + ')');
                        if (isJson(objResult)) {
                            layer.msg(objResult.info, {
                                icon: 5
                            });
                            closeDetail();
                            return;
                        }
                    } catch (ex) { }
                    $('#' + GV.DETAIL_ID).html(ci_strHtml);
                    layui.use('form', function () {
                        layui.form().render();
                    });
                }
            });
        }
    }
}

function postData(options) {
    if (options != undefined && options.url != undefined) {
        var bShowMask = true;
        if (options.showMask != undefined) {
            bShowMask = options.showMask;
        }
        if (bShowMask) {
            showDetailLoading();
        }

        var m_params = {};
        try {
            m_params = options.params;
        }
        catch (ex) { }
        $.post(options.url, m_params, function (ci_result) {
            closeLoading();
            if (options.success != undefined) {
                options.success(ci_result);
            }
        }, "json").error(function (err) {
            closeLoading();
            if (options.failure != undefined) {
                options.failure(ci_result);
            } else {
                console.log(JSON.stringify(err));
                layer.msg('操作失败，请重试或通知管理员', {
                    icon: 5
                });
            }
        });
    }
}

function initEdit() {
    $.each($('input[group="edit"]'), function () {
        $(this).val($(this).attr('data-origin'));
    });

    $.each($('#divInput').find('div.layui-form-checkbox'), function () {
        if ($(this).prev().attr('data-origin') == '1') {
            if (!$(this).hasClass('layui-form-checked')) {
                $(this).click();
            }
        } else {
            if ($(this).hasClass('layui-form-checked')) {
                $(this).click();
            }
        }
    });
    $.each($('#divInput').find('div.layui-form-switch'), function () {
        if ($(this).prev().attr('data-origin') == '1') {
            if (!$(this).hasClass('layui-form-onswitch')) {
                $(this).click();
            }
        } else {
            if ($(this).hasClass('layui-form-onswitch')) {
                $(this).click();
            }
        }
    });
    $.each($('#divInput').find('select'), function () {
        if ($(this).attr('data-origin')) {
            $(this).next().find('dd[lay-value="' + $(this).attr('data-origin') + '"]').click();
        }
    });
}

function noDetail() {
    $('#tabList').bootstrapTable('refresh');
    closeDetail();
    layer.msg('记录不存在或已删除！', {
        time: 2000
    });
}

function deleteDetail(detail_id, post_url, table_id) {
    layui.use('layer', function () {
        layer.msg('确认要删除该记录吗？', {
            icon: 2,
            time: 20000, //20s后自动关闭
            shade: [0.2, '#222'],
            btn: ['确认', '取消'],
            yes: function (index, layero) {
                layer.close(index);
                postData({
                    url: post_url,
                    params: { id: detail_id },
                    success: function (result) {
                        switch (result.status) {
                            case 0:
                                $('#' + table_id).bootstrapTable('refresh');
                                layer.msg('删除成功', {
                                    time: 2000
                                });
                                closeDetail();
                                break;
                            case 1:
                                $('#' + table_id).bootstrapTable('refresh');
                                layer.msg(result.info, {
                                    time: 2000
                                });
                                closeDetail();
                                break;
                            case -99:
                                noDetail();
                                break;
                            default:
                                layer.msg(result.content, {
                                    icon: 5
                                });
                                break;
                        }
                    }
                });
            }
        });
    });
}

function restoreDetail(msg, post_url, table_id) {
    layui.use('layer', function () {
        layer.msg(msg, {
            icon: 3,
            time: 20000, //20s后自动关闭
            shade: [0.2, '#222'],
            btn: ['确认', '取消'],
            yes: function (index, layero) {
                layer.close(index);
                postData({
                    url: post_url,
                    success: function (result) {
                        switch (result.status) {
                            case 1:
                                $('#' + table_id).bootstrapTable('refresh');
                                layer.msg(result.info, {
                                    time: 2000
                                });
                                closeDetail();
                                break;
                            default:
                                layer.msg(result.info, {
                                    icon: 5
                                });
                                break;
                        }
                    }
                });
            }
        });
    });
}

//将Base64编码字符串转换成Ansi编码的字符串
function base64decode(input) {
    var keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var output = "";
    var chr1, chr2, chr3 = "";
    var enc1, enc2, enc3, enc4 = "";
    var i = 0;
    if (input.length % 4 != 0) {
        return "";
    }
    var base64test = /[^A-Za-z0-9\+\/\=]/g;
    if (base64test.exec(input)) {
        return "";
    }
    do {
        enc1 = keyStr.indexOf(input.charAt(i++));
        enc2 = keyStr.indexOf(input.charAt(i++));
        enc3 = keyStr.indexOf(input.charAt(i++));
        enc4 = keyStr.indexOf(input.charAt(i++));
        chr1 = (enc1 << 2) | (enc2 >> 4);
        chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
        chr3 = ((enc3 & 3) << 6) | enc4;
        output = output + String.fromCharCode(chr1);
        if (enc3 != 64) {
            output += String.fromCharCode(chr2);
        }
        if (enc4 != 64) {
            output += String.fromCharCode(chr3);
        }
        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";
    } while (i < input.length);
    return output;
}

//utf-16转utf-8 
function utf8to16(str) {
    var out, i, len, c;
    var char2, char3;
    out = "";
    len = str.length;
    i = 0;
    while (i < len) {
        c = str.charCodeAt(i++);
        switch (c >> 4) {
            case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7:
                // 0xxxxxxx 
                out += str.charAt(i - 1);
                break;
            case 12: case 13:
                // 110x xxxx 10xx xxxx 
                char2 = str.charCodeAt(i++);
                out += String.fromCharCode(((c & 0x1F) << 6) | (char2 & 0x3F));
                break;
            case 14:
                // 1110 xxxx 10xx xxxx 10xx xxxx 
                char2 = str.charCodeAt(i++);
                char3 = str.charCodeAt(i++);
                out += String.fromCharCode(((c & 0x0F) << 12) |
                ((char2 & 0x3F) << 6) |
                ((char3 & 0x3F) << 0));
                break;
        }
    }
    return out;
}

function isJson(obj) {
    var isjson = typeof (obj) == "object" && Object.prototype.toString.call(obj).toLowerCase() == "[object object]" && !obj.length;
    return isjson;
}