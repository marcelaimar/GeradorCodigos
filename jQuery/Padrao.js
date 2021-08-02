$(function () {
    $.Configuracao = JSON.parse((Base64.decode(Base64.decode($("body>input[type=hidden]#DadosAplicacao").val()))));
    $("body>input[type=hidden]#DadosAplicacao").remove();

    $.ajaxSetup({
        dataType: "json",
        global: false,
        type: "POST",
        error: function () {
        },
        beforeSend: function () {
        },
        complete: function () {
        }
    });

    $("select").select2({
        placeholder: "Selecione",
        allowClear: true,
        minimumResultsForSearch: 10,
        width: 'resolve'
    });

    $("#Conexao>form select[name=BancoDados]").change(function (e) {
        var BancoDados = $(this).val();
        if (BancoDados) {
            $.ajax($.extend({
                url: $.Configuracao.Caminho + '/BancoDados/Tabela/Listar',
                data: {
                    BancoDados: BancoDados
                },
                success: function (Tabelas) {
                    $("select[name=Tabela]").html("<option value=\"\"></option>");
                    $.each(Tabelas, function (i, Tabela) {
                        $("select[name=Tabela]").append("<option value=\"" + Tabela + "\">" + Tabela + "</option>");
                    });
                    $("select[name=Tabela]").trigger("change");
                }
            }, $.formLoadDefault(this)));
        } else {
            $("select[name=Tabela]").html("<option value=\"\"></option>");
        }

        $.verificaConexao();
        e.stopPropagation();
    });

    $("#Conexao>form select[name=Tabela]").change(function (e) {
        $.verificaConexao();
    });

    $("html").delegate("a[href]", "click.TrocarPagina", function (e) {
        var link = $.trim($(this).attr("href"));
        var dominio = $.Configuracao.Caminho;
        if (
                (
                        link.substr(0, 4).toLocaleLowerCase() != "http" &&
                        link.substr(0, 10).toLocaleLowerCase() != "javascript" &&
                        link.substr(0, 7).toLocaleLowerCase() != "mailto:"
                        )
                ||
                (
                        $.trim(link.substr(0, dominio.length)) == $.trim(dominio)
                        )
                ) {
            $.redirecionar($(this).attr('href'));
            e.preventDefault();
        }
    });

    $("#Menu>nav>ul>li").click(function () {
        $("#Menu>nav>ul>li.Atual").removeClass("Atual");
        $(this).addClass("Atual");
    });

    $(window).bind('popstate', $.redirecionar);
    $.aoAbrirPagina($.Configuracao.Pagina, $.Configuracao.Parametros);
});
$.Configuracao = {};    