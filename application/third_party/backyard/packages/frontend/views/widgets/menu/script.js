(function ($) {

    /**
     * 選單組件
     * 
     * @param {*} _settings 設定值
     */
    $.fn.backyard_menu = function (_settings) {
        var settings = $.extend({
            'userType': 'admin',
            'code': $(this).attr('widget'),
            'instance': this,
            'url_code': '',
            'add_button_selector': 'button.add',
        }, _settings);

        // 自定義函式
        var widget = {
            menu: {
                initial: function () {
                    var urlParts = location.href.split('/');
                    settings.url_code = urlParts[urlParts.length - 1].replace('#','');

                    // 取得組件後設資料
                    var response = $.backyard({ 'userType': settings.userType }).metadata.widget(settings.code);
                    if (response.status != 'success') {
                        return;
                    }
                    
                    for (var i in response.metadata.widget.menu) {
                        var item = $('li.nav-item.template', settings.instance).clone();
                        item.removeClass('template').removeClass('d-none');
                        item = widget.menu.option.subItem(response.metadata.widget.menu[i], item, 1);
                        $('ul.nav:first', settings.instance).append(item);
                    }

                    // 如該頁是選單所屬的頁面，則展開所有上層的選單

                    $('a.active').parents('ul').each(function(){
                        $(this).css({'display':'block'});
                        $(this).closest('li').addClass('menu-open');
                    });
                    $('[data-widget="treeview"]', settings.instance).Treeview('init');
                },

                loadData: function () {

                },

                listener: {

                },

                option: {
                    subItem: function (menu, item, level) {

                        if (menu.icon != '') {
                            $('i').addClass(menu.icon);
                        }
                        $('a:first', item).css({ 'padding-left': (level * 15) + 'px' });
                        if (menu.type == 'page') {
                            $('p', item).html(menu.title);
                            $('a:first', item).attr('href', '/index.php/' + settings.userType + '/page/' + menu.code);
                            if (menu.code == settings.url_code) {
                                $('a:first', item).addClass('active');
                            }
                            return item;
                        }
                        else {
                            $('p', item).html(menu.title + '<i class="right fas fa-angle-left"></i>');
                            item.append('<ul class="nav nav-treeview"></ul>');
                            for (var i in menu.subItems) {
                                var subItem = $('li.nav-item.template', settings.instance).clone();
                                subItem.removeClass('template').removeClass('d-none');
                                subItem = this.subItem(menu.subItems[i], subItem, level + 1);
                                $('ul.nav:first', item).append(subItem);
                            }
                            return item;

                        }
                    }
                }
            }
        }

        widget.menu.initial();
        return widget;
    };
}(jQuery));