(function ($) {
    App.Nodejs = App.Nodejs || {
        'contentChannelNotificationCallbacks': {},
        'presenceCallbacks': {},
        'callbacks': {},
        'socket': false,
        'connectionSetupHandlers': {}
    };

    Drupal.behaviors.nodejs = {
        attach: function (context, settings) {
            if (!App.Nodejs.socket) {
                if (App.Nodejs.connect()) {
                    App.Nodejs.sendAuthMessage();
                }
            }
        }
    };

    App.Nodejs.runCallbacks = function (message) {
        // It's possible that this message originated from an ajax request from the
        // client associated with this socket.
        if (message.clientSocketId == App.Nodejs.socket.socket.sessionid) {
            return;
        }

        if (message.callback && App.Nodejs.callbacks[message.callback] && $.isFunction(App.Nodejs.callbacks[message.callback].callback)) {
            try {
                App.Nodejs.callbacks[message.callback].callback(message);
            }
            catch (exception) {}
        }
        else if (message.presenceNotification != undefined) {
            $.each(App.Nodejs.presenceCallbacks, function () {
                if ($.isFunction(this.callback)) {
                    try {
                        this.callback(message);
                    }
                    catch (exception) {}
                }
            });
        }
        else if (message.contentChannelNotification != undefined) {
            $.each(App.Nodejs.contentChannelNotificationCallbacks, function () {
                if ($.isFunction(this.callback)) {
                    try {
                        this.callback(message);
                    }
                    catch (exception) {}
                }
            });
        }
        else {
            $.each(App.Nodejs.callbacks, function () {
                if ($.isFunction(this.callback)) {
                    try {
                        this.callback(message);
                    }
                    catch (exception) {}
                }
            });
        }
    };

    App.Nodejs.runSetupHandlers = function (type) {
        $.each(App.Nodejs.connectionSetupHandlers, function () {
            if ($.isFunction(this[type])) {
                try {
                    this[type]();
                }
                catch (exception) {}
            }
        });
    };

    App.Nodejs.connect = function () {
        var scheme = App.settings.nodejs.secure ? 'https' : 'http',
            url = scheme + '://' + App.settings.nodejs.host + ':' + App.settings.nodejs.port;
        App.settings.nodejs.connectTimeout = App.settings.nodejs.connectTimeout || 5000;
        if (typeof io === 'undefined') {
            return false;
        }
        App.Nodejs.socket = io.connect(url, {'connect timeout': App.settings.nodejs.connectTimeout});
        App.Nodejs.socket.on('connect', function() {
            App.Nodejs.sendAuthMessage();
            App.Nodejs.runSetupHandlers('connect');
            App.Nodejs.socket.on('message', App.Nodejs.runCallbacks);

            if (Drupal.ajax != undefined) {
                // Monkey-patch Drupal.ajax.prototype.beforeSerialize to auto-magically
                // send sessionId for AJAX requests so we can exclude the current browser
                // window from resulting notifications. We do this so that modules can hook
                // in to other modules ajax requests without having to patch them.
                App.Nodejs.originalBeforeSerialize = Drupal.ajax.prototype.beforeSerialize;
                Drupal.ajax.prototype.beforeSerialize = function(element_settings, options) {
                    options.data['nodejs_client_socket_id'] = App.Nodejs.socket.socket.sessionid;
                    return App.Nodejs.originalBeforeSerialize(element_settings, options);
                };
            }
        });
        App.Nodejs.socket.on('disconnect', function() {
            App.Nodejs.runSetupHandlers('disconnect');
            if (Drupal.ajax != undefined) {
                Drupal.ajax.prototype.beforeSerialize = App.Nodejs.originalBeforeSerialize;
            }
        });
        setTimeout("App.Nodejs.checkConnection()", App.settings.nodejs.connectTimeout + 250);
    };

    App.Nodejs.checkConnection = function () {
        if (!App.Nodejs.socket.socket.connected) {
            App.Nodejs.runSetupHandlers('connectionFailure');
        }
    };

    App.Nodejs.sendAuthMessage = function () {
        var authMessage = {
            authToken: App.settings.nodejs.authToken,
            contentTokens: App.settings.nodejs.contentTokens
        };
        App.Nodejs.socket.emit('authenticate', authMessage);
    };

})(jQuery);
