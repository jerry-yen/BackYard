(function ($) {

    /**
     * 組件下拉選單元件
     * 
     * @param {*} _settings 設定值
     */
    $.widget_component = function (_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'form-control',
            'label': '',
            'source': '',
            'component': $('<div><div class="form-group"><select name="widgetlist"></select></div></div>')
        }, _settings);

        var components = [];

        // 自定義函式
        var coreMethod = {
            initial: function () {
                settings.component
                    .attr('id', settings.id)
                    .attr('name', settings.name);

                var widgetlist = $('select[name="widgetlist"]', settings.component);
                widgetlist.attr('class', settings.class).val(settings.value);

                if (settings.source.indexOf('api://') === 0) {
                    var apiUrl = settings.source.substring(6);
                    $.backyard().process.api('/index.php/api/' + apiUrl, {}, 'GET', function (response) {
                        for (var key in response) {
                            widgetlist.append('<option value="' + response[key] + '">' + response[key] + '</option>');
                        }
                    });
                }

                $('body').on('change', 'div#' + settings.id + ' select[name="widgetlist"]', function () {
                    $('div#' + settings.id + ' > *').not(':first').remove();
                    components = [];
                    $.backyard().process.api('/index.php/api/definewidget/user/master/code/' + $(this).val(), {}, 'GET', function (response) {
                        if (response.status != 'success') {
                            return;
                        }

                        // 呈現欄位元件
                        for (var key in response.metadata.fields) {
                            var componentName = response.metadata.fields[key].component + '_component';

                            $.backyard({'userType':'master'}).process.component(response.metadata.fields[key].component, function () {
                                var component = new $[componentName]({
                                    'id': response.metadata.fields[key].frontendVariable,
                                    'name': response.metadata.fields[key].frontendVariable,
                                    'tip': response.metadata.fields[key].fieldTip,
                                    'source': response.metadata.fields[key].source,
                                    'label': response.metadata.fields[key].name
                                });
                                component.initial();

                                var fieldContainer = $('<div class="form-group"></div>');
                                fieldContainer.append(component.label());
                                fieldContainer.append(component.tip());
                                fieldContainer.append(component.invalid());
                                fieldContainer.append('<br />');
                                fieldContainer.append(component.element());
                                component.elementConvertToComponent();

                                settings.component.append(fieldContainer);
                                components[response.metadata.fields[key].frontendVariable] = component;
                            });

                            
                        }

                        // 呈現事件欄位
                        for (var key in response.metadata.events) {

                            var componentName = response.metadata.events[key].component + '_component';


                            $.backyard().process.component(response.metadata.events[key].component, function () {
                                var component = new $[componentName]({
                                    'id': response.metadata.events[key].frontendVariable,
                                    'name': response.metadata.events[key].frontendVariable,
                                    'tip': response.metadata.events[key].fieldTip,
                                    'source': response.metadata.events[key].source,
                                    'label': response.metadata.events[key].name
                                });
                                component.initial();

                                var fieldContainer = $('<div class="form-group"></div>');
                                fieldContainer.append(component.label());
                                fieldContainer.append(component.tip());
                                fieldContainer.append(component.invalid());
                                fieldContainer.append('<br />');
                                fieldContainer.append(component.element());
                                component.elementConvertToComponent();

                                settings.component.append(fieldContainer);
                                components[response.metadata.events[key].frontendVariable] = component;
                            });

                        }
                    });

                });
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
            },
            getName: function () {
                return settings.name;
            },
            getValue: function () {
                var values = {};
                values['code'] = $('select[name="widgetlist"]', settings.component).val();
                for(var key in components){
                    values[key] = components[key].getValue();
                }
                return values;
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
                console.log(value);
                $('select[name="widgetlist"]', settings.component).val(value.code);
                $('select[name="widgetlist"]', settings.component).change();
                console.log(components);
                for(var key in components){
                    console.log(value[key]);
                    components[key].setValue(value[key]);
                }
            }
        };

        return coreMethod;
    };
}(jQuery));