/* 
 Arquivo javascript: Html
 
 Criado em : 18/09/2014, 11:10:10
 Autor     : MarceL AimaR (marcel_aimar@hotmail.com)    
 Arquivo   : Html.js 
 Encoding  : ISO-8859-1   
 */

/**
 * Função que será chamada ao abrir a página "Principal"
 * @param object Pagina
 * @param json Parametros
 * @returns json
 */
$.aoAbrirPagina.Html = function (Pagina, Parametros) {
    //Pagina.addClass("ConexaoObrigatoria");

    $("form", Pagina).submit(function (e) {
        var Dados = $(this).formToJSON();
        var HTML = ((Dados.Aspas == "Duplas") ? Dados.ConteudoHTML.replace(new RegExp('["]', 'gi'), '\\"') : Dados.ConteudoHTML).split("\n");
        var Retorno = "";
        var QuebraLinha = Dados.QuebraLinha ? "\\r" : "";
        var Aspas = (Dados.Aspas == "Duplas") ? '"' : "'";

        //console.log(Dados.Converter);

        if (Dados.Variavel != "" && !Dados.VariavelLinhas) {
            Retorno = Dados.Variavel + " = ";
        }

        for (var Contador = 0; Contador < HTML.length; Contador++) {
            if ($.trim(HTML[Contador]) != "") {
                if (Dados.Variavel == "") {
                    Retorno += Aspas + QuebraLinha + $.trim(HTML[Contador]) + Aspas + ((Contador == HTML.length - 1) ? "" : Dados.Converter) + "\n";
                } else if (Dados.VariavelLinhas) {
                    Retorno += Dados.Variavel + " " + (Contador ? Dados.Converter : "") + '= ' + Aspas + QuebraLinha + $.trim(HTML[Contador]) + Aspas + ';' + "\n";
                } else {
                    Retorno += Aspas + QuebraLinha + $.trim(HTML[Contador]) + Aspas + Dados.Converter + "\n";
                }
            }
        }

        Retorno = $.trim(Retorno);

        Retorno = Retorno.substring(0, Retorno.length - 1) + ";";

        Pagina.find("form textarea[name=ConteudoCodigo]").val(Retorno).focus().select();


        /* var Response = $.trim($(".Options input[name=Response]").val());
         // if(Response == ""){
         //  alert("Informe a veriavel para o retorno do código");
         // }else{
         var HTML = $.trim($(".Box textarea[name=Default]").val());
         var Type = $(this).attr("name");
         var Return = "";
         HTML = HTML.replace(new RegExp('["]', 'gi'), '\\"').split("\n");
         for (i = 0; i < HTML.length; i++) {
         if ($.trim(HTML[i]) != "") {
         if (Response == "") {
         Return += '"\\r' + $.trim(HTML[i]) + '"' + ((i == HTML.length - 1) ? "" : "+") + '' + "\n";
         } else {
         Return += Response + '\\r "' + $.trim(HTML[i]) + '";' + "\n";
         }
         
         
         }
         }
         $(".Box textarea[name=Result]").val(Return).focus().select();*/

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
$.aoFecharPagina.Html = function (Pagina, Parametros) {
    //Conteúdo
    return {};
};
