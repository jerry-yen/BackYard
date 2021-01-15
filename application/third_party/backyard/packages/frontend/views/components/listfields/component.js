(function ($) {

    /**
     * 清單欄位設定元件
     * 
     * @param {*} _settings 設定值
     */
    $.listfields_component = function (_settings) {
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
                    <table>\
                        <thead>\
                            <tr>\
                                <th>&nbsp;</th>\
                                <th>欄位名稱</th>\
                                <th>顯示在清單</th>\
                            </tr>\
                        </thead>\
                        <tbody>\
                        </tbody>\
                    </table>\
                </div>'),
            'emptyItem': '\
                            <tr>\
                                <td><div class="sort-drop"><i class="fas fa-grip-vertical"></i></div></td>\
                                <td class="name"></td>\
                                <td>\
                                    <div class="icheck-primary float-left pr-3">\
                                        <input id="" type="checkbox" name="[]" value="Y" />\
                                        <label for=""></label>\
                                    </div>\
                                </td>\
                            </tr>'
        }, _settings);

        // 自定義函式
        var coreMethod = {
            initial: function () {
                settings.component.attr('id', settings.id);
                $('table', settings.component)
                    .attr('class', settings.class)
                    .attr('name', settings.name);

                $('table tbody tr', settings.component).remove();
                var source = $('#fields select[name="source"]');
                var fieldsValue = $('option:selected', source).attr('fields');
                if (fieldsValue == undefined) {
                    return;
                }
                var fields = JSON.parse($('option:selected', source).attr('fields'));
                for (var key in fields) {
                    var emptyItem = $(settings.emptyItem);
                    $('td.name', emptyItem).html(fields[key].name);
                    $('td input[type="checkbox"]', emptyItem).attr('id', fields[key].fontendVariable);
                    $('td label', emptyItem).attr('for', fields[key].fontendVariable);
                    $('table tbody', settings.component).append(emptyItem);
                }
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

                $('body').on('change', '#fields select[name="source"]', function () {

                    $('table tbody tr', settings.component).remove();

                    var fieldsValue = $('option:selected', $(this)).attr('fields');
                    if (fieldsValue == undefined) {
                        return;
                    }
                    var fields = JSON.parse($('option:selected', $(this)).attr('fields'));
                    for (var key in fields) {
                        var emptyItem = $(settings.emptyItem);
                        $('td.name', emptyItem).html(fields[key].name);
                        $('table tbody', settings.component).append(emptyItem);
                    }
                });

                $('table tbody', settings.component).sortable({
                    handle: "td i.fa-grip-vertical"
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