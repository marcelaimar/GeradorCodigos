<?php

//<editor-fold  defaultstate="collapsed" desc="Funções padrões do sistema">

/**
 * Verifica se a requisição é via ajax
 * @return type
 */
function isAjax() {
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}

/**
 * Verifica se página requisitada foi é via ajax
 * @return type
 */
function isPaginaAjax() {
    return (isAjax() && (isset($_POST["TrocarPagina"]) && intval($_POST["TrocarPagina"]) == 1));
}

/**
 * Função para incluir a estrutura da página caso não seja uma requisição ajax
 * @param type $Local
 */
function incluirEstrutura($Local) {
    if (!isAjax() && file_exists("Conteudo/PaginaPrincipal.php")) {
        ob_start();
        include("Conteudo/PaginaPrincipal.php");
        $Pagina = ob_get_contents();
        $Conteudo = array();
        ob_end_clean();

        $PosicaoConteudo = strpos($Pagina, "id=\"Conteudo\"");
        $PosicaoTag = strpos(substr($Pagina, $PosicaoConteudo), ">");

        $Conteudo["Rodape"] = substr($Pagina, $PosicaoTag + $PosicaoConteudo + 1);
        $Conteudo["Topo"] = substr($Pagina, 0, $PosicaoTag + $PosicaoConteudo + 1);

        echo $Conteudo[$Local];
    }
}

/**
 * Retorna informações da url
 * @return \ArrayObject
 */
function getURL() {
    $Pagina = isset($_GET["URL"]) ? ($_GET["URL"] == "" ? "principal" : trim($_GET["URL"])) : "principal";
    $Pagina = str_replace(" ", "", ucwords(str_replace("-", " ", $Pagina)));
    $Parametros = array();

    $Parametros = explode("/", $Pagina);
    if (count($Parametros) > 0 && !file_exists("Paginas/{$Parametros[0]}.php") && is_dir($Parametros[0])) {
        $Pagina = (file_exists("Paginas/{$Pagina}.php")) ? $Pagina : "Principal";
    } else {
        $Pagina = $Parametros[0];
        unset($Parametros[0]);
        $Parametros = array_values($Parametros);
    }

    $Retorno = new ArrayObject();
    $Retorno->Pagina = (file_exists("Paginas/{$Pagina}.php")) ? trim($Pagina) : "Principal";
    $Retorno->Parametros = $Parametros;

    return $Retorno;
}

/**
 * Retornar a mensagem em formado JSON para o HTML
 * @param String $Tipo Tipo de mensagem (Sucesso,Erro, Ou a classe do exception)
 * @param String $Mensagem Mensagem
 * @param String $Campo Campo
 */
function retornoAjax($Tipo, $Mensagem = null, $Campo = null) {
    if (gettype($Tipo) == "object" && get_class($Tipo) === "ExcecaoNegocios") {
        $Mensagem = $Tipo->getMessage();
        $Campo = $Tipo->getFieldName();
        $Tipo = "Alerta";
    } else if (gettype($Tipo) == "object" && get_class($Tipo) === "ExcecaoDados") {
        $Mensagem = $Tipo->getMessage();
        $Tipo = "Erro";
    } else if ($Tipo === TRUE) {
        $Tipo = "Sucesso";
    } else if ($Tipo === FALSE) {
        $Tipo = "Erro";
    }

    /**
     * Se um tipo de retorno foi definido
     */
    if (gettype($Tipo) === "string") {
        $Dados = array(
            $Tipo => utf8_encode($Mensagem)
        );
        if (!empty($Campo)) {
            $Dados["Campo"] = $Campo;
        }
    } else {
        $Dados = $Tipo;
    }


    echo json_encode($Dados);
}

/**
 * Redirecionar páginas
 * @param string $Pagina Página destino
 */
function redirecionar($Pagina) {
    $Pagina = rtrim($Pagina, "/");

    if (isPaginaAjax()) {
        echo json_encode(array("Redirecionar" => $Pagina));
    } else {
        header("location:" . Caminho . "/{$Pagina}");
    }
    exit;
}

/**
 * 
 * @param string $BancoDados
 * @return \PDO
 */
function getConsulta($SQL, $BancoDados = null) {
    $BancoDados = (is_null($BancoDados)) ? "information_schema" : trim($BancoDados);
    $Conexao = new PDO("mysql:host=localhost;dbname={$BancoDados}", "root", "");
    $Conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $Conexao->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);

    $STMT = $Conexao->prepare($SQL);
    $STMT->execute();
    $Lista = $STMT->fetchAll(PDO::FETCH_CLASS);

    return $Lista;
}

function getAtributosBancoDados($BancoDados, $Tabela, $Codigo = "") {
    $TiposPHP = array("string", "double", "integer", "boolean");
    $Campos = array();
    $Tabela = trim(str_replace(" ", "", $Tabela));
    $CamposTabela = getConsulta("SHOW FULL COLUMNS FROM {$Tabela}", $BancoDados);
    $CodigoAutoIncremento = str_replace("{NomeTabela}", $Tabela, trim($Codigo));

    $SQL = "SELECT 
                i.TABLE_NAME, 
                i.CONSTRAINT_TYPE,     
                i.CONSTRAINT_NAME, 
                k.REFERENCED_TABLE_NAME AS TabelaReferencia, 
                k.REFERENCED_COLUMN_NAME AS ColunaReferencia,
                k.COLUMN_NAME AS Coluna 
            FROM 
                information_schema.TABLE_CONSTRAINTS i 
            LEFT JOIN 
                information_schema.KEY_COLUMN_USAGE k 
            ON 
                i.CONSTRAINT_NAME = k.CONSTRAINT_NAME 
            WHERE 
                k.REFERENCED_TABLE_NAME IS NOT NULL AND
                i.CONSTRAINT_TYPE = 'FOREIGN KEY' AND
                i.TABLE_SCHEMA = '{$BancoDados}' AND 
                i.TABLE_NAME = '{$Tabela}'";


    $ChavesEstrangeira = array();
    $ChaveEstrangeira = getConsulta($SQL, $BancoDados);
    foreach ($ChaveEstrangeira as $Atributo) {
        $ChavesEstrangeira[$Atributo->Coluna] = array(
            "Tabela" => $Atributo->TabelaReferencia,
            "Coluna" => $Atributo->ColunaReferencia
        );
    }

    foreach ($CamposTabela as $Atributo) {
        $Tamanho = getConteudoDelimitadores($Atributo->Type, "(", ")");
        $Tamanho = (isset($Tamanho[0])) ? trim($Tamanho[0]) : 0;

        $Campo = new ArrayIterator();
        $Campo->Nome = trim($Atributo->Field);
        $Campo->TabelaID = ($CodigoAutoIncremento == $Campo->Nome);
        $Campo->Nulo = ($Atributo->Null == "YES");
        $Campo->ChavePrimaria = ($Atributo->Key == "PRI");
        $Campo->ChaveEstrangeira = FALSE;
        $Campo->AutoIncremento = ($Atributo->Extra == "auto_increment");
        $Campo->Tipo = ($Tamanho) ? trim(str_replace("({$Tamanho})", "", $Atributo->Type)) : trim($Atributo->Type);
        $Campo->Tamanho = $Tamanho;
        $Campo->ValorPadrao = $Atributo->Default;


        $Campo->Tipo = trim(str_replace("unsigned", "", $Campo->Tipo));

        if (strpos($Campo->Tipo, "int") !== false) {
            $Campo->TipoPHP = $TiposPHP[2];
        } else if ($Campo->Tipo == "decimal") {
            $Campo->TipoPHP = $TiposPHP[1];
        } else if ($Campo->Tipo == "bit") {
            $Campo->TipoPHP = $TiposPHP[3];
            $Campo->ValorPadrao = (is_null($Campo->ValorPadrao)) ? NULL : (($Campo->ValorPadrao == "b'1'"));
        } else {
            $Campo->TipoPHP = $TiposPHP[0];
        }

        if ($Campo->Tipo == "date") {
            $Campo->Tamanho = 10;
        } else if ($Campo->Tipo == "datetime" || $Campo->Tipo == "timestamp") {
            $Campo->Tamanho = 19;
        }

        if (isset($ChavesEstrangeira[$Campo->Nome])) {
            $Campo->ChaveEstrangeira = $ChavesEstrangeira[$Campo->Nome];
        }

        $Campos[] = $Campo;
        unset($Campo);
    }

    return $Campos;
}

//</editor-fold>
//<editor-fold  defaultstate="collapsed" desc="Funções para String">

/**
 * Função para remover os excessos de espaços de uma determinada string
 * @param string $Texto
 * @return string
 */
function removerExcessoEspacos($Texto) {
    return preg_replace("/\s+/", " ", trim($Texto));
}

/**
 * Recuperar conteúdo entre deliminitadores de uma determinada string
 * @param string $Texto
 * @param string $Inicio
 * @param string $Fim
 * @param bool $ExibioInicioFim
 * @return string
 */
function getConteudoDelimitadores($Texto, $Inicio, $Fim, $ExibioInicioFim = false) {
    $Encontrados = array();
    $Conteudo = array_filter(explode($Inicio, $Texto));

    //exibir($Conteudo);
    //exibir(array_filter($Conteudo));
    foreach ($Conteudo as $Contador => $ConteudoDelimitador) {
        $Ocorrencias = substr_count($ConteudoDelimitador, $Fim);
        if ($Ocorrencias > 0) {
            $TextoAuxiliar = substr($ConteudoDelimitador, 0, stripos($ConteudoDelimitador, $Fim));
            $Encontrados[] = ($ExibioInicioFim) ? "{$Inicio}{$TextoAuxiliar}{$Fim}" : $TextoAuxiliar;
        }
        if ($Ocorrencias > 1) {
            $TextoNovo = $ConteudoDelimitador;

            for ($i = 1; $i < $Ocorrencias; $i++) {
                $TextoNovo = substr($ConteudoDelimitador, 0, strripos($TextoNovo, $Fim, 0));
                $TextoAuxiliar = $TextoNovo;

                // exibir("antes:$TextoAuxiliar");

                for ($a = 1; $a <= (substr_count($TextoNovo, $Fim) + 1); $a++) {
                    if (isset($Conteudo[$Contador - $a])) {
                        $TextoAuxiliar = $Conteudo[$Contador - $a] . $Inicio . $TextoAuxiliar;
                    }
                }

                // $TextoAuxiliar = rtrim($TextoAuxiliar,$Fim);
                //$TextoAuxiliar = ltrim($TextoAuxiliar,$Inicio);
                // exibir("depois:$TextoAuxiliar");

                $Encontrados[] = ($ExibioInicioFim) ? "{$Inicio}{$TextoAuxiliar}{$Fim}" : $TextoAuxiliar;
            }
        }
    }
    return $Encontrados;
}

/**
 * Criptografar string
 * @param string $Texto Texto que será usado para operação
 * @param string $Criptografar Caso true, criptogragar, caso false, descriptografar
 * @author Marcel Aimar <marcel_aimar@hotmail.com>
 * @return string
 */
function criptografia($Texto, $Criptografar = true) {
    $Retorno = "";

    if ($Criptografar) {
        $Invertido = "";
        foreach (str_split(base64_encode($Texto)) as $Letra) {
            $Invertido.= (strtolower($Letra) == $Letra) ? strtoupper($Letra) : strtolower($Letra);
        }

        $Texto = base64_encode($Invertido);
        $Tamanho = (strlen($Texto) / 2) - 5;

        $Comeco = substr($Texto, 0, $Tamanho);
        $Fim = substr($Texto, ($Tamanho * -1));

        $Texto = $Fim . substr($Texto, $Tamanho, ($Tamanho * -1)) . $Comeco;

        $Retorno = "";
        foreach (str_split($Texto) as $Letra) {
            $Retorno.= (strtolower($Letra) == $Letra) ? strtoupper($Letra) : strtolower($Letra);
        }

        $Retorno = base64_encode(convert_uuencode($Retorno));
    } else {
        $Texto = convert_uudecode(base64_decode($Texto));

        $Invertido = "";
        foreach (str_split($Texto) as $Letra) {
            $Invertido.= (strtolower($Letra) == $Letra) ? strtoupper($Letra) : strtolower($Letra);
        }
        $Texto = $Invertido;

        $Tamanho = (strlen($Texto) / 2) - 5;

        $Comeco = substr($Texto, 0, $Tamanho);
        $Fim = substr($Texto, ($Tamanho * -1));

        $Texto = substr($Texto, $Tamanho, ($Tamanho * -1));
        $Texto = base64_decode("{$Fim}{$Texto}{$Comeco}");

        $Invertido = "";
        foreach (str_split($Texto) as $Letra) {
            $Invertido.= (strtolower($Letra) == $Letra) ? strtoupper($Letra) : strtolower($Letra);
        }
        $Retorno = base64_decode($Invertido);
    }

    return $Retorno;
}

//</editor-fold>
?>