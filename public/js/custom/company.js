
$(function () {
    Wind.css('select2');
    Wind.use('select2', function () {

        Wind.use('https://cdn.bootcss.com/select2/4.0.3/js/i18n/zh-CN.js', function () {
            $('#pid,#company_id,#community_id').select2({language: "zh-CN"});
            $("#user_id").select2({
                ajax: {
                    url: typeof (user_json_url) != 'undefined' ? user_json_url : '',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            p: params.page
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.result,
                            pagination: {
                                more: (params.page * 20) < data.total_count
                            }
                        };
                    },
                    cache: true
                },
                escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
                minimumInputLength: 1,
                templateResult: formatRepo, // omitted for brevity, see the source of this page
                templateSelection: formatRepoSelection, // omitted for brevity, see the source of this page
                language: "zh-CN"
            });
        });
    });

    function formatRepo (repo) {
        console.log(repo);
        if (repo.loading) return repo.text;
        var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__avatar'><img src='"+ avatar_url +"/id/"+ repo.id +"' /></div>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'>" + repo.user_login + "</div>";

        if (repo.signature) {
            markup += "<div class='select2-result-repository__description'>" + repo.signature + "</div>";
        }

        return markup;
    }

    function formatRepoSelection (repo) {
        return repo.user_login || repo.user_nicename || repo.text;
    }
});

if (typeof module_json_url != 'undefined') {
    function get_module(id, checked, selected_company) {
        if (id != selected_company) {
            checked = '';
        }

        $('.js-ajax-submit').addClass('disabled');
        $.get(module_json_url+'?company_id='+id, function (data) {
            $('.js-ajax-submit').removeClass('disabled');
            if (data.state == 'success') {
                $('#modules').parent().show();
                var str = '';
                $.each(data.modules, function (i, v) {
                    var checked_text = '';
                    if (checked.split(',').indexOf(String(v.module_id)) >= 0) {
                        checked_text = 'checked';
                    }
                    str += '<label class="checkbox inline">' +
                        '<input type="checkbox" name="module[]" value="'+v.module_id+'" '+checked_text+'>' + v.name +
                        '</label>';
                });
                $('#modules').html(str);
            } else {
                $('#modules').parent().hide();
            }
        })
    }
}

if (typeof get_parent_url != 'undefined') {
    function get_parent_data(company_id, community_id, pid) {
        $.get(get_parent_url,{company_id:company_id,community_id:community_id,parentid:pid}, function (data) {
            if (data.state == 'success') {
                $('#pid').html(data.html);
            } else {
                $('#pid').html('<option value="0">最上级</option>');
            }
        });
    }
}

if (typeof get_community_url != 'undefined') {
    function get_community(company_id, community_id) {
        $.get(get_community_url,{company_id:company_id}, function (data) {
            if (data.state == 'success') {
                var str = '';
                $.each(data.communities, function (i, v) {
                    var selected = '';
                    if (parseInt(v.community_id) == parseInt(community_id)){
                        selected = 'selected';
                    }
                    str += '<option value="'+v.community_id+'" '+selected+'>'+v.name+'</option>';
                });
                if (data.communities[0]) {
                    get_parent_data(company_id, data.communities[0].community_id, 0);
                }
                $('#community_id').html(str);
            } else {
                $('#community_id').html('');
            }
        });
    }
}