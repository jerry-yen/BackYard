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
            'submit_button_selector': 'button.submit'
        }, _settings);

        var components = [];
        var listFields = [];

        // 自定義函式
        var coreMethod = {
            /**
             * @var 事件物件
             */
            event: {
                /**
                 * 初始化：載入Metadata並將介面擺放定位
                 */  
                tableInitial: function () {
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
                    $('table thead tr th', settings.instance).not(':first').remove();
                    for (var key in listFields) {
                        $('table thead tr', settings.instance).append('<th>' + listFields[key].name + '</th>');
                    }

                    var response = $.backyard({ 'userType': settings.userType }).metadata.dataset(settings.code);
                    if (response.status != 'success') {
                        return;
                    }

                    this.addEvent();
                    this.modifyEvent();
                },

                /**
                 * 載入資料
                 */
                tableLoadData: function () {

                    $.backyard({ 'userType': settings.userType }).process.api(
                        '/index.php/api/items/user/' + settings.userType + '/code/' + settings.code,
                        {},
                        'GET',
                        function (response) {
                            // 將資料代入到各個欄位
                            if (response.status == 'success') {
                                $('table tbody tr', settings.instance).not('.d-none').remove();
                                for (var index in response.results) {
                                    // 呈現欄位資料
                                    var tr = $('table tbody tr.d-none', settings.instance).clone();
                                    for (var key in listFields) {
                                        tr.removeClass('d-none');
                                        tr.append('<td>' + response.results[index][listFields[key].frontendVariable] + '</td>');
                                    }
                                    tr.attr('id', response.results[index]['id']);
                                    $('table tbody', settings.instance).append(tr);
                                }
                            }
                        }
                    );

                },
                formInitial: function () {
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

                    this.submitEvent();
                },
                formLoadData: function (id) {
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
                                if (response.item['id'] != undefined) {
                                    $('div.card-body', settings.instance).append('<input type="hidden" id="id" name="id" value="' + response.item['id'] + '"/>')
                                }
                            }
                        }
                    );
                },
                /**
                 * 新增資料
                 */
                addEvent: function () {
                    $(settings.add_button_selector).click(function () {
                        $('div.' + settings.code + '_table').addClass('d-none');
                        $('div.' + settings.code + '_form').removeClass('d-none');
                        // 清空所有欄位
                        for (var key in components) {
                            components[key].setValue('');
                        }
                    });
                },
                /**
                 * 修改資料
                 */
                modifyEvent: function () {
                    var backyard = this;
                    $('body').on('click', settings.modify_button_selector, function(){
                        $('div.' + settings.code + '_table').addClass('d-none');
                        $('div.' + settings.code + '_form').removeClass('d-none');
                        var id = $(this).closest('tr').attr('id');
                        backyard.formLoadData(id);
                    });
                },
                /**
                 * 送出表單
                 */
                submitEvent: function () {
                    var backyard = this;
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
                                    $('div.' + settings.code + '_table').removeClass('d-none');
                                    $('div.' + settings.code + '_form').addClass('d-none');
                                    backyard.tableLoadData();
                                }
                            }
                        );
                    });
                }
            },
        }

        coreMethod.event.tableInitial();
        coreMethod.event.tableLoadData();
        coreMethod.event.formInitial();

        return coreMethod;
    };
}(jQuery));