<?php

chdir( dirname(__DIR__));

include_once './Conteudo/Definicoes.php';
include_once './Conteudo/Funcoes.php';

if (Local) {
    require_once './Camada/Entidade/JavaScriptPacker.php';
}
header('Content-type: text/javascript');

$ConteudojQuery = "";
$ArquivoEmpacotado = "./jQuery/PadraoEmpacotado.js";
$DataEmpacotado = (file_exists($ArquivoEmpacotado)) ? filemtime($ArquivoEmpacotado) : 0;

$NaoEmpacotar = array(
    "./jQuery/Componente/jQuery.js",
    "./jQuery/Componente/jQuery.UI.js",
    "./jQuery/Componente/jQuery.Select2.js",
    );
$Empacotar = false;

//<editor-fold  defaultstate="collapsed" desc="Recupera conteúdo de arquivos">
if (Local) {
    foreach (array("./jQuery/Componente", "./jQuery/Pagina", "./jQuery") as $Pasta) {
        foreach (array_diff(scandir($Pasta, 0), array('..', '.')) as $Arquivo) {
            if (substr($Arquivo, -3) === ".js" && $Arquivo != "PadraoEmpacotado.js") {
                if (!in_array("{$Pasta}/{$Arquivo}", $NaoEmpacotar)) {
                    $ConteudojQuery.= file_get_contents("{$Pasta}/{$Arquivo}");
                }

                if (filemtime("{$Pasta}/{$Arquivo}") > $DataEmpacotado) {
                    $Empacotar = true;
                }
            }
        }
    }
}
//</editor-fold>
//<editor-fold  defaultstate="collapsed" desc="Empacota conteúdo e atualiza arquivo empacotado">
if ($Empacotar) {
    $ConteudojQuery = trim(preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $ConteudojQuery));
    if (get_magic_quotes_gpc()) {
        $ConteudojQuery = stripslashes($ConteudojQuery);
    }

    $Packer = new JavaScriptPacker($ConteudojQuery);
    $ConteudojQuery = trim($Packer->pack());

    $ConteudoNaoEmpacotado = "";
    foreach ($NaoEmpacotar as $Arquivo) {
        $ConteudoNaoEmpacotado.= file_get_contents($Arquivo);
    }
    $ConteudoNaoEmpacotado = trim(preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $ConteudoNaoEmpacotado));
    $ConteudojQuery = "{$ConteudoNaoEmpacotado}{$ConteudojQuery}";

    file_put_contents($ArquivoEmpacotado, $ConteudojQuery);
} else {
    $ConteudojQuery = file_get_contents($ArquivoEmpacotado);
}
//</editor-fold>

echo $ConteudojQuery;
?>

