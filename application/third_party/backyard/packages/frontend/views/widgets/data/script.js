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
                    console.log(response);
                    if (response.status != 'success') {
                        return;
                    }
                    // 組件標題
                    $('h3.card-title', settings.instance).html(response.metadata.name);

                    // 取得資料集欄位資訊
                   
                    var fields = response.metadata.listfields;
                    console.log(fields);
                    // 呈現欄位元件
                    $('table thead tr th', settings.instance).remove();
                    for (var key in fields) {
                        $('table thead tr', settings.instance).append('<th>' + fields[key].name + '</th>');
                    }

                    var response = $.backyard({ 'userType': settings.userType }).metadata.dataset(settings.code);
                    if (response.status != 'success') {
                        return;
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