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

                $('body').on('change', '#source select[name="source"]', function () {

                    $('table tbody tr', settings.component).remove();

                    var fieldsValue = $('option:selected', $(this)).attr('fields');
                    if (fieldsValue == undefined) {
                        return;
                    }
                    var fields = JSON.parse($('option:selected', $(this)).attr('fields'));
                    for (var key in fields) {
                        var emptyItem = $(settings.emptyItem);
                        $('td.name', emptyItem).html(fields[key].name);
                        $('td input[type="checkbox"]', emptyItem)
                            .attr('id', '_' + fields[key].fontendVariable)
                            .attr('title', fields[key].name)
                            .attr('name', fields[key].fontendVariable);
                        $('td label', emptyItem).attr('for', '_' + fields[key].fontendVariable);
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
                var fields = {};
                $('input[type="checkbox"]', settings.component).each(function(){
                    fields[$(this).attr('name')]={'status':$(this).prop('checked')?'Y':'N','name':$(this).attr('title')};
                });

                return fields;
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

                console.log(value);

                $('#source select[name="source"]').change();
                var sortIndex = 0;
                for(var key in value){
                    $('input[name="' + key+ '"]').prop('checked',(value[key].status == 'Y'));
                    $('input[name="' + key+ '"]').attr('sequence', sortIndex++);
                }

                $('table tbody tr', settings.component).sort(function(a, b){
                    var seq1 = $('input[type="checkbox"]', a).attr('sequence');
                    var seq2 = $('input[type="checkbox"]', b).attr('sequence');
                    console.log(seq1 + '--' + seq2);
                    return (seq1 > seq2) ? 1 : -1;
                }).appendTo($('table tbody', settings.component));
               
            }
        };

        return coreMethod;
    };
}(jQuery));