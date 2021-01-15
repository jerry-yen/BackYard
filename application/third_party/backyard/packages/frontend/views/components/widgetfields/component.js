(function ($) {

    /**
     * 資料集欄位元件
     * 
     * @param {*} _settings 設定值
     */
    $.widgetfields_component = function (_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'table table-hover text-nowrap',
            'label': '',
            'source': '',
            'component': $('\
                <div>\
                    <select name="source" class="form-control"></select>\
                    <br /><br />\
                    <table>\
                        <thead>\
                            <tr>\
                                <th>名稱</th>\
                                <th>前端變數</th>\
                                <th>後端變數</th>\
                            </tr>\
                        </thead>\
                        <tbody>\
                        </tbody>\
                    </table>\
                </div>'),
            'emptyItem': '\
                            <tr>\
                                <td class="name"></td>\
                                <td class="fontendVariable"></td>\
                                <td class="dbVariable"></td>\
                            </tr>'
        }, _settings);

        // 自定義函式
        var coreMethod = {
            initial: function () {
                settings.component.attr('id', settings.id);
                $('table', settings.component)
                    .attr('class', settings.class)
                    .attr('name', settings.name);

                $.backyard({ 'userType': 'master' }).process.api(
                    '/index.php/api/items/user/master/code/dataset',
                    {},
                    'GET',
                    function (response) {
                        var select = $('select[name="source"]', settings.component);
                        select.append('<option value="">請選擇</option>');
                        for (var key in response.results) {
                            select.append('<option value="' + response.results[key]._code + '" fields=\'' + response.results[key].fields + '\'>' + response.results[key].name + '</option>');
                        }
                    }
                );
            },
            tip: function () {
                return $('<tip for="' + settings.id + '">' + settings.label + '</tip>');
            },
            label: function () {
                return $('<label for="' + settings.id + '">' + settings.label + ' : </label>');
            },
            element: function () {
                return settings.component;
            },
            invalid: function () {
                return $('<invalid for="' + settings.id + '" style="display:none;"></invalid>');
            },
            elementConvertToComponent: function () {

                $('body').on('change', '#' + settings.id + ' select[name="source"]', function () {

                    $('table tbody tr', settings.component).remove();

                    var fieldsValue = $('option:selected', $(this)).attr('fields');
                    if (fieldsValue == undefined) {
                        return;
                    }
                    var fields = JSON.parse($('option:selected', $(this)).attr('fields'));
                    for (var key in fields) {
                        var emptyItem = $(settings.emptyItem);
                        $('td.name', emptyItem).html(fields[key].name);
                        $('td.fontendVariable', emptyItem).html(fields[key].fontendVariable);
                        $('td.dbVariable', emptyItem).html(fields[key].dbVariable);
                        $('table tbody', settings.component).append(emptyItem);
                    }
                });

                // datalist 選中之後，會自動被置制到 input，這時再偵測，如果input內容有在 datalist裡的話，就將datalist的內容刪除
                $('button.add_field', settings.component).click(function () {
                    $('table tbody', settings.component).append(settings.emptyItem);

                    $('input[name="validator"], input[name="converter"]', settings.component).each(function () {
                        var count = $('select option', $(this).closest('td')).length;
                        $(this).attr('placeholder', (count - 1) + '項');
                    });

                    $('table#' + settings.id + ' tbody').sortable({
                        handle: "td i.fa-grip-vertical"
                    });

                });

            },
            getName: function () {
                return settings.name;
            },
            getValue: function () {
                var items = [];
                $('tbody tr', settings.component).each(function () {
                    var item = {};
                    item.name = $('input[name="name"]', $(this)).val();
                    item.fontendVariable = $('input[name="fontendVariable"]', $(this)).val();
                    item.dbVariable = $('input[name="dbVariable"]', $(this)).val();
                    item.component = $('select[name="component"]', $(this)).val();
                    item.source = $('input[name="source"]', $(this)).val();
                    item.fieldTip = $('input[name="fieldTip"]', $(this)).val();
                    item.validator = [];
                    item.converter = [];
                    $('select[name="validatorlist"] option', $(this)).each(function (index) {
                        if (index > 0) {
                            item.validator.push($(this).attr('value'));
                        }
                    });

                    $('select[name="converterlist"] option', $(this)).each(function (index) {
                        if (index > 0) {
                            item.converter.push($(this).attr('value'));
                        }
                    });

                    items.push(item);
                });

                return JSON.stringify(items);
            },
            setInvalid: function (message) {
                var invalid = $('invalid[for="' + settings.id + '"]');
                if (message.trim() != '') {
                    invalid.html(message);
                    invalid.show();
                }
                else {
                    invalid.html('');
                    invalid.hide();
                }
            },
            setValue: function (value) {
                if (value == '') {
                    return;
                }

                $('table tbody tr', settings.component).remove();

                var items = JSON.parse(value);
                for (var i in items) {
                    var item = $(settings.emptyItem).clone();

                    $('input[name="name"]', item).val(items[i].name);
                    $('input[name="fontendVariable"]', item).val(items[i].fontendVariable);
                    $('input[name="dbVariable"]', item).val(items[i].dbVariable);
                    $('select[name="component"]', item).val(items[i].component);

                    if (items[i].validator.length > 0) {
                        for (var v in items[i].validator) {
                            $('select[name="validatorlist"]', item).append('<option value="' + items[i].validator[v] + '">' + items[i].validator[v] + '</option>');
                        }
                        $('input[name="validator"]', item).attr('placeholder', (items[i].validator.length) + '項');
                    }
                    if (items[i].converter.length > 0) {
                        for (var c in items[i].converter) {
                            $('select[name="converterlist"]', item).append('<option value="' + items[i].converter[c] + '">' + items[i].converter[c] + '</option>');
                        }
                        $('input[name="converter"]', item).attr('placeholder', (items[i].converter.length) + '項');
                    }

                    $('input[name="source"]', item).val(items[i].source);
                    $('input[name="fieldTip"]', item).val(items[i].fieldTip);


                    $('table tbody', settings.component).append(item);
                }

                $('table#' + settings.id + ' tbody').sortable({
                    handle: "td i.fa-grip-vertical"
                });
            }
        };

        return coreMethod;
    };
}(jQuery));