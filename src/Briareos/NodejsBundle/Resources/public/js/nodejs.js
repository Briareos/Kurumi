'use strict';
var Nodejs = {
    contentChannelNotificationCallbacks:{},
    presenceCallbacks:{},
    callbacks:{},
    socket:false,
    connectionSetupHandlers:{}
};

Nodejs.runCallbacks = function (message) {
    // It's possible that this message originated from an ajax request from the
    // client associated with this socket.
    if (message.clientSocketId === Nodejs.socket.socket.sessionid) {
        window.console.log('this message originated from an ajax request from the client associated with this socket');
        return;
    }

    if (message.callback && Nodejs.callbacks[message.callback] && $.isFunction(Nodejs.callbacks[message.callback].callback)) {
        try {
            Nodejs.callbacks[message.callback].callback(message);
        }
        catch (exception) {
        }
    }
    else if (typeof message.presenceNotification !== 'undefined') {
        $.each(Nodejs.presenceCallbacks, function () {
            if ($.isFunction(this.callback)) {
                try {
                    this.callback(message);
                }
                catch (exception) {
                }
            }
        });
    }
    else if (message.contentChannelNotification != undefined) {
        $.each(Nodejs.contentChannelNotificationCallbacks, function () {
            if ($.isFunction(this.callback)) {
                try {
                    this.callback(message);
                }
                catch (exception) {
                }
            }
        });
    }
    else {
        $.each(Nodejs.callbacks, function () {
            if ($.isFunction(this.callback)) {
                try {
                    this.callback(message);
                }
                catch (exception) {
                    throw exception;
                }
            }
        });
    }
};

Nodejs.runSetupHandlers = function (type) {
    $.each(Nodejs.connectionSetupHandlers, function () {
        if ($.isFunction(this[type])) {
            try {
                this[type]();
            }
            catch (exception) {
                throw exception;
            }
        }
    });
};

Nodejs.connect = function () {
    var scheme = App.settings.nodejs.secure ? 'https' : 'http',
        url = scheme + '://' + App.settings.nodejs.host + ':' + App.settings.nodejs.port;
    App.settings.nodejs.connectTimeout = App.settings.nodejs.connectTimeout || 5000;
    if (typeof window.io === 'undefined') {
        return false;
    }
    Nodejs.socket = window.io.connect(url, {'connect timeout':App.settings.nodejs.connectTimeout});
    Nodejs.socket.on('connect', function () {
        Nodejs.sendAuthMessage();
        Nodejs.runSetupHandlers('connect');
        Nodejs.socket.on('message', Nodejs.runCallbacks);

        //options.data['nodejs_client_socket_id'] = Nodejs.socket.socket.sessionid;
    });
    Nodejs.socket.on('disconnect', function () {
        Nodejs.runSetupHandlers('disconnect');
    });
    window.setTimeout(Nodejs.checkConnection, App.settings.nodejs.connectTimeout + 250);
};

Nodejs.checkConnection = function () {
    if (!Nodejs.socket.socket.connected) {
        Nodejs.runSetupHandlers('connectionFailure');
    }
};

Nodejs.sendAuthMessage = function () {
    var authMessage = {
        authToken:App.settings.nodejs.authToken,
        contentTokens:App.settings.nodejs.contentTokens
    };
    Nodejs.socket.emit('authenticate', authMessage);
};

if (!Nodejs.socket) {
    if (Nodejs.connect()) {
        Nodejs.sendAuthMessage();
    }
}
