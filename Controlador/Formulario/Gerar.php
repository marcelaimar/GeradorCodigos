<?php

try {
    $ChavePrimaria = intval($_POST["ChavePrimaria"]);
    $ChaveEstrangeira = intval($_POST["ChaveEstrangeira"]);

    $AtributoNome = intval($_POST["AtributoNome"]);
    $AtributoNecessario = intval($_POST["AtributoNecessario"]);
    $AtributoTamanhoMaximo = intval($_POST["AtributoTamanhoMaximo"]);
    $AtributoID = intval($_POST["AtributoID"]);
    $AtributoTamanhoMaximo = intval($_POST["AtributoTamanhoMaximo"]);
    $AtributoParagrafo = intval($_POST["AtributoParagrafo"]);
    $AtributoEtiqueta = intval($_POST["AtributoEtiqueta"]);

    $Campos = getAtributosBancoDados($_POST["BancoDados"], $_POST["Tabela"], $_POST["Codigo"]);

    //<editor-fold  defaultstate="collapsed" desc="Desenhando o formulário">
    $HTML = "<form>";

    foreach ($Campos as $Campo) {
        $Atributos = array();
        $Exibir = (($Campo->ChaveEstrangeira && $ChaveEstrangeira) || (($Campo->ChavePrimaria && $ChavePrimaria) || !$Campo->ChavePrimaria));

        if ($Exibir) {
            if ($AtributoID) {
                $Atributos[] = "id=\"{$Campo->Nome}\"";
            }
            if ($AtributoNome) {
                $Atributos[] = "name=\"{$Campo->Nome}\"";
            }

            if ($AtributoNecessario && !$Campo->Nulo && !$Campo->ChaveEstrangeira) {
                $Atributos[] = "required=\"required\"";
            }
            if ($AtributoTamanhoMaximo && !$Campo->ChaveEstrangeira && !in_array($Campo->Tipo, array("bit", "text"))) {
                $Atributos[] = "maxlength=\"{$Campo->Tamanho}\"";
            }

            if ($AtributoParagrafo) {
                $HTML.= "\r\t<p>";
            }
            if ($AtributoEtiqueta && $Campo->TipoPHP !== "boolean") {
                $HTML.= "\r\t   <label>{$Campo->Nome}</label>";
            }

            $Atributos = implode(" ", $Atributos);

            if ($Campo->ChaveEstrangeira && $ChaveEstrangeira) {
                $HTML.= "\r\t   <select {$Atributos}>"
                        . "\r\t       <option value=\"\"></option>"
                        . "\r\t   </select>";
            } else if (($Campo->ChavePrimaria && $ChavePrimaria) || !$Campo->ChavePrimaria) {
                if ($Campo->TipoPHP == "boolean") {
                    // echo $Campo->ValorPadrao ;

                    $Atributos .= (intval($Campo->ValorPadrao)) ? " checked=\"checked\"" : "";

                    $HTML.= "\r\t   <label>"
                            . "\r\t     <input type=\"checkbox\" {$Atributos}/>"
                            . "\r\t     <span>{$Campo->Nome}</span>"
                            . "\r\t   </label>";
                } else if ($Campo->Tipo == "text") {
                    $HTML.= "\r\t   <textarea {$Atributos}></textarea>";
                } else {
                    $HTML.= "\r\t   <input type=\"text\" {$Atributos}/>";
                }
            }

            if ($AtributoParagrafo) {
                $HTML.= "\r\t</p>";
            } else {
                $HTML.= "\r";
            }
        }
    }

    if ($AtributoParagrafo) {
        $HTML.= "\r\t<p>";
    }

    $HTML.= "\r\t   <input type=\"submit\" name=\"Salvar\" value=\"Salvar\" />"
            . "\r\t   <input type=\"reset\" name=\"Cancelar\" value=\"Cancelar\" />";

    if ($AtributoParagrafo) {
        $HTML.= "\r\t</p>";
    } else {
        $HTML.= "\r";
    }

    $HTML .= "\r</form>";

    echo json_encode(array("Formulario" => $HTML));

    //</editor-fold>
    // print_r($Campos);
} catch (Exception $exc) {
//    retornoAjax(false , "Ops! Desculpe, falha ao tentar cadastrar o novo" Informe o erro);
}
?>

