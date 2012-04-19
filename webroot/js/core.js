var App = {
    library:{},
    loadedLibraries:[],
    settings:{}
};

(function ($) {

    App.loadLibrary = function (libraryName, callback, async) {
        if (typeof async == undefined) {
            async = true;
        }
        if (App.loadedLibraries.indexOf(libraryName) == -1) {
            App.loadedLibraries.push(libraryName);
            $.ajax({
                url:App.library[libraryName].js,
                dataType:'script',
                cache:true,
                success:callback
            });
        } else {
            callback();
        }
    };

    App.Form = {
        beforeSubmit:function ($form) {
            $('input,select,button', $form).attr('disabled', true);
            $('.form-errors', $form).remove();
        },
        success:function ($form) {
            var $errors = $('.form-errors[data-for]', $form);
            if($errors.length) {
                var $element = $('#'+$errors.first().data('for'));
                $element.focus();
            }
        },
        callback:{
            login:{
                success:function (data, statusText, xhr, $form) {
                    if (data.success) {
                        document.location.reload();
                    } else {
                        $('.login-error .error-text', $form).html(data.message);
                        $('.login-error', $form).fadeIn();
                        $('input,button', $form).attr('disabled', false);
                    }
                },
                beforeSubmit:function (formData, $form, options) {
                    $('.login-error', $form).hide();
                    $('input,button', $form).attr('disabled', true);
                }
            },
            register:{
                success:function (data, statusText, xhr, $form) {
                    var $form = $('#' + data.form.id);
                    $form.replaceWith(data.form.body);
                    // Gotta reload that DOM.
                    $form = $('#' + data.form.id);
                    App.Form.success($form);
                },
                beforeSubmit:function (formData, $form, options) {
                    App.Form.beforeSubmit($form);
                }
            }
        }
    };

    $('form[data-ajax]').live('submit', function () {
        var ajaxFormOptions = {};
        if (App.Form.callback[$(this).data('ajax')]['success']) {
            ajaxFormOptions.success = App.Form.callback[$(this).data('ajax')]['success'];
        }
        if (App.Form.callback[$(this).data('ajax')]['beforeSubmit']) {
            ajaxFormOptions.beforeSubmit = App.Form.callback[$(this).data('ajax')]['beforeSubmit'];
        }
        $(this).ajaxSubmit(ajaxFormOptions);
        return false;
    });

    $('#language').live('click', function () {
        $('#language-swap').toggle();
        return false;
    });

})(jQuery);