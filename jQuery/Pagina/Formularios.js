/* 
    Arquivo javascript: Formularios

    Criado em : 03/10/2014, 10:06:49
    Autor     : MarceL AimaR (marcel_aimar@hotmail.com)    
    Arquivo   : Formularios.js 
    Encoding  : ISO-8859-1   
*/

/**
 * Fun��o que ser� chamada ao abrir a p�gina "Principal"
 * @param object Pagina
 * @param json Parametros
 * @returns json
 */
$.aoAbrirPagina.Formularios = function (Pagina, Parametros) {
    Pagina.addClass("ConexaoObrigatoria");
    
    $("form", Pagina).submit(function (e) {
        $.ajax($.extend({
            url: $.Configuracao.Caminho + '/Formulario/Gerar',
            data: $.extend($.getConexao(), $(this).formToJSON()),
            success: function (Retorno) {
                Pagina.find("textarea").val(Retorno.Formulario);
            }
        }, $.formLoadDefault(this)));

        e.preventDefault();
        return false;
    });
    return {};
};


/**
 * Fun��o que ser� chamada ao fechar a p�gina "Principal"
 * @param object Pagina
 * @param json Parametros
 * @returns json
 */
$.aoFecharPagina.Formularios = function (Pagina, Parametros) {
    //Conte�do
    return {};
};
