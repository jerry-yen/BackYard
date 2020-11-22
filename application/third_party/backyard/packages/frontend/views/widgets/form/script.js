(function ($) {

    /**
     * 表單組件
     * 
     * @param {*} _settings 設定值
     */
    $.fn.backyard_form = function (_settings) {
        var settings = $.extend({
            'userType': 'admin',
            'code': $(this).attr('widget'),
            'instance': this,
            'submit_button_selector': 'button.modify'
        }, _settings);

        var components = [];

        // 自定義函式
        var coreMethod = {
            /**
             * @var 事件物件
             */
            event: {
                /**
                 * 初始化：載入Metadata並將介面擺放定位
                 */
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

                        $('div.card-body', settings.instance).append(fieldContainer);

                        components[fields[key].frontendVariable] = component;
                    }
                },

                /**
                 * 載入資料
                 */
                loadData: function () {
                    $.backyard({ 'userType': settings.userType }).process.api(
                        '/index.php/api/item/user/' + settings.userType + '/code/' + settings.code,
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
                                if (response.item['id'] != undefined) {
                                    $('div.card-body', settings.instance).append('<input type="hidden" id="id" name="id" value="' + response.item['id'] + '"/>')
                                }
                            }
                        }
                    );
                },
                /**
                 * 送出表單
                 */
                submitEvent: function () {
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
                                if (response.status == 'failed') {
                                    // 欄位驗證失敗
                                    if (response.code == 'validator') {
                                        for (var fieldName in response.message) {
                                            components[fieldName].setInvalid(response.message[fieldName]);
                                        }
                                    }
                                }
                                console.log(response);
                            }
                        );
                    });
                }
            },
        }

        coreMethod.event.initial();
        coreMethod.event.submitEvent();
        coreMethod.event.loadData();

        return coreMethod;
    };
}(jQuery));