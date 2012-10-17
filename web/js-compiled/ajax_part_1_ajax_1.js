$(document).ready(function () {
    "use strict";

    var $context = $('body');
    var undefined;

    $context.ajaxSend(function (event, xhr, settings) {
    });

    $context.ajaxSuccess(function (event, xhr, settings, data) {
        if (data.commands !== undefined) {
            for (var i = 0; i < data.commands.length; i++) {
                var command = data.commands[i];
                Ajax.command[command.name](command['arguments']);
            }
        }
    });

    $context.ajaxError(function (event, xhr, settings, error) {
        if (xhr.readyState === 0) {
            // Browser was refreshed during an ajax request.
            return;
        }
        var $modal = $('<div class="modal fade hide"></div>');
        var $modalHeader = $('<div class="modal-header"></div>')
            .append('<button type="button" class="close" data-dismiss="modal">×</button>')
            .append($('<h3></h3>').text(xhr.status + ' ' + error))
            .append($('<span></span>').text(settings.type + ' ' + settings.url))
            .appendTo($modal);
        var $modalBody = $('<div class="modal-body"></div>')
            .appendTo($modal);
        $modal.modal('show');

        var $iframe = $('<iframe/>');
        $iframe.appendTo($modalBody);
        setTimeout(function () {
            $iframe.contents().find('body').html(xhr.responseText);
        }, 1);
        setTimeout(function () {
            $iframe.css({
                width:$modalBody.width(),
                height:$modalBody.height(),
                border:'none'
            });
        }, 1);
    });

    $context.ajaxComplete(function (event, xhr, settings) {

    });

    $context.on('click', 'a[data-ajax]', function (event) {
        var $link = $(this);
        $.ajax({
            url:$link.attr('href'),
            context:$link
        });
        return false;
    });

    $context.on('submit', 'form[data-ajax]', function (event) {
        var $form = $(this);
        $form.ajaxSubmit({
            context:$form
        });
        return false;
    });

    var Ajax = {
        command:{},
        behavior:{}
    };

    Ajax.attachBehaviors = function ($context) {
        var i;
        var behaviors = Ajax.behavior;
        for (i in behaviors) {
            if (behaviors.hasOwnProperty(i) && typeof behaviors[i].attach === 'function') {
                behaviors[i].attach($context);
            }
        }
    };

    Ajax.detachBehaviors = function ($context) {
        var i;
        var behaviors = Ajax.behavior;
        for (i in behaviors) {
            if (behaviors.hasOwnProperty(i) && typeof behaviors[i].detach === 'function') {
                behaviors[i].detach($context);
            }
        }
    };

    Ajax.command.location = function (settings) {
        if (settings.url) {
            if (settings.replace) {
                window.document.location.replace(settings.url);
            } else {
                window.document.location.href = settings.url;
            }
        } else {
            window.document.location.reload();
        }
    };

    Ajax.command.form = function (settings) {
        var $form = $(settings.body);
        var formId = $form.attr('id');
        $('#' + formId, $context).replaceWith($form);
    };

    Ajax.command.modal = function (settings) {
        var $modal = $('div.modal:visible');
        if (!$modal.length) {
            $modal = $('<div class="modal fade hide"></div>');
        }
        $modal.html(settings.body);
        $modal.modal('show');
    };

    Ajax.command.page = function (settings) {
        window.document.title = settings.title;
        $context.find('div#body:first').html(settings.body);
    };

    Ajax.command.state = function (settings) {
        $.bbq.pushState(settings.state);
    };


});