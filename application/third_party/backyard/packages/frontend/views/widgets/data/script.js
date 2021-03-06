(function ($) {

    /**
     * 清單組件
     * 
     * @param {*} _settings 設定值
     */
    $.fn.backyard_data = function (_settings) {
        var settings = $.extend({
            'userType': 'admin',
            'code': $(this).attr('widget'),
            'instance': this,
            'params': { 'parent_id': '' },
            'add_button_selector': 'button.add',
            'modify_button_selector': 'button.modify',
            'delete_button_selector': 'button.delete',
            'sort_button_selector': 'button.sort',
            'sort_cancel_button_selector': 'button.sort-return',
            'sort_check_button_selector': 'button.check-sort',
            'submit_button_selector': 'button.submit',
            'list_button_selector': 'button.list',
            'return_button_selector': 'button.return',
            'page_button_selector': 'li.page-item.number',
            'prev_page_button_selector': 'li.page-item.prev',
            'next_page_button_selector': 'li.page-item.next',
        }, _settings);

        var components = [];
        var listFields = [];
        var tableLoaded = false;
        var formLoaded = false;
        var sortLoaded = false;

        // 自定義函式
        var widget = {
            util: {
                /**
                 * URL History
                 */
                history: {
                    urls: [],
                    push: function (widget, params) {
                        var item = {};
                        item['widget'] = widget;
                        item['params'] = params;
                        this.urls.push(item);
                    },
                    pop: function () {
                        return this.urls.pop();
                    }
                },
                level: {
                    urls: [],
                    test: function () {
                        console.log(this.urls);
                    },
                    push: function (widget, params) {
                        var item = {};
                        item['widget'] = widget;
                        item['params'] = params;
                        this.urls.push(item);
                    },
                    pop: function () {
                        return this.urls.pop();
                    },
                    getLevel: function () {
                        return this.urls.length;
                    }
                },
            },

            table: {

                initial: function () {

                    widget.util.history.push(settings.code, settings.params);

                    if (tableLoaded) {
                        return;
                    }
                    tableLoaded = true;

                    // 取得組件後設資料
                    var response = $.backyard({ 'userType': settings.userType }).metadata.widget(settings.code);
                    if (response.status != 'success') {
                        return;
                    }

                    // 組件標題
                    $('h3.card-title', settings.instance).html(response.metadata.name);

                    // 取得資料集欄位資訊
                    listFields = response.metadata.widget.listfields;

                    // 呈現欄位元件
                    $('div.table table thead tr th', settings.instance).not(':first').remove();
                    for (var key in listFields) {
                        $('div.table table thead tr', settings.instance).append('<th>' + listFields[key].name + '</th>');
                    }

                    if (response.metadata.widget.permission.indexOf('ADD') > -1) {
                        $('button.add', settings.instance).removeClass('d-none');
                    }

                    if (response.metadata.widget.permission.indexOf('MODIFY') > -1) {
                        $('button.modify', settings.instance).removeClass('d-none');
                    }

                    if (response.metadata.widget.permission.indexOf('DELETE') > -1) {
                        $('button.batch_delete, button.delete', settings.instance).removeClass('d-none');
                    }

                    if (response.metadata.widget.permission.indexOf('SORT') > -1) {
                        $('button.sort', settings.instance).removeClass('d-none');
                    }

                    if (response.metadata.widget.permission.indexOf('EXPORT') > -1) {
                        $('button.export', settings.instance).removeClass('d-none');
                    }

                    if (response.metadata.widget.permission.indexOf('IMPORT') > -1) {
                        $('button.import', settings.instance).removeClass('d-none');
                    }

                    if (widget.util.level.getLevel() > 0) {
                        $('button.return', settings.instance).removeClass('d-none');
                    }
                    else {
                        $('button.return', settings.instance).addClass('d-none');
                    }

                    $('tr td button.list', settings.instance).not('.d-none').remove();

                    // 分類按鈕
                    if (response.metadata.widget.classLevel > 0 && widget.util.level.getLevel() < response.metadata.widget.classLevel - 1) {
                        if ($('button.list[widget="' + settings.code + '"]').length == 0) {
                            var listbutton = $('tr.d-none td button.list.d-none', settings.instance).clone();
                            listbutton
                                .removeClass('d-none')
                                .attr('widget', settings.code)
                                .attr('linkfield', 'parent_id');
                            $('div.table tr.d-none td', settings.instance).append(listbutton);
                        }
                    }

                    // 增加原始欄位的清單按鈕
                    if (response.metadata.widget.sublist != undefined) {
                        response.metadata.widget.sublist = JSON.parse(response.metadata.widget.sublist);

                        if(response.metadata.widget.sublist_level == undefined){
                            response.metadata.widget.sublist_level = '-1';
                        }
                        var levels = response.metadata.widget.sublist_level.split(',');
                        
                        if (
                            (levels.indexOf('-1') > -1 && widget.util.level.getLevel() == response.metadata.widget.classLevel - 1) ||
                            (levels.indexOf((widget.util.level.getLevel() + 1).toString()) > -1)
                        ) {

                            for (var key in response.metadata.widget.sublist) {
                                var listbutton = $('div.table tr.d-none td button.list.d-none', settings.instance).clone();
                                listbutton
                                    .removeClass('d-none')
                                    .attr('widget', response.metadata.widget.sublist[key].widget)
                                    .attr('linkfield', response.metadata.widget.sublist[key].linkfield);
                                if (response.metadata.widget.sublist[key].icon == '') {
                                    response.metadata.widget.sublist[key].icon = 'fas fa-bars';
                                }
                                $('i', listbutton).attr('class', response.metadata.widget.sublist[key].icon);
                                $('span.btitle', listbutton).html(response.metadata.widget.sublist[key].name);

                                $('div.table tr.d-none td', settings.instance).append(listbutton);
                            }
                        }
                    }


                    var response = $.backyard({ 'userType': settings.userType }).metadata.dataset(settings.code);
                    if (response.status != 'success') {
                        return;
                    }

                    // 新增事件
                    widget.table.listener.add();
                    // 修改事件
                    widget.table.listener.modify();
                    // 刪除事件
                    widget.table.listener.delete();
                    // 排序事件
                    widget.table.listener.sort();
                    // 清單瀏覽事件
                    widget.table.listener.list();
                    // 清單回上一層事件
                    widget.table.listener.return();
                    // 分頁事件
                    widget.table.listener.page();

                },

                loadData: function () {
                    console.log(settings);
                    $.backyard({ 'userType': settings.userType }).process.api(
                        '/index.php/api/items/user/' + settings.userType + '/code/' + settings.code,
                        settings.params,
                        'GET',
                        function (response) {
                            // 將資料代入到各個欄位
                            if (response.status == 'success') {
                                $('div.table table tbody tr', settings.instance).not('.d-none').remove();
                                for (var index in response.results) {
                                    // 呈現欄位資料
                                    var tr = $('div.table table tbody tr.d-none', settings.instance).clone();
                                    for (var key in listFields) {
                                        tr.removeClass('d-none');
                                        tr.append('<td>' + response.results[index][key] + '</td>');
                                    }
                                    tr.attr('id', response.results[index]['id']);

                                    $('div.table table tbody', settings.instance).append(tr);
                                }

                                var totalPage = response.total_page;
                                $('li.page-item.number', settings.instance).not('.d-none').remove();
                                for (var page = 1; page <= totalPage; page++) {
                                    var pageItem = $('li.page-item.number.d-none', settings.instance).clone();
                                    pageItem.removeClass('d-none');
                                    $('a.page-link', pageItem).html(page);
                                    $('a.page-link', pageItem).attr('page', page);
                                    pageItem.insertBefore($('li.page-item.next', settings.instance));
                                }

                                var currentPage = response.current_page;
                                if (currentPage > 1) {
                                    $('li.page-item.prev a', settings.instance).attr('page', currentPage - 1);
                                    $('li.page-item.prev', settings.instance).removeClass('d-none');
                                }
                                else {
                                    $('li.page-item.prev', settings.instance).addClass('d-none');
                                }

                                if (currentPage < totalPage) {
                                    $('li.page-item.next a', settings.instance).attr('page', currentPage + 1);
                                    $('li.page-item.next', settings.instance).removeClass('d-none');
                                }
                                else {
                                    $('li.page-item.next', settings.instance).addClass('d-none');
                                }
                            }
                        }
                    );
                },

                listener: {
                    sort: function () {
                        $('body').off('click', settings.sort_button_selector);
                        $('body').on('click', settings.sort_button_selector, function () {
                            // 切換到排序介面
                            widget.sort.loadData();
                            widget.interface.turn('sort');
                            widget.sort.initial();
                            widget.sort.loadData();
                        });
                    },

                    /**
                     * 新增資料
                     */
                    add: function () {
                        $('body').off('click', settings.add_button_selector);
                        $('body').on('click', settings.add_button_selector, function () {
                            // 清空所有欄位
                            for (var key in components) {
                                components[key].setValue('');
                            }

                            // 切換到表單介面
                            widget.interface.turn('form');
                            widget.form.initial();
                        });
                    },

                    /**
                     * 修改資料
                     */
                    modify: function () {
                        $('body').off('click', settings.modify_button_selector);
                        $('body').on('click', settings.modify_button_selector, function () {

                            // 切換到表單介面
                            widget.interface.turn('form');

                            var id = $(this).closest('tr').attr('id');
                            widget.form.initial();
                            widget.form.loadData(id);
                        });
                    },

                    delete: function () {
                        $('body').off('click', settings.delete_button_selector);
                        $('body').on('click', settings.delete_button_selector, function () {
                            var id = $(this).closest('tr').attr('id');
                            Swal.fire({
                                title: '刪除資料',
                                icon: 'warning',
                                html:
                                    '請注意！確定刪除後資料將<u>無法還原</u>',
                                showCloseButton: true,
                                showCancelButton: true,
                                focusConfirm: false,
                                confirmButtonText:
                                    '刪除',
                                cancelButtonText:
                                    '取消'
                            }).then((result) => {
                                if (result.value) {
                                    $.backyard({ 'userType': settings.userType }).process.api(
                                        '/index.php/api/item/user/' + settings.userType + '/code/' + settings.code,
                                        { 'id': id },
                                        'DELETE',
                                        function (response) {
                                            if (response.status != 'success') {
                                                Swal.fire(
                                                    '發生錯誤！',
                                                    response.message,
                                                    'error'
                                                );
                                            }
                                            else {
                                                Swal.fire(
                                                    '刪除成功',
                                                    '',
                                                    'success'
                                                );
                                                widget.table.loadData();
                                            }
                                        }
                                    );
                                }
                            });
                        });
                    },
                    /**
                     * 清單瀏覽資料
                     */
                    list: function () {

                        $('body').off('click', settings.list_button_selector);
                        $('body').on('click', settings.list_button_selector, function () {

                            // 切換到下一個組件
                            tableLoaded = false;

                            widget.util.level.push(settings.code, settings.params);

                            var widgetCode = $(this).attr('widget');
                            var linkfield = $(this).attr('linkfield');
                            var id = $(this).closest('tr').attr('id');

                            settings.params = {};
                            settings.code = widgetCode;
                            settings.params[linkfield] = id;

                            widget.table.initial();
                            widget.table.loadData();

                        });
                    },

                    return: function () {
                        $('body').off('click', settings.return_button_selector);
                        $('body').on('click', settings.return_button_selector, function () {

                            // 切換到下一個組件
                            tableLoaded = false;

                            var param = widget.util.level.pop();

                            settings.code = param.widget;
                            settings.params = param.params;

                            widget.table.initial();
                            widget.table.loadData();

                        });
                    },

                    page: function () {
                        $('body').off('click', settings.page_button_selector);
                        $('body').on('click', settings.page_button_selector, function () {
                            var page = $('a', $(this)).attr('page');
                            settings.params['page'] = page;
                            widget.table.loadData();
                        });

                        $('body').off('click', settings.prev_page_button_selector);
                        $('body').on('click', settings.prev_page_button_selector, function () {
                            var page = $('a', $(this)).attr('page');
                            settings.params['page'] = page;
                            widget.table.loadData();
                        });

                        $('body').off('click', settings.next_page_button_selector);
                        $('body').on('click', settings.next_page_button_selector, function () {
                            var page = $('a', $(this)).attr('page');
                            settings.params['page'] = page;
                            widget.table.loadData();
                        });
                    }
                }



            },
            form: {
                initial: function () {

                    $('div.card-body input[id="id"]', settings.instance).remove();

                    if (formLoaded) {
                        return;
                    }
                    formLoaded = true;

                    // 取得組件後設資料
                    var response = $.backyard({ 'userType': settings.userType }).metadata.widget(settings.code);
                    if (response.status != 'success') {
                        return;
                    }
                    // 組件標題
                    $('h3.card-title', settings.instance).html(response.metadata.name);

                    // 取得資料集欄位資訊
                    var response = $.backyard({ 'userType': settings.userType }).metadata.dataset(settings.code);
                    if (response.status != 'success') {
                        return;
                    }
                    var fields = response.dataset.fields;

                    $('div.form div.card-body *', settings.instance).remove();

                    // 呈現欄位元件
                    for (var key in fields) {
                        var componentName = fields[key].component + '_component';
                        var component = new $[componentName]({
                            'id': fields[key].frontendVariable,
                            'name': fields[key].frontendVariable,
                            'tip': fields[key].fieldTip,
                            'source': fields[key].source,
                            'label': fields[key].name,
                            'userType' : settings.userType,
                            'code': settings.code
                        });
                        component.initial();

                        var fieldContainer = $('<div class="form-group"></div>');
                        fieldContainer.append(component.label());
                        fieldContainer.append(component.tip());
                        fieldContainer.append(component.invalid());
                        fieldContainer.append('<br />');
                        fieldContainer.append(component.element());
                        
                        $('div.form div.card-body', settings.instance).append(fieldContainer);
                        component.elementConvertToComponent();

                        components[fields[key].frontendVariable] = component;
                    }

                    widget.form.listener.submit();
                },

                loadData: function (id) {
                    $.backyard({ 'userType': settings.userType }).process.api(
                        '/index.php/api/item/user/' + settings.userType + '/code/' + settings.code + '?id=' + id,
                        {},
                        'GET',
                        function (response) {
                            // 將資料代入到各個欄位
                            if (response.status == 'success') {
                                for (var fieldName in response.item) {
                                    if (components[fieldName] != undefined) {
                                        components[fieldName].setValue(response.item[fieldName]);
                                    }
                                }

                                // 如果預設有id，代表為修改模式
                                if (response.item.id != undefined && $('div.card-body input[id="id"]', settings.instance).length == 0) {
                                    $('div.card-body', settings.instance).append('<input type="hidden" id="id" name="id" value="' + response.item['id'] + '"/>')
                                }
                            }
                        }
                    );
                },

                listener: {
                    submit: function () {
                        $(settings.submit_button_selector).click(function () {

                            var data = {};

                            // 取得所有隱藏欄位值，包含id
                            $('input[type="hidden"]').each(function () {
                                data[$(this).attr('name')] = $(this).val();
                            });

                            var lastKey = '';
                            var lastValue = '';
                            for (var key in settings.params) {
                                lastKey = key;
                                lastValue = settings.params[key];
                            }
                            data[lastKey] = lastValue;

                            // 取得各欄位(元件)的值
                            for (var key in components) {
                                data[components[key].getName()] = components[key].getValue();
                                components[key].setInvalid('');
                            }
                            httpType = (data['id'] != undefined) ? 'PUT' : 'POST';
                            $.backyard({ 'userType': settings.userType }).process.api(
                                '/index.php/api/item/user/' + settings.userType + '/code/' + settings.code,
                                data,
                                httpType,
                                function (response) {
                                    console.log(response);
                                    if (response.status != 'success') {
                                        // 欄位驗證失敗
                                        if (response.code == 'validator') {
                                            for (var fieldName in response.message) {
                                                components[fieldName].setInvalid(response.message[fieldName]);
                                            }
                                        }
                                    }
                                    else {
                                        // 切換到清單介面
                                        widget.interface.turn('table');
                                        widget.table.initial();
                                        widget.table.loadData();
                                    }
                                }
                            );
                        });
                    }
                }


            },

            sort: {
                initial: function () {

                    if (sortLoaded) {
                        return;
                    }
                    sortLoaded = true;

                    // 取得組件後設資料
                    var response = $.backyard({ 'userType': settings.userType }).metadata.widget(settings.code);
                    if (response.status != 'success') {
                        return;
                    }
                    // 組件標題
                    $('h3.card-title', settings.instance).html(response.metadata.name);

                    // 取得資料集欄位資訊
                    listFields = response.metadata.widget.listfields;

                    // 呈現欄位元件
                    $('div.sort table thead tr th', settings.instance).not(':first').remove();
                    for (var key in listFields) {
                        $('div.sort table thead tr', settings.instance).append('<th>' + listFields[key].name + '</th>');
                    }

                    var response = $.backyard({ 'userType': settings.userType }).metadata.dataset(settings.code);
                    if (response.status != 'success') {
                        return;
                    }

                    widget.sort.loadData();
                    widget.sort.listener.check();
                    widget.sort.listener.cancel();

                    $('div.sort table tbody').sortable({
                        handle: "td i.fa-grip-vertical"
                    });
                },

                loadData: function () {
                    var params = $.extend({ 'count': '-1' }, settings.params);
                    $.backyard({ 'userType': settings.userType }).process.api(
                        '/index.php/api/items/user/' + settings.userType + '/code/' + settings.code,
                        params,
                        'GET',
                        function (response) {
                            // 將資料代入到各個欄位
                            if (response.status == 'success') {
                                $('div.sort table tbody tr', settings.instance).not('.d-none').remove();
                                for (var index in response.results) {
                                    // 呈現欄位資料
                                    var tr = $('div.sort table tbody tr.d-none', settings.instance).clone();
                                    for (var key in listFields) {
                                        tr.removeClass('d-none');
                                        tr.append('<td>' + response.results[index][key] + '</td>');
                                    }
                                    tr.attr('id', response.results[index]['id']);
                                    $('div.sort table tbody', settings.instance).append(tr);
                                }
                            }
                        },
                        null,
                        true
                    );
                },

                listener: {
                    check: function () {
                        $('body').on('click', settings.sort_check_button_selector, function () {
                            var data = { 'condition': [], 'value': [] };
                            $('div.sort table tbody tr', settings.instance).each(function (sequence) {
                                var date = new Date();
                                var year = date.getFullYear();
                                var month = (date.getMonth() + 1) < 10 ? ('0' + (date.getMonth() + 1)) : (date.getMonth() + 1);
                                var day = date.getDate() < 10 ? ('0' + date.getDate()) : date.getDate();
                                var hour = date.getHours() < 10 ? ('0' + date.getHours()) : date.getHours();
                                var min = date.getMinutes() < 10 ? ('0' + date.getMinutes()) : date.getMinutes();
                                var sec = date.getSeconds() < 10 ? ('0' + date.getSeconds()) : date.getSeconds();
                                currentDatetime = year + '-' + month + '-' + day;
                                currentDatetime += ' ' + hour + ':' + min + ':' + sec;
                                data['condition'].push($(this).attr("id"));
                                data['value'].push({ 'sequence': sequence, 'sorted_at': currentDatetime });
                            });

                            $.backyard({ 'userType': settings.userType }).process.api(
                                '/index.php/api/items/user/' + settings.userType + '/code/' + settings.code,
                                data,
                                'PUT',
                                function (response) {
                                    // 切換到清單介面
                                    widget.interface.turn('table');
                                    widget.table.initial();
                                    widget.table.loadData();
                                }
                            );
                        });
                    },

                    cancel: function () {
                        $('body').on('click', settings.sort_cancel_button_selector, function () {
                            // 切換到表單介面
                            widget.interface.turn('table');
                            widget.table.initial();
                            widget.table.loadData();
                        });
                    }
                }
            },

            /**
             * @var 界面
             */
            interface: {
                /**
                 * 介面切換
                 * @param {*} interfaceName 介面名稱(table|form|sort)
                 */
                turn: function (interfaceName) {
                    // 隱藏組件中的所有介面
                    $('div.table', settings.instance).addClass('d-none');
                    $('div.form', settings.instance).addClass('d-none');
                    $('div.sort', settings.instance).addClass('d-none');

                    // 顯示指定的介面
                    $('div.' + interfaceName, settings.instance).removeClass('d-none');
                }
            },
        }

        widget.table.initial();
        widget.table.loadData();

        return widget;
    };
}(jQuery));