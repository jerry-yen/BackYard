(function ($) {

    /**
     * 輸入框元件
     * 
     * @param {*} _settings 設定值
     */
    $.text_component = function (_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'form-control',
            'label': '',
            'component': $('<input type="text">')
        }, _settings);

        // 自定義函式
        var coreMethod = {
            initial: function () {
                settings.component
                    .attr('id', settings.id)
                    .attr('class', settings.class)
                    .attr('name', settings.name)
                    .val(settings.value);
            },
            tip: function () {
                return $('<tip for="' + settings.id + '">' + settings.label + '</tip>');
            },
            label: function () {
                return $('<label for="' + settings.id + '">' + settings.label + ' : </label>');
            },
            element: function () {
                return settings.component;
            }
        };

        return coreMethod;
    };
}(jQuery));