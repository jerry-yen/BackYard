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
            'instance': this
        }, _settings);

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
                    metadata_code = response.metadata.metadata;


                    // 取得資料集欄位資訊
                    var response = $.backyard({ 'userType': settings.userType }).metadata.metadata(settings.code);
                    if (response.status != 'success') {
                        return;
                    }
                    var fields = response.metadata.fields;

                    // 呈現欄位元件
                    for (var key in fields) {
                        var componentName = fields[key].component + '_component';
                        var component = new $[componentName]({
                            'id': fields[key].frontendVariable,
                            'name': fields[key].frontendVariable,
                            'tip': fields[key].fieldTip,
                            'label': fields[key].name
                        });
                        component.initial();

                        var fieldContainer = $('<div class="form-group"></div>');
                        fieldContainer.append(component.label());
                        fieldContainer.append(component.tip());
                        fieldContainer.append(component.element());

                        $('div.card-body', settings.instance).append(fieldContainer);
                    }

                }
            },
        }

        coreMethod.event.initial();

        return coreMethod;
    };
}(jQuery));