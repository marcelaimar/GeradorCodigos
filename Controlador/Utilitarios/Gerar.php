<?php

try {
    $Tabela = trim(str_replace(" ", "", $_POST["Tabela"]));
    $Opcao = trim($_POST["Opcao"]);
    $ChavePrimaria = intval($_POST["ChavePrimaria"]);
    $HTML = "";

    $Campos = getAtributosBancoDados($_POST["BancoDados"], $Tabela, $_POST["Codigo"]);

    //<editor-fold  defaultstate="collapsed" desc="Opção - SetterPOST">
    if ($Opcao == "SetterPOST") {
        foreach ($Campos as $Campo) {
            if (($Campo->ChavePrimaria && $ChavePrimaria) || !$Campo->ChavePrimaria) {
                $HTML.="\${$Tabela}->set{$Campo->Nome}(\$_POST[\"{$Campo->Nome}\"]);\n";
            }
        }
    }

    //</editor-fold>

    echo json_encode(array("Conteudo" => $HTML));
} catch (Exception $exc) {
//    retornoAjax(false , "Ops! Desculpe, falha ao tentar cadastrar o novo" Informe o erro);
}
?>

