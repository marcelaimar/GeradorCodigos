Bem-Vindo

<?php
/*
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
    i.TABLE_SCHEMA = 'ApoioGenetica' AND 
    i.TABLE_NAME = 'Acesso'";

$Tabela = "";
$Colunas = getConsulta($SQL);

print_r($Colunas);*/

/*foreach ($Tabelas as $Tabela) {
   $Colunas = getConsulta("SHOW FULL COLUMNS FROM {$Tabela}", "ApoioGenetica");
   print_r($Colunas);
   echo "<br /><br />";
}*/
?>


