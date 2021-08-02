<?php

if ($_POST["BancoDados"]) {
    $Lista = getConsulta("SHOW FULL TABLES WHERE Table_Type != 'VIEW'", trim($_POST["BancoDados"]));
    $Banco = trim(strtolower($_POST["BancoDados"]));

    $Tabelas = array();
    foreach ($Lista as $Tabela) {
        $Tabela = (array) $Tabela;
        $Nome = ucfirst($Tabela["Tables_in_{$Banco}"]);
        $Nome = preg_replace('/([a-z0-9])([A-Z])/', "$1 $2", $Nome);

        $Tabelas[] = $Nome;
    }
    echo json_encode($Tabelas);
}
?>
