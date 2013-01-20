$(function () {
    "use strict";
    var $context = $('body');
    var undefined;

    $context.bind('settings.default_nodejs', function (event, settings) {
        window.NodejsSettings = settings;
    });

    $context.bind('settings.default_chat', function (event, settings) {
        window.ChatSettings = settings;
    });

    $context.bind('settings.nodejs_authenticate', function (event, settings) {
        if (settings.nodejs_auth_token) {
            if (window.DefaultNodejs === undefined) {
                var DefaultNodejs = new window.Nodejs(settings.nodejs_auth_token, window.NodejsSettings);
                window.DefaultNodejs = DefaultNodejs;
                if (window.DefaultChat === undefined) {
                    DefaultNodejs.connectionSetupHandlers.chat = {
                        connect:function () {
                            var DefaultChat = new window.Chat(window.DefaultNodejs, window.ChatSettings);
                            window.DefaultChat = DefaultChat;
                            DefaultChat.initialize();
                        }
                    };
                }
                DefaultNodejs.connect();
            }
        } else {
            if (window.DefaultChat !== undefined) {
                window.DefaultChat.unload();
                try {
                    delete window.DefaultChat;
                } catch (e) {
                    window.DefaultChat = undefined;
                }
            }
            if (window.DefaultNodejs !== undefined) {
                window.DefaultNodejs.disconnect();
                try {
                    delete window.DefaultNodejs;
                } catch (e) {
                    window.DefaultNodejs = undefined;
                }
            }
        }
    });

    $.fn.attach = function () {
        $('.fileupload', $(this)).fileupload()
    };

    $context.attach();

    $context.on('click', 'a[data-oauth]', function () {
        var $link = $(this);
        $link.ajaxLoader('start', 'slide');
        $link.ajaxLoader('bar', {
            width:'33%'
        });
        var OAuthWindow = window.open($link.attr('href'), 'OAuthWindow', 'width=1000,height=550');
        var watchClose = setInterval(function () {
            if (OAuthWindow.closed) {
                clearTimeout(watchClose);
                if (OAuthenticating) {
                    $link.ajaxLoader('bar', {
                        width:'100%'
                    });
                } else {
                    $link.ajaxLoader('stop');
                    $link.ajaxLoader('bar', {
                        width:'0%'
                    });
                }
            }
        }, 200);
        var OAuthenticating = false;
        window.OAuthCallback = function (data) {
            if (data.status === 'success') {
                OAuthenticating = true;
                $.ajax({
                    url:data.url,
                    complete:function () {
                        $link.ajaxLoader('stop');
                        OAuthenticating = false;
                    }
                });
            }
        };
        return false;
    });

});