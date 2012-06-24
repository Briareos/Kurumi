var App = App || { settings: {}, behaviors: {}, locale: {} };

jQuery.noConflict();

(function ($) {
    'use strict';

    App.attachBehaviors = function (context, settings) {
        context = context || document;
        settings = settings || App.settings;
        // Execute all of them.
        $.each(App.behaviors, function () {
            if ($.isFunction(this.attach)) {
                this.attach(context, settings);
            }
        });
    };

    App.detachBehaviors = function (context, settings, trigger) {
        context = context || document;
        settings = settings || App.settings;
        trigger = trigger || 'unload';
        // Execute all of them.
        $.each(App.behaviors, function () {
            if ($.isFunction(this.detach)) {
                this.detach(context, settings, trigger);
            }
        });
    };

    App.checkPlain = function (str) {
        var character, regex,
            replace = { '&': '&amp;', '"': '&quot;', '<': '&lt;', '>': '&gt;' };
        str = String(str);
        for (character in replace) {
            if (replace.hasOwnProperty(character)) {
                regex = new RegExp(character, 'g');
                str = str.replace(regex, replace[character]);
            }
        }
        return str;
    };

    App.freezeHeight = function (element) {
        App.unfreezeHeight(element);
        $('<div id="freeze-height"></div>').css({
            position: 'absolute',
            top: '0px',
            left: '0px',
            width: '1px',
            height: $(element).css('height')
        }).appendTo(element);
    };

    App.unfreezeHeight = function (element) {
        $('> #freeze-height', element).remove();
    };

    App.Library = {
        load: function (libraryName, callback, async) {
            var library;
            if (typeof async === undefined) {
                async = true;
            }
            if (App.Library.loaded.indexOf(libraryName) === -1) {
                if (typeof App.Library.registered[libraryName] === undefined) {
                    throw "Library " + libraryName + " is not defined";
                }
                App.Library.loaded.push(libraryName);
                $.ajax({
                    url: App.Library.registered[libraryName].js,
                    dataType: 'script',
                    cache: true,
                    success: callback
                });
            } else {
                callback();
            }
        },
        loaded: [],
        register: function (name, files) {
            App.Library.registered[name] = files;
        },
        registered: {}
    };

    App.Form = {
        beforeSubmit: function (formData, $form, options) {
            $('input,textarea,select,button', $form).attr('disabled', true);
            $('.form-errors', $form).remove();
        },
        success: function (data, statusText, xhr, $form) {
            var $errors, $element;

            if (typeof data.form !== undefined) {
                $form.replaceWith(data.form.body);
                // Gotta reload that DOM.
                $form = $('#' + data.form.id);
            }

            $errors = $('.form-errors[data-for]', $form);

            /* Focus the first element with an error */
            if ($errors.length) {
                $element = $('#' + $errors.first().data('for'));
                if ($element.length && $.inArray($element.prop('tagName').toLowerCase(), ['input', 'textarea', 'select']) === -1) {
                    $element = $('input,textarea,select', $element).first();
                } else if (!$element.length) {
                    $element = $('[id^=' + $errors.first().data('for') + ']:visible');
                }
                $element.focus();
            }

        },
        callback: {
            login: {
                success: function (data, statusText, xhr, $form) {
                    if (data.success) {
                        document.location.reload();
                    } else {
                        $('.login-error', $form).fadeIn();
                        App.Form.success(data, statusText, xhr, $form);
                    }
                },
                beforeSubmit: function (formData, $form, options) {
                    $('.login-error', $form).hide();
                    App.Form.beforeSubmit(formData, $form, options);
                }
            },
            register: {
                success: function (data, statusText, xhr, $form) {
                    if (data.success) {
                        document.location.reload();
                    } else {
                        App.Form.success(data, statusText, xhr, $form);
                    }
                },
                beforeSubmit: function (formData, $form, options) {
                    App.Form.beforeSubmit(formData, $form, options);
                }
            },
            userPicture: {
                success: function (data, statusText, xhr, $form) {

                },
                beforeSubmit: function (formData, $form, options) {

                }
            }
        }
    };

    $('form[data-ajax]').live('submit', function (e) {
        var ajaxFormOptions = {};
        if (App.Form.callback[$(this).data('ajax')].success) {
            ajaxFormOptions.success = App.Form.callback[$(this).data('ajax')].success;
        }
        if (App.Form.callback[$(this).data('ajax')].beforeSubmit) {
            ajaxFormOptions.beforeSubmit = App.Form.callback[$(this).data('ajax')].beforeSubmit;
        }
        $(this).ajaxSubmit(ajaxFormOptions);
        return false;
    });

    App.Ajax = {
        success: function (data) {
            var $dialog, dialogOptions;
            dialogOptions = {
                draggable: false,
                hide: 'fade',
                modal: true,
                resizable: false,
                /*
                 open: function (event, ui) {
                 $(".ui-dialog-titlebar-close").hide();
                 },
                 */
                show: 'fade'
            };
            if (typeof data.dialog !== undefined) {
                $dialog = $(data.dialog.body);
                $.extend(dialogOptions, $dialog.data(), data.dialog.options);
                $dialog.dialog(dialogOptions);
            }
            if (typeof data.form !== undefined) {

            }
        }
    };

    $('a[data-ajax]').live('click', function (e) {
        var ajaxOptions = {};
        $.ajax({
            url: $(this).attr('href'),
            success: App.Ajax.success
        });
        return false;
    });

    $('#language').live('click', function () {
        $('#language-swap').toggle();
        return false;
    });

})(jQuery);