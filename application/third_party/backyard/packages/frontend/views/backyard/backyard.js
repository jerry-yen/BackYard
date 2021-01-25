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
                    var templateName = 'logo';
                    var templateMetadataApiPath = '/index.php/api/template/user/' + settings.userType + '/code/' + templateName;
                    $.backyard({ 'userType': settings.userType }).process.template(templateMetadataApiPath, templateName);
                },

                /**
                 * 載入左側版面組件
                 */
                leftSide: function () {
                    var templateName = 'leftside';
                    var templateMetadataApiPath = '/index.php/api/template/user/' + settings.userType + '/code/' + templateName;
                    $.backyard({ 'userType': settings.userType }).process.template(templateMetadataApiPath, templateName);
                },

                /**
                 * 載入右側版面組件
                 */
                rightSide: function () {
                    var templateName = 'rightside';
                    var templateMetadataApiPath = '/index.php/api/template/user/' + settings.userType + '/code/' + templateName;
                    $.backyard({ 'userType': settings.userType }).process.template(templateMetadataApiPath, templateName);
                },

                /**
                 * 載入頁頭版面的組件
                 */
                header: function () {
                    var templateName = 'header';
                    var templateMetadataApiPath = '/index.php/api/template/user/' + settings.userType + '/code/' + templateName;
                    $.backyard({ 'userType': settings.userType }).process.template(templateMetadataApiPath, templateName);
                },

                /**
                 * 載入頁尾的組件
                 */
                footer: function () {
                    var templateName = 'footer';
                    var templateMetadataApiPath = '/index.php/api/template/user/' + settings.userType + '/code/' + templateName;
                    $.backyard({ 'userType': settings.userType }).process.template(templateMetadataApiPath, templateName);
                },

                /**
                 * 載入主要內容中的組件
                 * @param {*} code 頁面代碼
                 */
                content: function (code) {
                    var templateName = 'content';
                    var templateMetadataApiPath = '/index.php/api/content/user/' + settings.userType + '/code/' + code;
                    $.backyard({ 'userType': settings.userType }).process.template(templateMetadataApiPath, templateName);
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
                        {},
                        'GET',
                        function (response) {
                            $('div[widget="' + code + '"]').html(response.content);
                        },
                        null,
                        true
                    );
                }
            },

            /**
             * @var 後設資料
             */
            metadata: {
                dataset: function (code) {
                    var content = '';
                    $.backyard({ 'userType': settings.userType }).process.api(
                        '/index.php/api/dataset/user/' + settings.userType + '/code/' + code,
                        {},
                        'GET',
                        function (response) {
                            content = response;
                        },
                        null,
                        false
                    );
                    return content;
                },
                widget: function (code) {
                    var content = '';
                    $.backyard({ 'userType': settings.userType }).process.api(
                        '/index.php/api/widget/user/' + settings.userType + '/code/' + code,
                        {},
                        'GET',
                        function (response) {
                            content = response;
                        },
                        null,
                        false
                    );
                    return content;
                },
                defineWidget: function (code) {
                    var content = '';
                    $.backyard({ 'userType': settings.userType }).process.api(
                        '/index.php/api/definewidget/user/' + settings.userType + '/code/' + code,
                        {},
                        'GET',
                        function (response) {
                            content = response;
                        },
                        null,
                        false
                    );
                    return content;
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
                 * @param {*} data 傳送的參數
                 * @param {*} method HTTP方法(GET,POST)
                 * @param {*} success_feedback 自訂呼叫成功後的處理方法
                 * @param {*} error_feedback 自訂呼叫失敗後的處理方法
                 * @param {*} async 是否以非同步方式呼叫API
                 */
                api: function (url, data, method, success_feedback, error_feedback, async) {
                    method = (method == 'undefined' || method == null) ? 'GET' : method;
                    success_feedback = (success_feedback == 'undefined' || success_feedback == null) ? function () { } : success_feedback;
                    error_feedback = (error_feedback == 'undefined' || error_feedback == null) ? function (thrownError) { console.log(thrownError); } : error_feedback;
                    async = (async == 'undefined' || async == null) ? false : async;
                    $.ajax({
                        'url': url,
                        'async': async,
                        'data': data,
                        'dataType': 'json',
                        'type': method,
                        'success': success_feedback,
                        'error': error_feedback
                    });
                },
                /**
                 * 登入
                 */
                login: function (account, password, verifycode) {

                    var data = {};
                    // 帳號
                    data.account = account;
                    // 密碼
                    data.password = password;
                    // 驗證碼(不一定有，非必填)
                    data.verifycode = verifycode;
                    var count = Math.floor(Math.random() * 5) + 5;
                    var code = JSON.stringify(data);
                    for (var i = 0; i < count; i++) {
                        code = btoa(code);
                    }
                    code += count;
                    this.api('/index.php/api/login/user/' + settings.userType, {
                        'code': code
                    }, 'POST', function (response) {
                        console.log(response);
                        if (response.status == 'success') {
                            location.href = '/index.php/' + settings.userType + '/page';
                        }
                        else {
                            $.backyard().dialog.alert(
                                response.message,
                                '',
                                'warning',
                                '確定'
                            );
                        }
                    });
                },

                /**
                 * 載入元件的腳本
                 * 
                 * @param {*} url 腳本路徑
                 * @param {*} success_feedback 自訂呼叫成功後的處理方法
                 * @param {*} error_feedback 自訂呼叫失敗後的處理方法
                 * @param {*} async 是否以非同步方式載入腳本
                 */
                component: function (component_name, success_feedback, error_feedback, async) {
                    success_feedback = (success_feedback == 'undefined' || success_feedback == null) ? function () { } : success_feedback;
                    error_feedback = (error_feedback == 'undefined' || error_feedback == null) ? function (thrownError) { console.log(thrownError); } : error_feedback;
                    async = (async == 'undefined' || async == null) ? false : async;

                    $.ajax({
                        'url': '/index.php/api/component/user/' + settings.userType + '/code/' + component_name,
                        'async': async,
                        'dataType': 'script',
                        'type': 'GET',
                        'success': success_feedback,
                        'error': error_feedback
                    });
                },

                /**
                 * 載入所有元件所需的腳本
                 * 
                 * @param {*} code 資料集代碼
                 * @param {*} success_feedback 自訂呼叫成功後的處理方法
                 * @param {*} error_feedback 自訂呼叫失敗後的處理方法
                 * @param {*} async 是否以非同步方式載入腳本
                 */
                script: function (code, success_feedback, error_feedback, async) {
                    success_feedback = (success_feedback == 'undefined' || success_feedback == null) ? function () { } : success_feedback;
                    error_feedback = (error_feedback == 'undefined' || error_feedback == null) ? function (thrownError) { console.log(thrownError); } : error_feedback;
                    async = (async == 'undefined' || async == null) ? false : async;

                    $.ajax({
                        'url': '/index.php/api/script/user/' + settings.userType + '/code/' + code,
                        'async': async,
                        'dataType': 'script',
                        'type': 'GET',
                        'success': success_feedback,
                        'error': error_feedback
                    });
                },

                /**
                 * 載入所有元件所需的樣式檔
                 * 
                 * @param {*} code 資料集代碼
                 * @param {*} success_feedback 自訂呼叫成功後的處理方法
                 * @param {*} error_feedback 自訂呼叫失敗後的處理方法
                 * @param {*} async 是否以非同步方式載入腳本
                 */
                css: function (code, success_feedback, error_feedback, async) {
                    success_feedback = (success_feedback == 'undefined' || success_feedback == null) ? function () { } : success_feedback;
                    error_feedback = (error_feedback == 'undefined' || error_feedback == null) ? function (thrownError) { console.log(thrownError); } : error_feedback;
                    async = (async == 'undefined' || async == null) ? false : async;

                    var fileref = document.createElement("link");
                    fileref.setAttribute("rel", "stylesheet");
                    fileref.setAttribute("type", "text/css");
                    fileref.setAttribute("href", '/index.php/api/css/user/' + settings.userType + '/code/' + code);
                    if (typeof fileref != "undefined") {
                        document.getElementsByTagName("head")[0].appendChild(fileref);
                    }
                },

                /**
                 * 載入版面中的組件
                 * 
                 * @param {*} templateMetadataApiPath 版面後設資料的API呼叫路徑
                 * @param {*} templateName 版面名稱
                 */
                template: function (templateMetadataApiPath, templateName) {
                    $.backyard().process.api(
                        templateMetadataApiPath,
                        {},
                        'GET',
                        function (response) {
                            var widgets = response.widgets;
                            var content = '';
                            for (var key in widgets) {
                                content += '<div widget="' + widgets[key].code + '" class="col-' + widgets[key].mobile + ' col-sm-' + widgets[key].mobile + ' col-md-' + widgets[key].pad + ' col-lg-' + widgets[key].desktop + ' col-xl-' + widgets[key].desktop + '"></div>';
                            }
                            $(settings.template[templateName]).html(content);
                            for (var key in widgets) {
                                $.backyard({ 'userType': settings.userType }).html.widget(widgets[key].code);
                            }
                        },
                        null,
                        true
                    );
                }
            },
            dialog: {
                /**
                 * 訊息
                 */
                alert: function (title, message, iconType, buttonText) {
                    Swal.fire({
                        'title': title,
                        'html': message,
                        'icon': iconType,
                        'confirmButtonText': buttonText,
                    });
                },

                /**
                 * 確認訊息
                 */
                confirm: function (title, message, iconType, feedback, confirmText, cancelText) {
                    confirmText = (confirmText == undefined) ? '確定' : confirmText;
                    cancelText = (cancelText == undefined) ? '取消' : cancelText;
                    Swal.fire({
                        'title': title,
                        'html': message,
                        'icon': iconType,
                        'showCloseButton': true,
                        'showCancelButton': true,
                        'focusCancel': true,
                        'confirmButtonText': confirmText,
                        'cancelButtonText': cancelText
                    }).then((response) => {
                        if (feedback != undefined) {
                            feedback(response);
                        }
                    });
                }
            }

        }


        return coreMethod;
    };
}(jQuery));