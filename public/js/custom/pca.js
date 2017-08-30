(function ($) {
    var options = null;

    var defaluts = {
        url: '',
        province: '',
        city: '',
        area: '',
        form: null,
        success: null
    };

    $.extend({
        pca: function (opt) {
            options = $.extend({}, defaluts, opt);
            getAreaJson(options.url, $('select[name=province]').closest('div'));

            options.form.on('select(state)', function (data) {
                var $that = $(data.elem);
                var code = $that.find('option:selected').data('code') || 0;
                if (parseInt(data.value) > 0)
                    getAreaJson(options.url + '?id=' + code, $that.closest("div").next());
                else
                    clearArea($that.closest("div").next());
            });
        }
    });

    function clearArea(el) {
        var name = el.find("select").attr("name");
        var select = "<select name=\"" + name + "\" lay-verify=\"required\" lay-filter=\"state\">";
        select += "<option value=\"0\">请选择 </option>";
        select += "</select>";
        el.html(select);
        form.render('select');
        if (el.next().find('select').length > 0) {
            clearArea(el.next());
        }
    }

    function getAreaJson(urls, even) {
        $.getJSON(urls, function (json) {
            var pid = 0;
            var name = even.find("select").attr("name");
            var select = "<select name=\"" + name + "\" lay-verify=\"required\" lay-filter=\"state\">";
            select += "<option value=\"\">请选择 </option>";
            $(json).each(function () {
                select += "<option data-code=\"" + this.code + "\" value=\"" + this.id + "\"";
                if (options.province == this.value || options.city == this.value || options.area == this.value) {
                    select += " selected=\"selected\" ";
                    pid = this.code;
                }
                select += ">" + this.value + "</option>";
            });
            select += "</select>";
            even.html(select);
            var nextName = even.next().find("select").attr("name");
            even.next().html("<select name=\"" + nextName + "\" lay-verify=\"required\" lay-filter=\"state\"><option value=\"\">请选择 </option></select>");
            options.form.render('select');
            if (pid != 0) {
                getAreaJson(options.url + "?id=" + pid, even.next());
            }
        });
    }

})(jQuery);