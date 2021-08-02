/* 
    Arquivo javascript: Utilitarios

    Criado em : 03/10/2014, 11:26:30
    Autor     : MarceL AimaR (marcel_aimar@hotmail.com)    
    Arquivo   : Utilitarios.js 
    Encoding  : ISO-8859-1   
*/

/**
 * Função que será chamada ao abrir a página "Principal"
 * @param object Pagina
 * @param json Parametros
 * @returns json
 */
$.aoAbrirPagina.Utilitarios = function (Pagina, Parametros) {
    Pagina.addClass("ConexaoObrigatoria");
    
    $("form", Pagina).submit(function (e) {
        $.ajax($.extend({
            url: $.Configuracao.Caminho + '/Utilitarios/Gerar',
            data: $.extend($.getConexao(), $(this).formToJSON()),
            success: function (Retorno) {
                Pagina.find("textarea").val(Retorno.Conteudo);
            }
        }, $.formLoadDefault(this)));

        e.preventDefault();
        return false;
    });
    return {};
    return {};
};


/**
 * Função que será chamada ao fechar a página "Principal"
 * @param object Pagina
 * @param json Parametros
 * @returns json
 */
$.aoFecharPagina.Utilitarios = function (Pagina, Parametros) {
    //Conteúdo
    return {};
};
