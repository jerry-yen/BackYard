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
            'add_button_selector': 'button.add',
            'modify_button_selector': 'button.modify',
            'delete_button_selector': 'button.delete',
            'sort_button_selector': 'button.sort',
            'sort_cancel_button_selector': 'button.return',
            'sort_check_button_selector': 'button.check-sort',
            'submit_button_selector': 'button.submit'
        }, _settings);

        var components = [];
        var listFields = [];

        // 自定義函式
        var widget = {
            table: {
                initial: function () {
                    // 取得組件後設資料
                    var response = $.backyard({ 'userType': settings.userType }).metadata.widget(settings.code);
                    if (response.status != 'success') {
                        return;
                    }
                    // 組件標題
                    $('h3.card-title', settings.instance).html(response.metadata.name);

                    // 取得資料集欄位資訊
                    listFields = response.metadata.listfields;

                    // 呈現欄位元件
                    $('div.' + settings.code + '_table table thead tr th', settings.instance).not(':first').remove();
                    for (var key in listFields) {
                        $('div.' + settings.code + '_table table thead tr', settings.instance).append('<th>' + listFields[key].name + '</th>');
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
                },

                loadData: function () {
                    $.backyard({ 'userType': settings.userType }).process.api(
                        '/index.php/api/items/user/' + settings.userType + '/code/' + settings.code,
                        {},
                        'GET',
                        function (response) {
                            // 將資料代入到各個欄位
                            if (response.status == 'success') {
                                $('div.' + settings.code + '_table table tbody tr', settings.instance).not('.d-none').remove();
                                for (var index in response.results) {
                                    // 呈現欄位資料
                                    var tr = $('div.' + settings.code + '_table table tbody tr.d-none', settings.instance).clone();
                                    for (var key in listFields) {
                                        tr.removeClass('d-none');
                                        tr.append('<td>' + response.results[index][listFields[key].frontendVariable] + '</td>');
                                    }
                                    tr.attr('id', response.results[index]['id']);
                                    $('div.' + settings.code + '_table table tbody', settings.instance).append(tr);
                                }
                            }
                        }
                    );
                },

                listener: {
                    sort: function () {
                        $('body').on('click', settings.sort_button_selector, function () {
                            // 切換到排序介面
                            widget.sort.loadData();
                            widget.interface.turn('sort');
                        });
                    },

                    /**
                     * 新增資料
                     */
                    add: function () {
                        $(settings.add_button_selector).click(function () {
                            // 清空所有欄位
                            for (var key in components) {
                                components[key].setValue('');
                            }

                            // 切換到表單介面
                            widget.interface.turn('form');
                        });
                    },

                    /**
                     * 修改資料
                     */
                    modify: function () {
                        $('body').on('click', settings.modify_button_selector, function () {

                            // 切換到表單介面
                            widget.interface.turn('form');

                            var id = $(this).closest('tr').attr('id');
                            widget.form.loadData(id);
                        });
                    },

                    delete: function () {
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
                    }
                }



            },
            form: {
                initial: function () {
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

                    // 呈現欄位元件
                    for (var key in fields) {
                        var componentName = fields[key].component + '_component';
                        var component = new $[componentName]({
                            'id': fields[key].frontendVariable,
                            'name': fields[key].frontendVariable,
                            'tip': fields[key].fieldTip,
                            'source': fields[key].source,
                            'label': fields[key].name
                        });
                        component.initial();

                        var fieldContainer = $('<div class="form-group"></div>');
                        fieldContainer.append(component.label());
                        fieldContainer.append(component.tip());
                        fieldContainer.append(component.invalid());
                        fieldContainer.append('<br />');
                        fieldContainer.append(component.element());
                        component.elementConvertToComponent();

                        $('div.' + settings.code + '_form div.card-body', settings.instance).append(fieldContainer);

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
                                if (response.item.id != undefined) {
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
                    // 取得組件後設資料
                    var response = $.backyard({ 'userType': settings.userType }).metadata.widget(settings.code);
                    if (response.status != 'success') {
                        return;
                    }
                    // 組件標題
                    $('h3.card-title', settings.instance).html(response.metadata.name);

                    // 取得資料集欄位資訊
                    listFields = response.metadata.listfields;

                    // 呈現欄位元件
                    $('div.' + settings.code + '_sort table thead tr th', settings.instance).not(':first').remove();
                    for (var key in listFields) {
                        $('div.' + settings.code + '_sort table thead tr', settings.instance).append('<th>' + listFields[key].name + '</th>');
                    }

                    var response = $.backyard({ 'userType': settings.userType }).metadata.dataset(settings.code);
                    if (response.status != 'success') {
                        return;
                    }

                    widget.sort.loadData();
                    widget.sort.listener.check();
                    widget.sort.listener.cancel();

                    $('div.' + settings.code + '_sort table tbody').sortable();
                },

                loadData: function () {
                    $.backyard({ 'userType': settings.userType }).process.api(
                        '/index.php/api/items/user/' + settings.userType + '/code/' + settings.code,
                        {},
                        'GET',
                        function (response) {
                            // 將資料代入到各個欄位
                            if (response.status == 'success') {
                                $('div.' + settings.code + '_sort table tbody tr', settings.instance).not('.d-none').remove();
                                for (var index in response.results) {
                                    // 呈現欄位資料
                                    var tr = $('div.' + settings.code + '_sort table tbody tr.d-none', settings.instance).clone();
                                    for (var key in listFields) {
                                        tr.removeClass('d-none');
                                        tr.append('<td>' + response.results[index][listFields[key].frontendVariable] + '</td>');
                                    }
                                    tr.attr('id', response.results[index]['id']);
                                    $('div.' + settings.code + '_sort table tbody', settings.instance).append(tr);
                                }
                            }
                        }
                    );
                },

                listener: {
                    check: function () {
                        $('body').on('click', settings.sort_check_button_selector, function () {
                            $.backyard({ 'userType': settings.userType }).process.api(
                                '/index.php/api/item/user/' + settings.userType + '/code/' + settings.code,
                                data,
                                'PUT',
                                function (response) {
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

                                        widget.table.loadData();
                                    }
                                }
                            );
                            // 切換到表單介面
                            widget.interface.turn('table');
                        });
                    },

                    cancel: function () {
                        $('body').on('click', settings.sort_cancel_button_selector, function () {
                            // 切換到表單介面
                            widget.interface.turn('table');
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
                    $('div.' + settings.code + '_table').addClass('d-none');
                    $('div.' + settings.code + '_form').addClass('d-none');
                    $('div.' + settings.code + '_sort').addClass('d-none');

                    // 顯示指定的介面
                    $('div.' + settings.code + '_' + interfaceName).removeClass('d-none');
                }
            },
        }

        widget.table.initial();
        widget.form.initial();
        widget.sort.initial();
        widget.table.loadData();

        return widget;
    };
}(jQuery));