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

    $context.on('click', 'a[data-oauth]', function (event) {
        var $link = $(this);
        var OAuthWindow;
        if ($link.data('oauth_window')) {
            OAuthWindow = $link.data('oauth_window');
            OAuthWindow.focus();
        } else {
            OAuthWindow = window.open($link.attr('href'), 'OAuthWindow', 'width=1000,height=550');
            $link.data('oauth_window', OAuthWindow);
        }
        var watchClose = setInterval(function () {
            if (OAuthWindow.closed) {
                clearTimeout(watchClose);
                $link.data('oauth_window', false);
            }
        }, 200);
        window.OAuthCallback = function (url) {
            $.ajax({
                context:$link,
                url:url
            });
        };
        return false;
    });
});