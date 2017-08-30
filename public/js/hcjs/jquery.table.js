/**
 * @author Link
 * @version v1.1.4
 */

!function ($) {

    'use strict';

    $.extend($.fn.bootstrapTable.defaults, {
        method: 'get',
        striped: false,
        sortable: true,
        sortStable: true,
        silent: false,
        //在表格底部显示分页工具栏
        pagination: true,
        pageSize: 10,
        pageNumber: 1,
        pageList: [10, 20, 50, 100],
        //名片格式
        showToggle: false,
        //设置为True时显示名片（card）布局
        cardView: false,
        //显示隐藏列
        showColumns: true,
        //显示刷新按钮
        showRefresh: true,
        //复选框只能选择一条记录
        singleSelect: true,
        //是否显示右上角的搜索框
        search: false,
        //点击行即可选中单选/复选框
        clickToSelect: true,
        //表格分页的位置
        sidePagination: "server",
        undefinedText: "--"
    });
}(jQuery);

function rowSelected(e) {
    $('.rowselected').removeClass('rowselected');
    $(e).addClass('rowselected');
}

function stateFormatter(value, row) {
    return row.state_desc;
}

function createUserFormatter(value, row) {
    return row.create_user_name;
}

function updateUserFormatter(value, row) {
    return row.update_user_name;
}

function catFormatter(value, row) {
    return row.cat_desc;
}

function typeFormatter(value, row) {
    return row.type_desc;
}

function searchState(value) {
    $('[name="state"]').next().find('dd[lay-value="' + value + '"]').click()
}