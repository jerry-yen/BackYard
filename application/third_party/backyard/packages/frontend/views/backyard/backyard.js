(function ($) {

    /**
     * 後花園前端主程式
     * 
     * @param {*} _settings 設定值
     */
    $.backyard = function (_settings) {
        var settings = $.extend({
            'userType': 'admin',
            'template': {
                'content': 'section.content > div.container-fluid > div.row',
                'logo': 'aside div.logo',
                'leftside': 'aside div.leftside',
                'rightside': 'aside.control-sidebar',
                'footer': 'footer.main-footer',
                'header': 'ul.navbar-nav'
            }
        }, _settings);

        // 自定義函式
        var coreMethod = {
            /**
             * @var 後設資料物件
             */
            template: {
                /**
                 * 載入LOGO版面組件
                 */
                logo: function () {
                    $.backyard().process.api(
                        '/index.php/api/template/user/' + settings.userType + '/code/logo',
                        'GET',
                        function (response) {
                            var widgets = response.widgets;
                            var content = '';
                            for (var key in widgets) {
                                content += $.backyard({ 'userType': settings.userType }).html.widget(widgets[key].code);
                            }
                            $(settings.template.logo).html(content);
                        }
                    );
                },

                /**
                 * 載入左側版面組件
                 */
                leftSide: function () {
                    $.backyard().process.api(
                        '/index.php/api/template/user/' + settings.userType + '/code/leftside',
                        'GET',
                        function (response) {
                            var widgets = response.widgets;
                            var content = '';
                            for (var key in widgets) {
                                content += $.backyard({ 'userType': settings.userType }).html.widget(widgets[key].code);
                            }
                            $(settings.template.leftside).html(content);
                        }
                    );
                },

                /**
                 * 載入右側版面組件
                 */
                rightSide: function () {
                    $.backyard().process.api(
                        '/index.php/api/template/user/' + settings.userType + '/code/rightside',
                        'GET',
                        function (response) {
                            var widgets = response.widgets;
                            var content = '';
                            for (var key in widgets) {
                                content += $.backyard({ 'userType': settings.userType }).html.widget(widgets[key].code);
                            }
                            $(settings.template.rightside).html(content);
                        }
                    );
                },

                /**
                 * 載入頁頭版面的組件
                 */
                header: function () {
                    $.backyard().process.api(
                        '/index.php/api/template/user/' + settings.userType + '/code/header',
                        'GET',
                        function (response) {
                            var widgets = response.widgets;
                            var content = '';
                            for (var key in widgets) {
                                content += $.backyard({ 'userType': settings.userType }).html.widget(widgets[key].code);
                            }
                            $(settings.template.header).html(content);
                        }
                    );
                },

                /**
                 * 載入頁尾的組件
                 */
                footer: function () {
                    $.backyard().process.api(
                        '/index.php/api/template/user/' + settings.userType + '/code/footer',
                        'GET',
                        function (response) {
                            var widgets = response.widgets;
                            var content = '';
                            for (var key in widgets) {
                                content += $.backyard({ 'userType': settings.userType }).html.widget(widgets[key].code);
                            }
                            $(settings.template.footer).html(content);
                        }
                    );
                },

                /**
                 * 載入主要內容中的組件
                 * @param {*} code 頁面代碼
                 */
                content: function (code) {
                    $.backyard().process.api(
                        '/index.php/api/content/user/' + settings.userType + '/code/' + code,
                        'GET',
                        function (response) {
                            var widgets = response.widgets;
                            var content = '';
                            for (var key in widgets) {
                                content += $.backyard({ 'userType': settings.userType }).html.widget(widgets[key].code);
                            }
                            $(settings.template.content).html(content);
                        }
                    );
                }
            },

            metadata: {
                /**
                 * 取得組件後設資料
                 * @param {*} code 代碼
                 */
                widget: function (code) {
                    var content = '';
                    $.backyard({ 'userType': settings.userType }).process.api(
                        '/index.php/api/widget/user/' + settings.userType + '/code/' + code,
                        'GET',
                        function (response) {
                            content = response.content;
                        },
                        null,
                        false
                    );

                    return content
                }
            },
            /**
             * @var 內容物件
             */
            html: {

                /**
                 * 取得組件HTML內容
                 * @param {*} code 代碼
                 */
                widget: function (code) {
                    var content = '';
                    $.backyard({ 'userType': settings.userType }).process.api(
                        '/index.php/api/widgethtml/user/' + settings.userType + '/code/' + code,
                        'GET',
                        function (response) {
                            response = JSON.parse(response);
                            content = response.content;
                        },
                        null,
                        false
                    );

                    return content
                }
            },

            /**
             * @var 資料物件
             */
            data: {
                load: function () {

                }
            },
            /**
             * @var 處理物件
             */
            process: {

                /**
                 * 呼叫API
                 * 
                 * @param {*} url API路徑
                 * @param {*} method HTTP方法(GET,POST)
                 * @param {*} success_feedback 自訂呼叫成功後的處理方法
                 * @param {*} error_feedback 自訂呼叫失敗後的處理方法
                 * @param {*} async 是否以非同步方式呼叫API
                 */
                api: function (url, method, success_feedback, error_feedback, async) {

                    method = (method == 'undefined' || method == null) ? 'GET' : method;
                    success_feedback = (success_feedback == 'undefined' || success_feedback == null) ? function () { } : success_feedback;
                    error_feedback = (error_feedback == 'undefined' || error_feedback == null) ? function (thrownError) { console.log(thrownError); } : error_feedback;
                    async = (async == 'undefined' || async == null) ? false : async;
                    $.ajax({
                        'url': url,
                        'async': async,
                        'dataType': 'json',
                        'type': method,
                        'success': success_feedback,
                        'error': error_feedback
                    });
                }
            }

        }


        return coreMethod;
    };
}(jQuery));