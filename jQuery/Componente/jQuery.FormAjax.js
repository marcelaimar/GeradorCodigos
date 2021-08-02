(function($) {
    $.fn.formAjax = function(options) {
        var form = this;
        var standards;

        options = $.extend({
            data: {},
            url: null,
            language: "php",
            dataType: "json",
            resetSuccess: true,
            lockSend: true,
            type: "POST",
            global: false,
            success: function() {
            },
            error: function() {
            },
            alert: function() {
            },
            beforeSend: function() {
            },
            complete: function() {
            }
        }, options);

        options.url = (options.url) ? options.url : $(form).attr("action");
        options.type = (options.type) ? options.type : $(form).attr("method");

        $(form)
                .unbind("submit.FormAjax")
                .bind("submit.FormAjax", function(e) {
                    standards = $.formAjaxSettings;

                    e.preventDefault();

                    $.ajax({
                        url: options.url,
                        dataType: options.dataType,
                        global: options.global,
                        type: options.type.toUpperCase(),
                        data: $.extend($(this).formToJSON(), options.data),
                        cache: options.cache,
                        beforeSend: function() {
                            if (options.lockSend) {
                                $(form).addClass("Travado");
                            }

                            standards.beforeSend.call(form, this);
                            options.beforeSend.call(form, this);
                        },
                        complete: function() {
                            if (options.lockSend) {
                                $(form).removeClass("Travado");
                            }

                            standards.complete.call(form, this);
                            options.complete.call(form, this);
                        },
                        success: function(message) {
                            if (message.Sucesso && message.Sucesso) {
                                if (options.resetSuccess) {
                                    $(form).resetForm();
                                }

                                standards.success.call(form, message.Sucesso);
                                options.success.call(form, message.Sucesso);
                            } else if (message.Alerta) {
                                standards.alert.call(form, message.Alerta, message.Campo);
                                options.alert.call(form, message.Alerta, message.Campo);
                            } else if (message.Erro) {
                                standards.error.call(form, message.Erro);
                                options.error.call(form, message.Erro);
                            }
                        },
                        error: function(error) {
                            options.error.call(form, error);
                            standards.error.call(form, error);
                        }
                    });

                    return false;
                });
    };

    $.formAjaxSetup = function(options) {
        $.formAjaxSettings = $.extend($.formAjaxSettings, options);
    };

    $.fn.resetForm = function() {
        $(this)[0].reset();
        $(this).find("select[data-placeholder]").trigger("chosen:updated");
    };

    $.fn.formToJSON = function() {
        var ArrayForm = {}, name;
        $("input,select,textarea", $(this)).each(function() {
            name = $(this).attr("id");
            name = (name === undefined) ? $.trim($(this).attr("name")) : $.trim(name);

            if (!($(this).attr("type") == "radio" && !$(this).is(":checked"))) {
                if ($.trim(name) !== "") {
                    ArrayForm[name] = $(this).val();
                }
                if (($(this).attr("type") == "radio" || $(this).attr("type") == "checkbox")) {
                    if ($(this).val() == "" || $(this).val() == "on") {
                        ArrayForm[name] = ($(this).is(":checked")) ? 1 : 0;
                    }
                }
            }
        });
        return ArrayForm;
    };

    $.formAjaxSettings = {
        success: function() {
        },
        error: function() {
        },
        alert: function() {
        },
        beforeSend: function() {
        },
        complete: function() {
        }
    };

})(jQuery);