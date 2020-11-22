(function ($) {

    /**
     * 開關元件
     * 
     * @param {*} _settings 設定值
     */
    $.switch_component = function (_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'form-control',
            'label': '',
            'source': '',
            'component': $('<input type="checkbox" value="Y">')
        }, _settings);

        // 自定義函式
        var coreMethod = {
            initial: function () {
                settings.component
                    .attr('id', settings.id)
                    //                    .attr('class', settings.class)
                    .attr('name', settings.name);
                //                    .val(settings.value);
                var source = JSON.parse(settings.source);
                settings.component.attr('data-on-text', source[0]);
                settings.component.attr('data-off-text', source[1]);

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
            elementConvertToComponent: function () {
                settings.component.bootstrapSwitch();

            },
            getName: function () {
                return settings.name;
            },
            getValue: function () {
                return (settings.component.closest('div.bootstrap-switch-on').length > 0) ? 'Y' : 'N';
            }
        };

        return coreMethod;
    };
}(jQuery));