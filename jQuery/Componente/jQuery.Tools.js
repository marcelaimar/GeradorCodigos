$.redirecionar = function (Pagina) {
    var Dominio = $.Configuracao.Caminho;

    /**
     * Verifica se clicou no botão voltar, caso não, pega a página por parâmetro
     */
    Pagina = (this.window) ? location.href : Pagina;

    if (Pagina.substr(0, 1) == "/") {
        Pagina = Pagina.substr(1, Pagina.length);
    }
    if (Pagina.substr(-1) == "/") {
        Pagina = Pagina.substr(0, Pagina.length - 1);
    }
    if ($.trim(Pagina) == "") {
        Pagina = "principal";
    }

    if (!window.history.pushState) {
        location.href = Pagina;
        return false;
    }

    var Caminho = (this.window) ? Pagina : ($.trim(Pagina.substr(0, Dominio.length)) == $.trim(Dominio) ? Pagina : Dominio + "/" + Pagina);

    if ($.trim(Caminho) == $.trim(Dominio)) {
        Caminho = Caminho + "/Principal";
        Pagina = "Principal";
    }

    //Pagina = Pagina.replace("-", "").ucwords();
    /**
     * Faz a requisição da página
     */
    $.ajax({
        url: Caminho,
        cache: false,
        type: 'POST',
        data: {
            TrocarPagina: 1
        },
        dataType: "json",
        contentType: 'application/x-www-form-urlencoded; charset=ISO-8859-1',
        beforeSend: function () {
            $.aoFecharPagina($.Configuracao.PaginaAtual.Pagina, $.Configuracao.PaginaAtual.Parametros);
        },
        success: function (DadosPagina) {
            if (DadosPagina.Redirecionar) {
                $.trocarPagina(DadosPagina.Redirecionar);
            } else {
                var Conteudo = $("#Conteudo").removeAttr("class").addClass(DadosPagina.Classe);

                Conteudo.html(Base64.decode(DadosPagina.Conteudo));

                $.aoAbrirPagina(DadosPagina.Classe, DadosPagina.Parametros);
            }
        }
    });

    /**
     * Altera a URL da página, caso não tenha clicado no botão voltar
     */
    if (!(this.window) && Caminho != window.location.href) {
        if (Pagina == "principal") {
            Caminho = Dominio;
        }
        if (window.history.pushState) {
            window.history.pushState({path: Pagina}, '', Caminho);
        }
    }
};
$.funcaoExiste = function (Funcao) {
    return (typeof window[Funcao] === "function");
};
$.getConexao = function () {
    return $("#Conexao>form").formToJSON();
};

$.formLoadDefault = function (form) {
    return {
        beforeSend: function () {
            $(form).addClass("Carregando");
        },
        complete: function () {
            $(form).removeClass("Carregando");
        }
    };
};

$.verificaConexao = function () {
    var Conteudo = $("#Conteudo.ConexaoObrigatoria");
    if ((!$.getConexao().BancoDados || !$.getConexao().Tabela)) {
        if (!$(">#InformaConexao", Conteudo).length) {
            Conteudo.prepend("<div id=\"InformaConexao\"><span>Banco de dados ou tabela não informados</span></div>")
        }    
    } else {
        $(">#InformaConexao", Conteudo).remove();
    }
};

$.aoAbrirPagina = function (Pagina, Parametros) {
    var ConteudoPagina = $("#Conteudo." + Pagina);

    //alert("abriu");

    $.Configuracao.PaginaAtual = {
        Pagina: Pagina,
        Parametros: Parametros
    };

    ConteudoPagina.find("select").select2({
        placeholder: "Selecione",
        allowClear: true,
        minimumResultsForSearch: 10,
        width: 'resolve'
    });

    $("#Menu>nav>ul>li").removeClass("Atual").find("a[href=" + (Pagina.toLocaleLowerCase()) + "]").parent("li").addClass("Atual");

//    console.log(Pagina);

    if (typeof $.aoAbrirPagina[Pagina] == "function") {
        $.aoAbrirPagina[Pagina].call(null, ConteudoPagina, Parametros);
        $.verificaConexao();
    }
    return {};
};
$.aoFecharPagina = function (Pagina, Parametros) {
    var PaginaAtual = $.Configuracao.PaginaAtual.Pagina;
    if (PaginaAtual && typeof $.aoFecharPagina[PaginaAtual] == "function") {
        $.aoFecharPagina[PaginaAtual].call(null, $("#Conteudo." + PaginaAtual), $.Configuracao.PaginaAtual.Parametros);
    }
    return {};
};


String.prototype.ucwords = function () {
    return this.toLowerCase().replace(/\b[a-z]/g, function (letter) {
        return letter.toUpperCase();
    });
};