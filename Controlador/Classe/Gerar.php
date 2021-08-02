<?php

try {
    $BancoDados             = trim($_POST["BancoDados"]);
    $classe                 = intval($_POST["Classe"]);
    $Namespace              = $_POST["Namespace"];
    $Maiuscula              = intval($_POST["Maiuscula"]);
    $chaveEstrangeiraObjeto = intval($_POST["ChaveEstrangeiraObjeto"]);
    $conteudo               = array("<?php");
    $ConverterUTF8          = intval($_POST["ConverterUTF8"]);
    $Tabela                 = trim(str_replace(" ", "", $_POST["Tabela"]));
    $InserirComentario      = intval($_POST["Comentario"]);
    $Avanco                 = "";
    $tipos                  = [
        "string"  => ["char", "date", "datetime", "time", "timestamp", "text", "varchar", "longtext", "blob", "enum", "json"],
        "double"  => ["decimal"],
        "integer" => ["bigint", "int", "smallint", "tinyint", "year"],
        "boolean" => ["bit"]
    ];
    $classes                = [
        "Representa objeto de persistência de dados para entidade",
        "Representa objeto de regras de negócio para entidade",
        "Representa objeto de acesso a dados para entidade"
    ];
    $sufixoClasse           = ["", "BO", "DAO"];
    $nomeClasse             = "{$Tabela}{$sufixoClasse[$classe]}";

    $nomeArquivo = rtrim(strtolower(implode("-", array_filter(preg_split('/(?=[A-Z])/', $Tabela))) . "-" . $sufixoClasse[$classe]), "-");
    $nomeArquivo = "{$nomeArquivo}.php";
    $ano         = date("Y");

    $adicionarNamespace = !empty($Namespace);

    if ($adicionarNamespace) {
        $Avanco = "\t";
    }


    //<editor-fold  defaultstate="collapsed" desc="Recuperando Colunas">
    $SQL = "SELECT DISTINCT
                Coluna.TABLE_NAME AS Tabela,
                Tabela.TABLE_COLLATION AS Charset ,
                Tabela.ENGINE AS Engine,
                Tabela.VERSION AS Versao,
                Tabela.TABLE_ROWS AS TotalDados,
                IF(Tabela.AUTO_INCREMENT AND Coluna.EXTRA LIKE '%auto_increment%',TRUE,FALSE) AS AutoIncremento ,
                IF(Coluna.COLUMN_TYPE LIKE '%unsigned%',TRUE,FALSE) AS SomentePositivo , 
                IF(Coluna.COLUMN_TYPE LIKE '%zerofill%',TRUE,FALSE) AS PreencherEspacos , 
                Tabela.CREATE_TIME AS DataCriacao ,
                Tabela.UPDATE_TIME AS DataModificacao,
                Coluna.COLUMN_NAME AS Coluna,
                Coluna.ORDINAL_POSITION AS Posicao,
                CASE 
                    WHEN CAST(Coluna.COLUMN_DEFAULT AS CHAR) = \"b'1'\" THEN TRUE
                    WHEN CAST(Coluna.COLUMN_DEFAULT AS CHAR) = \"b'0'\" THEN FALSE
                    ELSE Coluna.COLUMN_DEFAULT
                END AS ValorPadrao,
                IF(Coluna.IS_NULLABLE = 'NO',TRUE,FALSE) AS Obrigatorio,
                Coluna.DATA_TYPE AS Tipo,
                CASE 
                    WHEN   
                        (Coluna.DATA_TYPE = 'decimal')
                    THEN
                        Coluna.NUMERIC_PRECISION
                    WHEN   
                        (Coluna.DATA_TYPE LIKE '%int%')
                    THEN
                        (Coluna.NUMERIC_PRECISION +1)
                    WHEN   
                        (Coluna.DATA_TYPE  = 'timestamp' OR Coluna.DATA_TYPE  = 'datetime' )  
                    THEN
                        19
                    WHEN   
                        (Coluna.DATA_TYPE  = 'time')
                    THEN
                        8
                    WHEN   
                        (Coluna.DATA_TYPE  = 'date')
                    THEN
                        10
                    WHEN   
                        (Coluna.DATA_TYPE  = 'bit')
                    THEN
                        1
                    ELSE
                        Coluna.CHARACTER_MAXIMUM_LENGTH
                END as Tamanho,
                Coluna.NUMERIC_SCALE AS CasasDecimais ,
                Coluna.COLUMN_TYPE AS TipoColuna,       
                IF(Coluna.COLUMN_KEY = '', NULL, Coluna.COLUMN_KEY) AS TipoChave,                
                Coluna.EXTRA AS Extra,
                Coluna.COLUMN_COMMENT AS Comentario  ,
                Referencia.RestricaoTipo,
                Referencia.RestricaoNome,
                Referencia.TabelaReferencia,
                Referencia.ColunaReferencia
            FROM     
                TABLES AS Tabela,
                COLUMNS AS Coluna
            LEFT JOIN
                (
                    SELECT 
                        k.TABLE_NAME AS Tabela,
                        k.COLUMN_NAME AS Coluna,    
                        i.CONSTRAINT_TYPE AS RestricaoTipo,
                        k.CONSTRAINT_NAME AS RestricaoNome,          
                        GROUP_CONCAT(k.COLUMN_NAME SEPARATOR ',')  AS RestricaoAgrupada,
                        MAX(k.REFERENCED_TABLE_NAME) AS TabelaReferencia,             
                        MAX(k.REFERENCED_COLUMN_NAME) AS ColunaReferencia
                    FROM 
                        KEY_COLUMN_USAGE k      
                    INNER JOIN
                        TABLE_CONSTRAINTS i 
                    ON 
                        i.CONSTRAINT_NAME = k.CONSTRAINT_NAME  
                    AND
                        i.TABLE_SCHEMA = k.TABLE_SCHEMA
                    AND 
                        i.TABLE_NAME = k.TABLE_NAME
                    WHERE 
                        k.TABLE_SCHEMA = '{$BancoDados}'
                    AND
                        k.TABLE_NAME = '{$Tabela}'
                    AND
                        k.CONSTRAINT_SCHEMA = k.TABLE_SCHEMA
                    GROUP BY  
                        k.TABLE_NAME,
                        i.CONSTRAINT_TYPE,
                        k.CONSTRAINT_NAME
                    ORDER BY  
                        k.TABLE_NAME,
                        k.COLUMN_NAME
                ) AS Referencia
            ON
                Referencia.Coluna = Coluna.COLUMN_NAME
            AND
                Referencia.Tabela = Coluna.TABLE_NAME

            WHERE 
                Coluna.TABLE_SCHEMA = '{$BancoDados}'
            AND
                Tabela.TABLE_SCHEMA = Coluna.TABLE_SCHEMA
            AND
                Tabela.TABLE_NAME = Coluna.TABLE_NAME
            AND 
                Tabela.TABLE_TYPE = 'BASE TABLE'
            AND 
                Tabela.TABLE_NAME = '{$Tabela}'
            GROUP BY
                Coluna.COLUMN_NAME
            ORDER BY
                Tabela, 
                Posicao";

    $colunas = getConsulta($SQL);
    //</editor-fold>
    //<editor-fold  defaultstate="collapsed" desc="Define o nome do arquivo">

    $CaminhoArquivo = "./Arquivo/{$nomeArquivo}";
    //</editor-fold>
    //<editor-fold  defaultstate="collapsed" desc="Adiciona o cabeçalho">
    if ($InserirComentario) {
        $conteudo[] = "/**";
        $conteudo[] = "* Marcel Aimar Estácio";
        $conteudo[] = "* Desenvolvedor Web";
        $conteudo[] = "* ";
        $conteudo[] = "* Humberto de Campos, 161 - Sumaré";
        $conteudo[] = "* Presidente Venceslau, CEP 19400-000, Brasil";
        $conteudo[] = "* Telefone: +55(18)997467617  ";
        $conteudo[] = "* ";
//        $Conteudo[] = "* Compusofts Informática";
//        $Conteudo[] = "* Soluções em informação e automação";
//        $Conteudo[] = "* ";
//        $Conteudo[] = "* Emilia Yoshiko Takakura Omori Computadores ME";
//        $Conteudo[] = "* CNPJ: 67.049.932/0001-08";
//        $Conteudo[] = "* Av. Dom Pedro II, 146, Centro";
//        $Conteudo[] = "* Presidente Venceslau, CEP 19400-000, Brasil";
//        $Conteudo[] = "* Telefone: +55(18)3271-3245";
//        $Conteudo[] = "* http://www.compusofts.com.br";
//        $Conteudo[] = "* contato@compusofts.com.br";
//        $Conteudo[] = "* ";
        $conteudo[] = "* Classe {$nomeClasse}";
        $conteudo[] = "* {$classes[$classe]} {$Tabela}";
        $conteudo[] = "* ";
        $conteudo[] = "* @name {$nomeClasse}";
        $conteudo[] = "* @access public ";

        if (!empty($Namespace)) {
            $conteudo[] = "* @package {$Namespace}";
        }

        $conteudo[] = "* @license https://creativecommons.org/licenses/by-nc-nd/4.0/legalcode CC BY-NC-ND";
        $conteudo[] = "* @copyright (c) {$ano}";
        $conteudo[] = "* @author Marcel Aimar <marcel_aimar@hotmail.com>";

        if ($adicionarNamespace) {
            $conteudo[] = "*/";
            $conteudo[] = "";
            $conteudo[] = "namespace {$Namespace} {";
            $conteudo[] = "";
            if ($classe == 0) {
                $conteudo[] = "{$Avanco}/**";
            }
            $adicionarNamespace = false;
        } else {
            $conteudo[] = "* ";
        }

        if ($classe == 0) {
            $conteudo[] = "{$Avanco}* @dbtable     {$Tabela}";
            $conteudo[] = "{$Avanco}* @dbengine    {$colunas[0]->Engine}";
            $conteudo[] = "{$Avanco}* @dbcharset   {$colunas[0]->Charset}";
        }

        if ((empty($Namespace)) || (!empty($Namespace) && $classe == 0)) {
            $conteudo[] = "{$Avanco}*/";
        }
    }

    //</editor-fold>


    if ($adicionarNamespace) {
        $conteudo[] = "namespace {$Namespace} {";
    }

    if ($classe == 0) {

        $conteudo[] = "{$Avanco}class {$nomeClasse} {";

        //<editor-fold  defaultstate="collapsed" desc="Declaração de variáveis">        
        $conteudo[] = "\t//<editor-fold  defaultstate=\"collapsed\" desc=\"Declaração de variáveis\">";
        $conteudo[] = "\t";
        foreach ($colunas as &$coluna) {
            foreach ($tipos as $Tipo => $Valores) {
                if (in_array($coluna->Tipo, $Valores, true)) {
                    $coluna->TipoPHP = $Tipo;
                    break;
                }
            }

            $coluna->Variavel         = $Maiuscula ? ucfirst($coluna->Coluna) : lcfirst($coluna->Coluna);
            $coluna->AutoIncremento   = intval($coluna->AutoIncremento);
            $coluna->SomentePositivo  = intval($coluna->SomentePositivo);
            $coluna->PreencherEspacos = intval($coluna->PreencherEspacos);
            $coluna->Obrigatorio      = intval($coluna->Obrigatorio);
            $coluna->CasasDecimais    = intval($coluna->CasasDecimais);

            $coluna->ChavePrimaria    = ($coluna->TipoChave == "PRI" || $coluna->RestricaoTipo == "PRIMARY KEY");
            $coluna->ChaveEstrangeira = (!empty($coluna->TabelaReferencia) && !empty($coluna->ColunaReferencia));
            $coluna->Unico            = ($coluna->TipoChave == "UNI" && $coluna->RestricaoTipo == "UNIQUE");

            if ($chaveEstrangeiraObjeto && $coluna->ChaveEstrangeira && $coluna->TabelaReferencia) {
                $coluna->TipoPHP  = $coluna->TabelaReferencia;
                $coluna->Variavel = $Maiuscula ? ucfirst($coluna->TabelaReferencia) : lcfirst($coluna->TabelaReferencia);
            }

            if ($coluna->TipoPHP == "boolean" && $coluna->ValorPadrao) {
                $coluna->ValorPadrao = (intval($coluna->ValorPadrao)) ? "true" : "false";
            }
            
            $tipo = $coluna->Tipo;
            
            if($tipo == "enum"){
               $tipo.=" [". substr(substr($coluna->TipoColuna, 0,-1), 5)."]" ;
            }

            if ($InserirComentario) {
                $conteudo[] = "\t/**";
                $conteudo[] = "\t*";
                $conteudo[] = "\t* @var {$coluna->TipoPHP}";
                $conteudo[] = "\t* @access protected";
                $conteudo[] = "\t* ";
                $conteudo[] = "\t* @dbcolumn {$coluna->Coluna}";
                $conteudo[] = "\t* @dbtype {$tipo}";
                $conteudo[] = "\t* @dbsize {$coluna->Tamanho}";
                if ($coluna->CasasDecimais) {
                    $conteudo[] = "\t* @dbprecision {$coluna->CasasDecimais}";
                }

                if ($coluna->ChavePrimaria) {
                    $conteudo[] = "\t* @dbprimarykey true";
                }
                if ($coluna->Unico) {
                    $conteudo[] = "\t* @dbunique {$coluna->RestricaoNome}";
                }
                if ($coluna->AutoIncremento) {
                    $conteudo[] = "\t* @dbautoincrement true";
                }
                if ($coluna->SomentePositivo) {
                    $conteudo[] = "\t* @dbunsigned true";
                }
                if ($coluna->PreencherEspacos) {
                    $conteudo[] = "\t* @dbzerofill true";
                }
                if ($coluna->Obrigatorio) {
                    $conteudo[] = "\t* @dbrequired true";
                }
                if ($coluna->ValorPadrao) {
                    $conteudo[] = "\t* @dbdefault {$coluna->ValorPadrao}";
                }
                if ($coluna->ChaveEstrangeira) {
                    $conteudo[] = "\t* @dbforeignkey {$coluna->RestricaoNome}";
                }

                if ($coluna->ChaveEstrangeira) {
                    $conteudo[] = "\t* ";

                    $conteudo[] = "\t* @dbreferencedtable   {$coluna->TabelaReferencia}";
                    $conteudo[] = "\t* @dbreferencedcolumn  {$coluna->ColunaReferencia}";
                }

                $conteudo[] = "\t*/";
            }



            $conteudo[] = "\tprotected \${$coluna->Variavel};";
            $conteudo[] = "\t";
        }
        $conteudo[] = "\t//</editor-fold>";
        unset($coluna);
        //</editor-fold>  
        //<editor-fold  defaultstate="collapsed" desc="Atribuições"> 

        $conteudo[] = "\t//<editor-fold  defaultstate=\"collapsed\" desc=\"Atribuições\">";
        $conteudo[] = "\t";
        foreach ($colunas as $coluna) {
            if ($InserirComentario) {
                $conteudo[] = "\t/**";
                $conteudo[] = "\t*";
                $conteudo[] = "\t* @access public";
                $conteudo[] = "\t* @param {$coluna->TipoPHP} \${$coluna->Variavel}";

                if ($coluna->Tipo == "datetime" || $coluna->Tipo == "timestamp") {
                    $conteudo[] = "\t* @example 00/00/0000 00:00:00";
                } else if ($coluna->Tipo == "date") {
                    $conteudo[] = "\t* @example 00/00/0000";
                } else if ($coluna->Tipo == "time") {
                    $conteudo[] = "\t* @example 00:00:00";
                } else if ($coluna->TipoPHP == "boolean") {
                    $conteudo[] = "\t* @example TRUE|FALSE";
                }

                $conteudo[] = "\t*/";
            }

            $Tipo = (strcasecmp($coluna->TipoPHP, $coluna->Variavel) == 0) ? "{$coluna->TipoPHP} " : "";


            $conteudo[] = "\tpublic function set" . ucfirst($coluna->Variavel) . "({$Tipo}\${$coluna->Variavel}){";
            $conteudo[] = "\t\t\$this->{$coluna->Variavel} = \${$coluna->Variavel};";
            $conteudo[] = "\t}";
            $conteudo[] = "\t";
        }

        $conteudo[] = "\t//</editor-fold>";
        //</editor-fold> 
        //<editor-fold  defaultstate="collapsed" desc="Obtenções"> 

        $conteudo[] = "\t//<editor-fold  defaultstate=\"collapsed\" desc=\"Obtenções\">";
        $conteudo[] = "\t";
        foreach ($colunas as $coluna) {
            if ($InserirComentario) {
                $conteudo[] = "\t/**";
                $conteudo[] = "\t*";
                $conteudo[] = "\t* @access public";
                $conteudo[] = "\t* @return {$coluna->TipoPHP}";
                $conteudo[] = "\t*/";
            }

            //$Tipo = ($Coluna->Variavel == $Coluna->TipoPHP) ? "" : "{$Coluna->TipoPHP} ";

            $conteudo[] = "\tpublic function get" . ucfirst($coluna->Variavel) . "(){";
            $conteudo[] = "\t\treturn \$this->{$coluna->Variavel};";
            $conteudo[] = "\t}";
            $conteudo[] = "\t";
        }

        $conteudo[] = "\t//</editor-fold>";
        //</editor-fold> 
    }

    if ($classe == 2) {
        $conteudo[] = "{$Avanco}abstract class {$nomeClasse} extends \BancoDados {";
        $Parametros = array();
        $Variavel   = lcfirst($Tabela);

        //<editor-fold  defaultstate="collapsed" desc="CRUD">
//        $conteudo[] = "\t//<editor-fold  defaultstate=\"collapsed\" desc=\"CRUD\">";
//        $conteudo[] = "\t";
        //<editor-fold  defaultstate="collapsed" desc="Incluir">
//        if ($InserirComentario) {
//            $conteudo[] = "\t/**";
//            $conteudo[] = "\t* Método para incluir {$Tabela}";
//            $conteudo[] = "\t*";
//            $conteudo[] = "\t* @access public";
//            $conteudo[] = "\t* @param {$Tabela} \${$Variavel}";
//            $conteudo[] = "\t*/";
//        }
//
//
//
//        $conteudo[] = "\t/*public function incluir(\${$Variavel}) {";
//        $conteudo[] = "\t\t\$sql = \"INSERT INTO {$Tabela}";
//        $conteudo[] = "\t\t\t\t(";
//
//        foreach ($colunas as $coluna) {
//            if (!$coluna->AutoIncremento && ($coluna->Tipo !== "timestamp" && $coluna->ValorPadrao !== "CURRENT_TIMESTAMP")) {
//                $Parametros[] = "\t\t\t\t\t\t:{$coluna->Coluna}";
//            }
//        }
//        $conteudo[] = str_replace(":", "", implode(",\n", $Parametros));
//        $conteudo[] = "\t\t\t\t)";
//        $conteudo[] = "\t\t\t\tVALUES";
//        $conteudo[] = "\t\t\t\t(";
//        $conteudo[] = implode(",\n", $Parametros);
//        $conteudo[] = "\t\t\t\t)\";";
//        $conteudo[] = "\t\t";
//        $conteudo[] = "\t\t\$this->setSQL(\$sql);";
//        $conteudo[] = "\t\t\$this->setEntidade(\${$Variavel});";
//        $conteudo[] = "\t\treturn \$this->executar();";
//        $conteudo[] = "\t}*/";
//        $conteudo[] = "\t";


        if ($InserirComentario) {
            $conteudo[] = "/**";
            $conteudo[] = "\t* Método para listar dinamicamente {$Tabela}";
            $conteudo[] = "\t*";
            $conteudo[] = "\t* @access public";
            $conteudo[] = "\t* @param array \$parametros";
            $conteudo[] = "\t* @return array";
            $conteudo[] = "\t*/";
        }


        $colunasSelecao = [];

        $conteudo[] = "\tprotected function listarPor(array \$parametros = []) {";

        //$Conteudo[] = "\t\t\t\t(";
//        $condicoes = [
//            "CodigoCupomDesconto" => "CupomDesconto.CodigoCupomDesconto",
//            "CodigoIdentificacao" => "CupomDesconto.CodigoIdentificacao"
//        ];
//
//        foreach ($parametros as $nome => $valor) {
//            if (!isset($condicoes[$nome])) {
//                unset($parametros[$nome]);
//                continue;
//            }
//            $filtros[] = "{$condicoes[$nome]} =:{$nome}";
//        }

        $colunasCondicao = [];
        
        foreach ($colunas as $coluna) {
            if (!$coluna->AutoIncremento && ($coluna->Tipo !== "timestamp" && $coluna->ValorPadrao !== "CURRENT_TIMESTAMP")) {
                $colunasSelecao[] = $coluna->Coluna;
            }
            if ($coluna->TipoChave == "PRI") {
                $colunasCondicao[] = "\"{$coluna->Coluna}\" => \"{$Tabela}.{$coluna->Coluna}\"";
            }
        }


        $conteudo[] = "\t\$filtros   = [];";
        $conteudo[] = "\t\$condicoes = [";
        $conteudo[] = "\t\t". implode(",\n\t\t", $colunasCondicao);
        $conteudo[] = "\t];";
        $conteudo[] = "\tforeach (\$parametros as \$nome => \$valor) {";
        $conteudo[] = "\tif (!isset(\$condicoes[\$nome])) {";
        $conteudo[] = "\t\t\tunset(\$parametros[\$nome]);";
        $conteudo[] = "\t\t\tcontinue;";
        $conteudo[] = "\t}";
        $conteudo[] = "\t".'$filtros[] = "{$condicoes[$nome]} =:{$nome}";';
        $conteudo[] = "\t}";
        $conteudo[] = "";
        $conteudo[] = "\$sql = \"SELECT  " . array_shift($colunasSelecao);
        $conteudo[] = "\t\t     ,  " . implode("\r\t\t     ,  ", $colunasSelecao);
        $conteudo[] = "\t\t  FROM\t{$Tabela}";
        $conteudo[] = "\t\t WHERE\t" . '" . implode(" AND ", $filtros);';
        //$Conteudo[] = implode(",\n", $Parametros);        
        $conteudo[] = "";
//        $conteudo[] = "\t\t\$this->setSQL(\$sql);";
//        $conteudo[] = "\t\t\$this->setEntidade(\${$Variavel});";
//        $conteudo[] = "\t\t\treturn \$this->getDados();";
        $conteudo[] = "return \$this->getListaDados(\$sql,\$parametros);";
        $conteudo[] = "}";
                
        




        //</editor-fold> 
//        $conteudo[] = "\t//</editor-fold>";
        //</editor-fold>

        unset($Parametros);
    }

    if ($classe == 1) {
        $conteudo[] = "{$Avanco}class {$nomeClasse} extends {$Tabela}{$sufixoClasse[2]} {";
        $Nome       = $Maiuscula ? ucfirst($Tabela) : lcfirst($Tabela);
        $heranca    = array("validar", "antesIncluir", "depoisIncluir", "antesAlterar",
            "depoisAlterar", "antesExcluir", "depoisExcluir");

        $conteudo[] = "\t//<editor-fold  defaultstate=\"collapsed\" desc=\"Métodos de herança\">";

        foreach ($heranca as $funcao) {
            if ($InserirComentario) {
                $conteudo[] = "\t/**";
                $conteudo[] = "\t*";
                $conteudo[] = "\t* @access protected";
                $conteudo[] = "\t* @param {$Tabela} \${$Nome}";
                $conteudo[] = "\t*/";
            }


            $conteudo[] = "\tprotected function {$funcao}(&\${$Nome}) {";
            $conteudo[] = "\t}";
            $conteudo[] = "\t";
        }

        $conteudo[] = "\t//</editor-fold>";

        //</editor-fold>
    }

    $conteudo[] = "}";
    if (!empty($Namespace)) {
        $conteudo[] = "}";
    }

    $conteudo[] = "?>";

    if (file_exists($CaminhoArquivo)) {
        unlink($CaminhoArquivo);
    }

    $ConteudoArquivo = implode("\r", $conteudo);

    if ($ConverterUTF8) {
        $ConteudoArquivo = utf8_encode($ConteudoArquivo);
    }

    //file_put_contents($CaminhoArquivo, trim(str_replace("\t", "    ", implode("\r", $Conteudo))));
    file_put_contents($CaminhoArquivo, $ConteudoArquivo);
    echo json_encode(array("Nome" => $nomeArquivo));
} catch (Exception $exc) {
//    retornoAjax(false , "Ops! Desculpe, falha ao tentar cadastrar o novo" Informe o erro);
}
?>

