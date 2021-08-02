/* 
 Arquivo javascript: jQuery.Select2Auxiliary
 
 Criado em : 28/08/2014, 08:11:49
 Autor     : MarceL AimaR (marcel_aimar@hotmail.com)    
 Arquivo   : jQuery.Select2Auxiliary.js 
 Encoding  : ISO-8859-1   
 */


$.fn.old_val = $.fn.val;
$.fn.val = function() {
    var self = $(this);
    var returnData = this.old_val.apply(this, arguments);
    
    if (self.data().select2 && arguments[0]) {        
        $(self).trigger("change");    
    }

    return returnData;
};

/**
 * Select2 Brazilian Portuguese translation
 */
(function ($) {
    "use strict";

    $.fn.select2.locales['pt-BR'] = {
        formatNoMatches: function () { return "Nenhum resultado encontrado"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "Digite mais " + n + " caracter" + (n == 1? "" : "es"); },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "Apague " + n + " caracter" + (n == 1? "" : "es"); },
        formatSelectionTooBig: function (limit) { return "Só é possível selecionar " + limit + " elemento" + (limit == 1 ? "" : "s"); },
        formatLoadMore: function (pageNumber) { return "Carregando mais resultados?"; },
        formatSearching: function () { return "Buscando?"; }
    };

    $.extend($.fn.select2.defaults, $.fn.select2.locales['pt-BR']);
})(jQuery);