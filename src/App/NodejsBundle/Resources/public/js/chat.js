(function ($) {
    App.Nodejs.callbacks.chat = {
        callback:function (message) {
            if (!message.data) {
                return;
            }
            console.log(message.data);
            // Each request made by the chat sends a randomly generated tab ID
            // (tid), unique to every tab, which is broadcast back in the socket
            // message. If those tid's match, it means the request originated from
            // this tab. This is done so no duplicate operations occur.
            var origin = (message.data.tid == App.Chat.tid);
            // These commands reflect user's actions, such as open (window), close,
            // activate, etc.
            switch (message.data.command) {
                case 'pong':
                    App.Chat.pong = new Date().getTime() / 1000;
                    break;

                case 'message':
                    if (message.data.receiver.u == App.Chat.user.u) {
                        // Received message.
                        message.data.message.r = true;
                        // Is this a new window?
                        if ($.inArray(message.data.sender.u, App.Chat.data.v) == -1) {
                            // Is there an active window already or is this window already
                            // open?
                            if (!App.Chat.data.a && $
                                .inArray(message.data.sender.u, App.Chat.data.v) == -1) {
                                // Not among open windows and there are no open
                                // windows, open
                                // and activate this one, but don't focus it
                                App.Chat.openWindow(message.data.sender);
                                App.Chat.command.activateWindow(message.data.sender.u);
                            } else {
                                // New window, open it
                                App.Chat.openWindow(message.data.sender);
                            }
                        }
                        // If this chat window is scrolled to the bottom we should
                        // scroll it
                        // again after we append the message
                        var scroll = App.Chat.isScrolledToBottom(message.data.sender.u);
                        App.Chat.appendMessage(message.data.sender, message.data.message);
                        if (scroll) {
                            App.Chat.scrollToBottom(message.data.sender.u);
                        }
                        // The window that we appended messages to wasn't active,
                        // meaning
                        // that we should notify the user of new message(s)
                        if (App.Chat.data.a != message.data.sender.u) {
                            App.Chat.newMessages(message.data.sender.u);
                        }
                        // Finally, update our local cache
                        App.Chat.data.w[message.data.sender.u].m[message.data.message.i] = message.data.message;
                    } else {
                        // Sent message
                        message.data.message.r = false;
                        // Is this the tab that the user sent the message from?
                        if (!origin) {
                            // Message is sent from another tab, just append it in
                            // the chat
                            var scroll = App.Chat
                                .isScrolledToBottom(message.data.receiver.u);
                            App.Chat
                                .appendMessage(message.data.receiver, message.data.message);
                            if (scroll) {
                                App.Chat.scrollToBottom(message.data.receiver.u);
                            }
                        } else {
                            // Message is sent from this tab, and is already in the
                            // window,
                            // but without an ID, set it now
                            // Modules can also alter messages, such as converting
                            // YouTube
                            // links to videos, we should alter the message at this
                            // point
                            $('#chat *[data-chat=message][data-uid=' + message.data.receiver.u + '][data-cmid=0]:first')
                                .attr('data-cmid', message.data.message.i)
                                .html(message.data.message.b);
                        }
                        App.Chat.data.w[message.data.receiver.u].m[message.data.message.i] = message.data.message;
                    }
                    break;

                case 'close':
                    if (!origin) {
                        $('#chat *[data-chat=toggle][data-uid=' + message.data.uid + ']')
                            .parent().remove();
                        var index = $.inArray(message.data.uid, App.Chat.data.v);
                        if (index != -1) {
                            App.Chat.data.v.splice(index, index + 1);
                        }
                        if (App.Chat.data.a == message.data.uid) {
                            App.Chat.data.a = 0;
                        }
                        delete App.Chat.data.w[message.data.uid];
                    }
                    break;

                case 'activate':
                    if (!origin) {
                        $('#chat-windows .window-toggle').parent().removeClass('active')
                            .children('.panel').hide();
                        if (message.data.uid) {
                            var index = $.inArray(message.data.uid, App.Chat.data.v);
                            if (index == -1) {
                                App.Chat.openWindow(message.data.d);
                            }
                            $('#chat-windows .window-toggle[data-uid=' + message.data.uid + ']')
                                .parent().addClass('active').children('.panel').show()
                                .find('.message-text').focus();
                            App.Chat.scrollToBottom(message.data.uid);
                            App.Chat.noNewMessages(message.data.uid);
                        }
                        App.Chat.data.a = message.data.uid;
                    }
                    break;
            }
        }
    };

    App.Chat = {};

    App.Chat.newMessages = function (uid) {
        if (App.Chat.data.a != uid) {
            $('#chat-windows .window-toggle[data-uid=' + uid + ']').parent()
                .addClass('new-messages');
        }
    };

    App.Chat.noNewMessages = function (uid) {
        $('#chat-windows .window-toggle[data-uid=' + uid + ']').parent()
            .removeClass('new-messages');
    };

    App.Chat.escape = function (text) {
        return text.replace(/&/g, "&amp;").replace(/</g, "&lt;")
            .replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    };

    App.Chat.generateTid = function () {
        var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
        var tidLength = 12;
        var tid = '';
        for (var i = 0; i < tidLength; i++) {
            var rnum = Math.floor(Math.random() * chars.length);
            tid += chars.substring(rnum, rnum + 1);
        }
        return tid;
    };

    App.Chat.compileTpl = function (html, variables) {
        for (var i in variables) {
            html = html.replace(new RegExp('\\$\{' + i + '\}', 'gm'), variables[i]);
        }
        return html;
    };

    App.Chat.ping = function () {
        var now = parseInt(new Date().getTime() / 1000);
        if ((now - App.Chat.pong) >= 300) {
            $.post(App.settings.chat.pingUrl);
            // console.log("Pinging now "+new Date().toUTCString());
        } else {
            // console.log("Ping skipped "+new Date().toUTCString());
        }
    };

    App.Chat.load = function (data) {
        // Store global data object that should be preserved across pages
        App.Chat.tid = App.Chat.generateTid();
        App.Chat.data = data;
        App.Chat.data.a = parseInt(App.Chat.data.a);
        App.Chat.local = {};
        App.Chat.pong = 0;
        setInterval(App.Chat.ping, 303000);
        App.Chat.user = {
            u:data.u,
            n:data.n,
            p:data.p
        };
        // This variable stores our javascript templates
        var tpl = App.settings.chat.tpl;
        // First generate the status block
        var onlineUsersHtml = '';

        // Iterate through all online users
        var countUsers = 0;
        for (var i in data.o) {
            var onlineUser = data.o[i];
            // n:name, s:status, p:picture, u:uid
            var userData = {
                uid:onlineUser.u,
                name:onlineUser.n,
                status:onlineUser.s,
                picture:onlineUser.p
            };
            onlineUsersHtml += App.Chat.compileTpl(tpl.user, userData);
            countUsers++;
        }
        var onlineUsersData = {
            users:onlineUsersHtml,
            count:countUsers
        };
        onlineUsersHtml = App.Chat.compileTpl(tpl.status, onlineUsersData);
        $('#chat-status').html(onlineUsersHtml);

        // data.v is an array that stores all open window's uid's in user's
        // session
        // Iterate through all open chat windows
        for (var i in data.v) {
            var uid = data.v[i];
            // This is the chat window we're currently working with
            var chatWindow = data.w[uid];
            // This variable should store the last message displayed in this
            // chat
            // window
            App.Chat.local[uid] = {};
            App.Chat.local[uid].last = false;
            // User's chat partner in this window
            var partner = chatWindow.d;
            // Insert our chat window in DOM at this point
            var chatWindowHtml = App.Chat.generateWindow(partner);
            $('#chat-windows').prepend(chatWindowHtml);
            for (var j in chatWindow.m) {
                // Iterate through all messages in this window
                var message = chatWindow.m[j];
                App.Chat.appendMessage(partner, message);
            }
            if (chatWindow.e) {
                App.Chat.newMessages(uid);
            }
        }

        if (data.a) {
            $('#chat-windows .window-toggle[data-uid=' + data.a + ']').parent()
                .addClass('active').children('.panel').show().find('.message-text')
                .focus();
            // Scroll this chat window to bottom
            App.Chat.scrollToBottom(data.a);
        }
    };

    App.Chat.isScrolledToBottom = function (uid) {
        if (App.Chat.data.a != uid) {
            // This chat window is not open at all.
            return false;
        }
        var convo = $('#chat-windows *[data-chat=focus][data-uid=' + uid + ']');
        var scrolled = (convo.prop('scrollHeight') - convo.scrollTop()) == convo.height();
        return scrolled;
    };

    App.Chat.scrollToBottom = function (uid) {
        // Scroll this chat window to bottom
        var convo = $('#chat-windows .body[data-uid=' + uid + ']');
        convo.scrollTop(convo.prop('scrollHeight') - convo.height());
    };

    App.Chat.openWindow = function (partner) {
        var chatWindow = App.Chat.data.w[partner.u];
        console.log(chatWindow);
        if (!chatWindow) {
            App.Chat.local[partner.u] = {};
            App.Chat.local[partner.u].last = false;
            App.Chat.data.v.push(parseInt(partner.u));
            App.Chat.data.w[partner.u] = {
                d:partner,
                m:{}
            };
            var chatWindowHtml = App.Chat.generateWindow(partner);
            $('#chat-windows').prepend(chatWindowHtml);
        }
    };

    App.Chat.appendMessage = function (partner, message) {
        var messageTime = new Date();
        messageTime.setTime((parseInt(message.t)) * 1000);
        var monthNamesShort = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ];
        var messageData = {
            uid:message.r ? partner.u : App.Chat.user.u,
            cmid:message.i,
            name:message.r ? partner.n : App.Chat.user.n,
            picture:message.r ? partner.p : App.Chat.user.p,
            body:message.b,
            time:messageTime.getDate() + '. ' + monthNamesShort[messageTime
                .getMonth()] + ' \'' + String(messageTime.getFullYear()).substring(2, 4) + '. @ ' + messageTime
                .getHours() + ':' + messageTime.getMinutes()
        };
        if (!App.Chat.local[partner.u].last || App.Chat.local[partner.u].last.r != message.r) {
            // First message in this chat window or from a different sender,
            // load
            // new message container
            var messageContainer = App.Chat
                .compileTpl(App.settings.chat.tpl.messages, messageData);
            $('#chat *[data-chat=conversation][data-uid=' + partner.u + ']')
                .append(messageContainer);
        }
        var messageHtml = App.Chat
            .compileTpl(App.settings.chat.tpl.message, messageData);
        $('#chat *[data-chat=conversation][data-uid=' + partner.u + '] *[data-chat=messages]:last')
            .append(messageHtml);
        // Store this message as the last message in this window for later use
        // by
        // other chat functions
        App.Chat.local[partner.u].last = message;
    };

    App.Chat.generateWindow = function (user, messages) {
        messages = messages ? messages : '';
        var chatWindowData = {
            uid:user.u,
            label:user.n,
            picture:user.p,
            name:user.n,
            messages:messages
        };
        return App.Chat
            .compileTpl(App.settings.chat.tpl.window, chatWindowData);
    };

    App.Chat.command = {};

    App.Chat.command.sendMessage = function (uid, messageText) {
        var sendData = {
            uid:uid,
            message:messageText,
            tid:App.Chat.tid
        };
        var message = {
            i:0,
            t:new Date().getTime() / 1000,
            b:App.Chat.escape(messageText),
            r:false
        };
        var scroll = App.Chat.isScrolledToBottom(uid);
        App.Chat.appendMessage(App.Chat.data.w[uid].d, message);
        if (scroll) {
            App.Chat.scrollToBottom(uid);
        }
        $.post(App.settings.chat.sendUrl, sendData);
    };

    App.Chat.command.activateWindow = function (uid) {
        if (App.Chat.data.w[uid]) {
            if (App.Chat.data.a != uid) {
                $('#chat-windows .window-toggle[data-uid!=' + uid + ']').parent()
                    .removeClass('active').children('.panel').hide();
                $('#chat-windows .window-toggle[data-uid=' + uid + ']').parent()
                    .addClass('active').children('.panel').show();
                App.Chat.noNewMessages(uid);
                App.Chat.data.a = uid;
                App.Chat.scrollToBottom(uid);
                var sendData = {
                    uid:uid,
                    tid:App.Chat.tid
                };
                $.post(App.settings.chat.activateUrl, sendData);
            }
            $('#chat textarea[data-uid=' + uid + ']').focus();
        }
    };

    App.Chat.command.deactivateWindow = function () {
        if (App.Chat.data.a) {
            $('#chat-windows .window-toggle').parent().removeClass('active')
                .children('.panel').hide();
            App.Chat.data.a = 0;
            var sendData = {
                uid:0,
                tid:App.Chat.tid
            };
            $.post(App.settings.chat.activateUrl, sendData);
        }
    };

    App.Chat.command.closeWindow = function (uid) {
        if (App.Chat.data.a == uid) {
            App.Chat.data.a = 0;
        }
        var sendData = {
            uid:uid,
            tid:App.Chat.tid
        };
        $.post(App.settings.chat.closeUrl, sendData);
        delete App.Chat.local[uid];
        var index = $.inArray(uid, App.Chat.data.v);
        if (index != -1) {
            App.Chat.data.v.splice(index, index + 1);
        }
        delete App.Chat.data.w[uid];
        $('#chat-windows .window-toggle[data-uid=' + uid + ']').parent().remove();
    };

    $('#chat .window-toggle').live('click', function () {
        if ($(this).parent().hasClass('active')) {
            if (!$(this).parent('#chat-status').length) {
                // Chat window has closed
                App.Chat.command.deactivateWindow();
            } else {
                $(this).parent().removeClass('active').children('.panel').hide();
            }
        } else {
            if (!$(this).parent('#chat-status').length) {
                // Chat window has opened
                var uid = $(this).data('uid');
                App.Chat.command.activateWindow(uid);
            } else {
                $(this).parent().addClass('active').children('.panel').show();
            }
        }
        return false;
    });

    $('.chat-user').live('click', function () {
        var uid = $(this).data('uid');
        if ($.inArray(uid, App.Chat.data.v) == -1) {
            var partner = {
                u:uid,
                n:$(this).data('name'),
                p:$(this).data('picture')

            };
            App.Chat.openWindow(partner);
        }
        App.Chat.command.activateWindow(uid);
        return false;
    });

    $('#chat *[data-chat=minimize]').live('click', function (e) {
        if ($(e.srcElement).data('chat') == 'minimize') {
            App.Chat.command.deactivateWindow();
        }
    });

    $('#chat *[data-chat=close]').live('click', function () {
        var uid = $(this).data('uid');
        App.Chat.command.closeWindow(uid);
    });

    $('#chat *[data-chat=focus]').live('click', function () {
        App.Chat.command.activateWindow($(this).data('uid'));
    });

    $('#chat-windows .message-text').live('keyup', function () {
        var cloneId = 'clone-' + $(this).data('uid');
        var $clone = $('#' + cloneId);
        if (!$clone.length) {
            $clone = $('<div />').attr('id', cloneId).addClass('message-text').css({
                maxHeight:'none',
                position:'absolute',
                wordWrap:'break-word',
                height:'auto',
                display:'none'
            });
            $(this).parent().prepend($clone);
        }
        $clone.html($(this).val().replace(/&/g, '&amp;')
            .replace(/ {2}/g, ' &nbsp;').replace(/<|>/g, '&gt;')
            .replace(/\n/g, '<br />') + "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
        $(this).css('height', $clone.height());
    });

    $('#chat .message-input textarea').live('keydown', function (e) {
        if (e.keyCode == 13) {
            App.Chat.command.sendMessage($(this).data('uid'), $(this).val());
            $(this).val('');
            return false;
        }
    });

    if (App.settings.chat.enabled) {
        $(document).ready(function () {
            $('body').append(App.Chat.compileTpl(App.settings.chat.tpl.chat));
            $.getJSON(App.settings.chat.cacheUrl, null, App.Chat.load);
        });
    }
})(jQuery);
