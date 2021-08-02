/* 
 Arquivo javascript: Classes
 
 Criado em : 18/09/2014, 11:10:10
 Autor     : MarceL AimaR (marcel_aimar@hotmail.com)    
 Arquivo   : Classes.js 
 Encoding  : ISO-8859-1   
 */

/**
 * Função que será chamada ao abrir a página "Principal"
 * @param object Pagina
 * @param json Parametros
 * @returns json
 */
$.aoAbrirPagina.Classes = function (Pagina, Parametros) {
    Pagina.addClass("ConexaoObrigatoria");
    
    $("form", Pagina).submit(function (e) {
        $.ajax($.extend({
            url: $.Configuracao.Caminho + '/Classe/Gerar',
            data: $.extend($.getConexao(), $(this).formToJSON()),
            success: function (Arquivo) {
                /* $("select[name=Tabela]").html("<option value=\"\"></option>");
                 $.each(Tabelas, function (i, Tabela) {
                 $("select[name=Tabela]").append("<option value=\"" + Tabela + "\">" + Tabela + "</option>");
                 });
                 $("select[name=Tabela]").trigger("change");*/

                var URL = $.Configuracao.Caminho + "/Download.php?p=" + Arquivo.Nome;
                
                location.href = URL;
                
//                console.log(URL);
//                $("#Download").ready(function () {
//                    window.setTimeout(function () {
//                        if ($("iframe").contents().find("div").html() === "0") {
//                            alert("Erro ao gerar classe");
//                        }
//                    }, 1000);
//                }).attr("src",+ URL);
            }
        }, $.formLoadDefault(this)));

        e.preventDefault();
        return false;
    });
    return {};
};


/**
 * Função que será chamada ao fechar a página "Principal"
 * @param object Pagina
 * @param json Parametros
 * @returns json
 */
$.aoFecharPagina.Classes = function (Pagina, Parametros) {
    //Conteúdo
    return {};
};
