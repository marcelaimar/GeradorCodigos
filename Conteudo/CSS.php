<?php

chdir( dirname(__DIR__));

include_once './Conteudo/Definicoes.php';
include_once './Conteudo/Funcoes.php';

header('Content-type: text/css');

$ConteudoCSS = "";
$ArquivoEmpacotado = "./CSS/PadraoEmpacotado.css";
$DataEmpacotado = (file_exists($ArquivoEmpacotado)) ? filemtime($ArquivoEmpacotado) : 0;
$Empacotar = false;

//<editor-fold  defaultstate="collapsed" desc="Funções">

function exibir($t) {

    echo "<pre>";
    print_r($t);
    echo "<pre>";
}

function limparCSS($CSS) {
    $CSS = str_replace(array("\r", "\n", "\t", "\v"), '', $CSS);
    $CSS = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $CSS);
    $CSS = removerExcessoEspacos($CSS);
    //$CSS = preg_replace("/[ ]{2,}/", " ", trim($CSS));
    // $CSS = preg_replace("/\s+/", " ", trim($CSS));

    $CSS = str_replace(array("{ ", " {"), "{", $CSS);
    $CSS = str_replace(array("} ", " }"), "}", $CSS);
    $CSS = str_replace(array(": ", " :"), ":", $CSS);
    $CSS = str_replace(array("; ", " ;"), ";", $CSS);
    return removerExcessoEspacos($CSS);
    //return preg_replace("/[ ]{2,}/", " ", trim($CSS));
}

function getFuncoesCSS($CSS, $Global = false) {
    $Nome = "::" . ($Global ? "global_" : "") . "functions";

    $FiltraFuncoes = array();
    $Funcoes = array();

    if (strpos($CSS, $Nome) !== false) {
        $FiltraFuncoes = array();
        while (strpos($CSS, $Nome) !== false) {
            $Funcao = substr($CSS, strpos($CSS, $Nome));
            $Funcao = substr($Funcao, 0, strpos($Funcao, "}}") + 2);

            $CSS = str_ireplace($Funcao, "", $CSS);
            $FiltraFuncoes[] = $Funcao;
        }

        foreach ($FiltraFuncoes as $Funcao) {
            $Funcao = explode("function:", $Funcao);

            /* Começa de "1" pois na posição "0" tem "::functions{" */
            for ($i = 1; $i < count($Funcao); $i++) {

                $Parametros = substr(substr($Funcao[$i], strpos($Funcao[$i], "(")), 0, strpos(substr($Funcao[$i], strpos($Funcao[$i], "(")), "){") + 1);
                $Parametros = getConteudoDelimitadores($Parametros, "(", ")");
                $Parametros = preg_split('/\,(?![^(]*\))/', end($Parametros));

                $Conteudo = getConteudoDelimitadores($Funcao[$i], "{", "}");

                $ListaParametros = array();
                foreach ($Parametros as $Parametro) {
                    $Parametro = explode(" ", trim($Parametro), 2);
                    $ListaParametros[] = array(
                        "Variavel" => trim($Parametro[0]),
                        "Valor" => (isset($Parametro[1])) ? trim($Parametro[1]) : ""
                    );
                }

                $Funcoes[trim(substr($Funcao[$i], 0, strpos($Funcao[$i], "(")))] = array(
                    "Conteudo" => trim($Conteudo[0]),
                    "Parametros" => $ListaParametros
                );
            }
        }
    }

    unset($FiltraFuncoes);
    unset($Funcao);

    return array(
        "Funcoes" => $Funcoes,
        "CSS" => removerExcessoEspacos($CSS)
    );
}

function getVariaveisCSS($CSS, $Global = false) {
    $Nome = "::" . ($Global ? "global_" : "") . "variables";

    $FiltraVariaveis = array();
    $Variaveis = array();


    if (strpos($CSS, $Nome) !== false) {
        //exibir($Nome);

        while (strpos($CSS, $Nome) !== false) {
            $Variavel = substr($CSS, strpos($CSS, $Nome));
            $Variavel = substr($Variavel, 0, strpos($Variavel, "}") + 1);

            $CSS = str_ireplace($Variavel, "", $CSS);
            $FiltraVariaveis[] = $Variavel;
        }

        $VariaveisTexto = "";
        foreach ($FiltraVariaveis as $Variavel) {
            $VariaveisTexto.= str_replace("}", "", substr($Variavel, strlen($Nome) + 1));
        }

        foreach (explode(";", $VariaveisTexto) as $Variavel) {
            $VariavelValor = explode(":", $Variavel, 2);
            if (count($VariavelValor) == 2) {
                $Variaveis[trim($VariavelValor[0])] = trim($VariavelValor[1]);
            }
        }
    }


    return array(
        "Variaveis" => $Variaveis,
        "CSS" => removerExcessoEspacos($CSS)
    );
}

function atribuirFuncoes($CSS, array $Funcoes) {
    if (strpos($CSS, "function:") !== false) {
        $FuncoesCSS = explode("function:", $CSS);
        unset($FuncoesCSS[0]);

        foreach ($FuncoesCSS as $FuncaoCSS) {
            $Funcao = substr($FuncaoCSS, 0, strpos($FuncaoCSS, ");"));
            $FuncaoNoCSS = "function:{$Funcao});";
            $Funcao = explode("(", trim($Funcao), 2);

            if (isset($Funcao[1])) {
                $Funcao[0] = trim($Funcao[0]);
                $Funcao[1] = preg_split('/\,(?![^(]*\))/', $Funcao[1]);

                if (array_key_exists($Funcao[0], $Funcoes)) {

                    $ConteudoSubstituido = $Funcoes[$Funcao[0]]["Conteudo"];
                    foreach ($Funcoes[$Funcao[0]]["Parametros"] as $Contador => $FuncaoValores) {
                        $Valor = (strlen(trim($Funcao[1][$Contador]))) ? $Funcao[1][$Contador] : $FuncaoValores["Valor"];
                        //ACHAR FUNÇÃO PRA SUBSTITUIR APENAS A PALAVRA .. neste caso #tes vai substituir em #testando
                        $ConteudoSubstituido = str_ireplace($FuncaoValores["Variavel"], trim($Valor), $ConteudoSubstituido);
                    }            
                    $CSS = str_ireplace($FuncaoNoCSS, str_replace("'", "", $ConteudoSubstituido), $CSS);
                }
            }
        }
    }
    return removerExcessoEspacos($CSS);
}

function atribuirVariaveis($CSS, array $Variaveis) {
    if (strpos($CSS, "_") !== false) {
        foreach ($Variaveis as $Variavel => $Valor) {
            //$CSS = str_ireplace($Variavel, $Valor, $CSS);
            $CSS = str_ireplace($Variavel, str_replace("'", "", $Valor), $CSS);
        }
    }

    return removerExcessoEspacos($CSS);
}

//</editor-fold>

$VariaveisGlobais = array();
$FuncoesGlobais = array();

if (Local) {

    //<editor-fold  defaultstate="collapsed" desc="Recupera conteúdo de arquivos">
    $Arquivos = array();
    foreach (array("./CSS/Componente", "./CSS", "./CSS/Pagina") as $Pasta) {
        foreach (array_diff(scandir($Pasta, 1), array('..', '.')) as $Arquivo) {
            if (strtolower(substr($Arquivo, -4)) === ".css" && $Arquivo != "PadraoEmpacotado.css") {
                $Arquivos[] = array(
                    "Nome" => substr($Arquivo, 0, -4),
                    "Caminho" => "{$Pasta}/{$Arquivo}"
                );
                if (filemtime("{$Pasta}/{$Arquivo}") > $DataEmpacotado) {
                    $Empacotar = true;
                }
            }
        }
    }
    //</editor-fold>

    if ($Empacotar && $Arquivos) {
        foreach ($Arquivos as $Arquivo) {
            $CSS = limparCSS(str_ireplace("_Pagina", "#Conteudo.{$Arquivo["Nome"]}", file_get_contents($Arquivo["Caminho"])));

            //<editor-fold  defaultstate="collapsed" desc="Verifica se existem variaveis locais ou globais neste arquivo">
            /**
             * Recupera as variaveis globais e retira-as do CSS
             */
            $VariavelGlobalCSS = getVariaveisCSS($CSS, true);
            $CSS = $VariavelGlobalCSS["CSS"];

            /**
             * Recupera as variaveis locais e retira-as do CSS
             */
            $VariavelCSS = getVariaveisCSS($CSS);
            $CSS = $VariavelCSS["CSS"];

            /**
             * Atribui ao CSS apenas as variaveis locais deste arquivo
             */
            $CSS = atribuirVariaveis($CSS, $VariavelCSS["Variaveis"]);

            /**
             * Mescla as variaveis globais deste arquivo com as outras variaveis globais
             */
            $VariaveisGlobais = array_merge($VariaveisGlobais, $VariavelGlobalCSS["Variaveis"]);

            //</editor-fold>
            //<editor-fold  defaultstate="collapsed" desc="Verifica se existem funções locais ou globais neste arquivo">
            /**
             * Recupera as funções globais e retira-as do CSS
             */
            $FuncaoGlobalCSS = getFuncoesCSS($CSS, true);
            $CSS = $FuncaoGlobalCSS["CSS"];

            /**
             * Recupera as funções locais e retira-as do CSS
             */
            $FuncaoCSS = getFuncoesCSS($CSS);
            $CSS = $FuncaoCSS["CSS"];

            /**
             * Atribui ao CSS apenas as funcções locais deste arquivo
             */
            $CSS = atribuirFuncoes($CSS, $FuncaoCSS["Funcoes"]);

            /**
             * Mescla as funções globais deste arquivo com as outras funções globais
             */
            $FuncoesGlobais = array_merge($FuncoesGlobais, $FuncaoGlobalCSS["Funcoes"]);

            //</editor-fold>

            $ConteudoCSS .= $CSS;
        }

        $ConteudoCSS = atribuirVariaveis($ConteudoCSS, $VariaveisGlobais);
        $ConteudoCSS = atribuirFuncoes($ConteudoCSS, $FuncoesGlobais);
        file_put_contents($ArquivoEmpacotado, $ConteudoCSS);
    }
}

if (!$Empacotar) {
    $ConteudoCSS = file_get_contents($ArquivoEmpacotado);
}

$ConteudoCSS = str_ireplace("Imagem/", CaminhoImagem . "/", $ConteudoCSS);
echo $ConteudoCSS;
exit;
?>

