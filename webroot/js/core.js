$(function () {
    "use strict";
    var $context = $('body');
    $context.bind('settings.nodejs_authenticate', function (event, settings) {
        if (settings.nodejs_auth_token) {
            if (typeof window.DefaultNodejs === 'undefined') {
                var DefaultNodejs = new window.Nodejs(settings.nodejs_auth_token, window.NodejsSettings);
                window.DefaultNodejs = DefaultNodejs;
                if (typeof window.DefaultChat === 'undefined') {
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
            if (typeof window.DefaultChat !== 'undefined') {
                window.DefaultChat.unload();
                try {
                    delete window.DefaultChat;
                } catch (e) {
                    window.DefaultChat = undefined;
                }
            }
            if (typeof window.DefaultNodejs !== 'undefined') {
                window.DefaultNodejs.disconnect();
                try {
                    delete window.DefaultNodejs;
                } catch (e) {
                    window.DefaultNodejs = undefined;
                }
            }
        }
    });

});