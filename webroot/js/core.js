(function ($) {
    "use strict";
    var Form = {};
    window.Form = Form;

    Form.beforeSubmit = function (formData, $form, options) {
        $('input,textarea,select,button', $form).attr('disabled', true);
        $('.form-errors', $form).remove();
    };

    Form.success = function (data, statusText, xhr, $form) {
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
    };

    Form.callback = {
        login:{
            success:function (data, statusText, xhr, $form) {
                if (data.success) {
                    document.location.reload();
                } else {
                    $('.login-error', $form).fadeIn();
                    Form.success(data, statusText, xhr, $form);
                }
            },
            beforeSubmit:function (formData, $form, options) {
                $('.login-error', $form).hide();
                Form.beforeSubmit(formData, $form, options);
            }
        },
        register:{
            success:function (data, statusText, xhr, $form) {
                if (data.success) {
                    document.location.reload();
                } else {
                    Form.success(data, statusText, xhr, $form);
                }
            },
            beforeSubmit:function (formData, $form, options) {
                Form.beforeSubmit(formData, $form, options);
            }
        },
        userPicture:{
            success:function (data, statusText, xhr, $form) {
            },
            beforeSubmit:function (formData, $form, options) {
            }
        }
    };

    $('form[data-ajax]').live('submit', function (e) {
        var ajaxFormOptions = {};
        if (Form.callback[$(this).data('ajax')].success) {
            ajaxFormOptions.success = Form.callback[$(this).data('ajax')].success;
        }
        if (App.Form.callback[$(this).data('ajax')].beforeSubmit) {
            ajaxFormOptions.beforeSubmit = Form.callback[$(this).data('ajax')].beforeSubmit;
        }
        $(this).ajaxSubmit(ajaxFormOptions);
        return false;
    });

    App.Ajax = {
        success:function (data) {
            var $dialog, dialogOptions;
            dialogOptions = {
                draggable:false,
                hide:'fade',
                modal:true,
                resizable:false,
                /*
                 open: function (event, ui) {
                 $(".ui-dialog-titlebar-close").hide();
                 },
                 */
                show:'fade'
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



    $('#language').live('click', function () {
        $('#language-swap').toggle();
        return false;
    });

})(jQuery);