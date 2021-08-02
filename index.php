<?php

header('Content-type: text/html; charset=ISO-8859-1');
include_once './Conteudo/Definicoes.php';
include_once './Conteudo/Funcoes.php';

try {
    $PaginaConteudo = array();
    $URL = getURL();
    $Pagina = $URL->Pagina;


    if (isPaginaAjax()) {
        $Arquivo = "Paginas/{$Pagina}.php";

        ob_start();
        include($Arquivo);

        $PaginaConteudo["Classe"] = $Pagina;
        $PaginaConteudo["Parametros"] = $URL->Parametros;
        $PaginaConteudo["Conteudo"] = base64_encode(ob_get_contents());
        ob_end_clean();

        echo json_encode($PaginaConteudo);
    } else if (isAjax()) {
        include "Controlador/{$_GET["URL"]}.php";
    } else {
        $Parametros = $URL->Parametros;
        $ConteudoPagina = (substr($Pagina, 0, 9) !== "Conteudo/");

        if ($ConteudoPagina && $Pagina !== "Download") {
            incluirEstrutura("Topo");
        }
        include "Paginas/{$Pagina}.php";
        if ($ConteudoPagina && $Pagina !== "Download") {
            incluirEstrutura("Rodape");
        }
    }
} catch (Exception $exc) {
    
}
?>
