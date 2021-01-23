(function ($) {

    /**
     * 組件下拉選單元件
     * 
     * @param {*} _settings 設定值
     */
    $.pagewidgets_component = function (_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'form-control',
            'label': '',
            'source': '',
            'component': $('\
                <div>\
                    <button type="button" class="add_widget btn bg-green float-right">\
                        <i class="fas fa-plus"></i> 新增\
                    </button>\
                    <div class="clearfix"></div>\
                    <div class="widgets">\
                    </div>\
                </div>\
            '),
            'emptyItem': $('\
                <div class="widget-block">\
                    <div class="card-header">\
                        組件資訊\
                    </div>\
                    <div class="form-group">\
                        <label>組件</label>\
                        <select name="widget"></select>\
                    </div>\
                    <div class="form-group">\
                        <label>桌面</label>\
                        <div class="slider-blue">\
                            <input type="text" name="desktop" data-slider-min="1" data-slider-max="12"\
                            data-slider-step="1" data-slider-value="12" data-slider-orientation="horizontal"\
                            data-slider-selection="before" data-slider-tooltip="show">\
                        </div>\
                    </div>\
                    <div class="form-group">\
                        <label>平板</label>\
                        <div class="slider-blue">\
                            <input type="text" name="pad" data-slider-min="1" data-slider-max="12"\
                            data-slider-step="1" data-slider-value="12" data-slider-orientation="horizontal"\
                            data-slider-selection="before" data-slider-tooltip="show">\
                        </div>\
                    </div>\
                    <div class="form-group">\
                        <label>手機</label>\
                        <div class="slider-blue">\
                            <input type="text" name="mobile" data-slider-min="1" data-slider-max="12"\
                            data-slider-step="1" data-slider-value="12" data-slider-orientation="horizontal"\
                            data-slider-selection="before" data-slider-tooltip="show">\
                        </div>\
                    </div>\
                </div>\
            ')
        }, _settings);

        var components = [];

        // 自定義函式
        var coreMethod = {
            initial: function () {
                settings.component
                    .attr('id', settings.id)
                    .attr('name', settings.name);

                $('select', settings.emptyItem)
                    .attr('class', settings.class)


                $.backyard({ 'userType': 'master' }).process.api(
                    '/index.php/api/items/user/master/code/widget',
                    {},
                    'GET',
                    function (response) {
                        $('select', settings.emptyItem).append('<option value="">請選擇</option>');
                        for (var key in response.results) {
                            $('select', settings.emptyItem).append('<option value="' + response.results[key]._code + '">' + response.results[key].name + '</option>');
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
            invalid: function () {
                return $('<invalid for="' + settings.id + '" style="display:none;"></invalid>');
            },
            element: function () {
                return settings.component;
            },
            elementConvertToComponent: function () {

                $('body').on('click', '#' + settings.id + ' button.add_widget', function () {
                    var widget = settings.emptyItem.clone();
                    $('input[type="text"]', widget).bootstrapSlider();
                    $('div.widgets', settings.component).append(widget);
                });

                $('div.widgets', settings.component).sortable({
                    handle: "div.card-header"
                });

            },
            getName: function () {
                return settings.name;
            },
            getValue: function () {
                var widgets = [];
                $('div.widget-block', settings.component).each(function () {
                    var widget = $('select[name="widget"]', $(this)).val();
                    var desktop = $('input[name="desktop"]', $(this)).val();
                    var pad = $('input[name="pad"]', $(this)).val();
                    var mobile = $('input[name="mobile"]', $(this)).val();
                    widgets.push({ 'code': widget, 'desktop': desktop, 'pad': pad, 'mobile': mobile });
                });
                return widgets;
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
                for (var key in value) {
                    var widget = settings.emptyItem.clone();
                    $('select[name="widget"]', widget).val(value[key].code);
                    $('input[type="text"]', widget).bootstrapSlider();
                    $('input[name="desktop"]', widget).bootstrapSlider('setValue', value[key].desktop);
                    $('input[name="pad"]', widget).bootstrapSlider('setValue', value[key].pad);
                    $('input[name="mobile"]', widget).bootstrapSlider('setValue', value[key].mobile);

                    $('div.widgets', settings.component).append(widget);
                }
            }
        };

        return coreMethod;
    };
}(jQuery));