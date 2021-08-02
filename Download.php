<?php

//$URL = getURL();
//print_r($URL);
//echo "teste";
$parametros = $_GET["p"] ?? "";
if ($parametros) {
    $Arquivo = trim($parametros);
    $Caminho = "Arquivo/{$Arquivo}";

    if (file_exists($Caminho)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/force-download');
        header("Content-Disposition: attachment; filename={$Arquivo}");
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header("Content-Length:" . filesize($Caminho));
        readfile($Caminho);
        unlink($Caminho);
    } else {
        echo "<div>0</div>";
    }
} else {
    echo "<div>0</div>";
}
?>
