<?php

try {
    $BancoDados = trim($_POST["BancoDados"]);
    $classe = intval($_POST["Classe"]);
    $Tabela = trim(str_replace(" ", "", $_POST["Tabela"]));
    $Comentario = intval($_POST["Comentario"]);
    $CamposTabela = getConsulta("SHOW FULL COLUMNS FROM {$Tabela}", $BancoDados);

    $CodigoAutoIncremento = str_replace("{NomeTabela}", $Tabela, trim($_POST["Codigo"]));
    $nomeArquivo = "";
    $TiposPHP = array("string", "double", "integer", "boolean");
    $classes = array("Entidade", "Negocio", "Dados", "Estrutura");
    $Prefixo = array("", "Valida", "Banco", "Estrutura");
    $Campos = array();

    $Declaracoes = "";
    $Setters = "";
    $Getters = "";
    $Inicializacoes = "";

    $Campos = getAtributosBancoDados($BancoDados, $Tabela, $_POST["Codigo"]);

    //<editor-fold  defaultstate="collapsed" desc="Recuperando Modelo">
    $Cabecalho = file_get_contents("./Modelo/ModeloCabecalho.txt");
    $Cabecalho = str_replace("\${Ano}", date("Y"), $Cabecalho);


    $Modelo = trim(file_get_contents("./Modelo/Modelo{$classes[$classe]}.txt"));

    $Modelo = str_replace("\${Cabecalho}", $Cabecalho, $Modelo);
    $Modelo = str_replace("\${Nome}", $Tabela, $Modelo);
    $Modelo = str_replace("\${TipoClasse}", $classes[$classe], $Modelo);
    $Modelo = str_replace("\${PrefixoClasse}", $Prefixo[$classe], $Modelo);
    //</editor-fold>
    //<editor-fold  defaultstate="collapsed" desc="Classe de Entidade">    
    if ($classe == 0) {
        $ChavesEstrangeiras = array();
        foreach ($Campos as $Campo) {
            $NomeVariavel = ($Campo->ChaveEstrangeira) ? $Campo->ChaveEstrangeira["Tabela"] : $Campo->Nome;
            $Tipo = ($Campo->ChaveEstrangeira) ? $Campo->ChaveEstrangeira["Tabela"] : $Campo->TipoPHP;
            
            if (!$Campo->ChaveEstrangeira || ($Campo->ChaveEstrangeira && !in_array($Campo->ChaveEstrangeira["Tabela"], $ChavesEstrangeiras))) {
                $ChavesEstrangeiras[] = $Campo->ChaveEstrangeira["Tabela"];


                //<editor-fold  defaultstate="collapsed" desc="Declarações">  
                // print_r($Tipo);


                if ($Comentario) {
                    $Declaracoes .= "\r\t/**"
                            . "\r\t  *"
                            . "\r\t  * @var {$Tipo}"
                            . "\r\t  */";
                }
                $Declaracoes .= "\r\tprotected \${$NomeVariavel};";


                //</editor-fold>  
                //<editor-fold  defaultstate="collapsed" desc="Inicializacões">  
                /* $ValorInicial = $Campo->ValorPadrao;
                  if ($Campo->Tipo == "timestamp" && $ValorInicial == "CURRENT_TIMESTAMP") {
                  $ValorInicial = "date(\"d/m/Y H:i:s\")";
                  } else if ($Campo->Tipo == "bit") {
                  $ValorInicial = ($ValorInicial) ? "TRUE" : "FALSE";
                  } else if (empty($ValorInicial)) {
                  $ValorInicial = "NULL";
                  }
                  $Inicializacoes .= "\r\t\t\$this->{$NomeVariavel} = {$ValorInicial};"; */

                $Inicializacoes .= "\r\t\t\$this->{$NomeVariavel} = NULL;";

                //</editor-fold>  
                //<editor-fold  defaultstate="collapsed" desc="Setters">  

                if ($Comentario) {
                    $Setters .= "\r\t/**"
                            . "\r\t* "
                            . "\r\t* @param {$Tipo} \${$NomeVariavel}"
                            . "\r\t*/";
                }

                $Setters .="\r\tpublic function set{$NomeVariavel}(" . (in_array($Tipo, $TiposPHP) ? "" : "{$Tipo} ") . "\${$NomeVariavel}) {"
                        . "\r\t\t";

                if ($Tipo == "integer") {
                    $Setters .= "\$this->{$NomeVariavel} = is_numeric(\${$NomeVariavel}) ? intval(\${$NomeVariavel}) : \"\";";
                } else if ($Tipo == "double") {
                    $Setters .= "\$this->{$NomeVariavel} = is_numeric(\${$NomeVariavel}) ? floatval(\${$NomeVariavel}) : \"\";";
                } else if ($Tipo == "boolean") {
                    $Setters .= "\$this->{$NomeVariavel} = (\${$NomeVariavel}) ? TRUE : FALSE;";
                } else if ($Tipo == "string") {
                    $Setters .= "\$this->{$NomeVariavel} = trim(\${$NomeVariavel});";
                } else {
                    $Setters .= "\$this->{$NomeVariavel} = \${$NomeVariavel};";
                }
                $Setters .= "\r\t}\n";

                //</editor-fold>  
                //<editor-fold  defaultstate="collapsed" desc="Getters">  
                if ($Comentario) {
                    $Getters.= "\r\t/**"
                            . "\r\t* "
                            . "\r\t* @return {$Tipo}"
                            . "\r\t*/";
                }

                $Getters .= "\r\tpublic function get{$NomeVariavel}() {"
                        . "\r\t\treturn \$this->{$NomeVariavel};"
                        . "\r\t}\n";

                //</editor-fold>  
            }
        }
        $Modelo = str_replace("\${Declaracoes}", $Declaracoes, $Modelo);
        $Modelo = str_replace("\${Inicializacoes}", $Inicializacoes, $Modelo);
        $Modelo = str_replace("\${Setters}", $Setters, $Modelo);
        $Modelo = str_replace("\${Getters}", $Getters, $Modelo);

        $nomeArquivo = $Tabela;
    }
    //</editor-fold>
    //<editor-fold  defaultstate="collapsed" desc="Classe de Negócio">    
    else if ($classe == 1) {
        $nomeArquivo = "Valida{$Tabela}";
        $AoIncluir = "";
        $Validacoes = "";

        foreach (array($Tabela, "Estrutura{$Tabela}") as $Variavel) {
            if ($Comentario) {
                $Declaracoes .= "\r\t/**"
                        . "\r\t  *"
                        . "\r\t  * @var {$Variavel}"
                        . "\r\t  */";
            }
            $Declaracoes .= "\r\tprotected \${$Variavel};";
        }

        if ($Comentario) {
            $Setters .= "\r\t/**"
                    . "\r\t* "
                    . "\r\t* @param {$Tabela} \${$Tabela}"
                    . "\r\t*/";
        }

        $Setters .="\r\tpublic function set{$Tabela}({$Tabela} \${$Tabela}) {"
                . "\r\t\t"
                . "\$this->{$Tabela} = \${$Tabela};"
                . "\r\t\t"
                . "\$this->Estrutura{$Tabela} = new Estrutura{$Tabela}(\${$Tabela});"
                . "\r\t}\n";

        foreach ($Campos as $Campo) {

            if (!$Campo->ChaveEstrangeira) {
                $AoIncluir .= "\n\t\$this->is{$Campo->Nome}(" . (($Campo->Nulo) ? "FALSE" : "TRUE") . ");";


                $Validacoes .= "\r\tpublic function is{$Campo->Nome}(\$Obrigatorio = FALSE) {";
                $Validacoes .= "\r\t\t\${$Campo->Nome} = \$this->{$Tabela}->get{$Campo->Nome}();\n";

                $Tamanho = "\$this->Estrutura{$Tabela}->{$Campo->Nome}()->getTamanho()";

                if ($Campo->TipoPHP == "integer" || $Campo->TipoPHP == "double") {
                    $Validacoes .= "\r\t\tif (is_null(\${$Campo->Nome}) || \${$Campo->Nome} == 0) {"
                            . "\r\t\t\tif(\$Obrigatorio) throw new ExcecaoNegocios(\"Por favor, informe " . strtolower($Campo->Nome) . "\", \"{$Campo->Nome}\");"
                            . "\r\t\t}else if(strlen(\${$Campo->Nome}) > {$Tamanho}){"
                            . "\r\t\t\tthrow new ExcecaoNegocios(\"Por favor, informe " . strtolower($Campo->Nome) . " corretamente\", \"{$Campo->Nome}\");"
                            . "\r\t\t}";
                } else if ($Campo->TipoPHP == "boolean") {
                    $Validacoes .= "\r\t\tif (!is_bool(\${$Campo->Nome})) {"
                            . "\r\t\t\tif(\$Obrigatorio) throw new ExcecaoNegocios(\"Por favor, informe " . strtolower($Campo->Nome) . "\", \"{$Campo->Nome}\");"
                            . "\r\t\t}";
                } else {
                    $Validacoes .= "\r\t\tif (empty(\${$Campo->Nome})) {"
                            . "\r\t\t\tif(\$Obrigatorio) throw new ExcecaoNegocios(\"Por favor, informe " . strtolower($Campo->Nome) . "\", \"{$Campo->Nome}\");"
                            . "\r\t\t}else if(strlen(\${$Campo->Nome}) > {$Tamanho}){"
                            . "\r\t\t\tthrow new ExcecaoNegocios(\"Por favor, informe " . strtolower($Campo->Nome) . " corretamente\", \"{$Campo->Nome}\");"
                            . "\r\t\t}";
                }

                $Validacoes .= "\r\t}\n";
            }
        }


        $Modelo = str_replace("\${Setters}", $Setters, $Modelo);
        $Modelo = str_replace("\${Declaracoes}", $Declaracoes, $Modelo);
        $Modelo = str_replace("\${AoIncluir}", $AoIncluir, $Modelo);
        $Modelo = str_replace("\${Validacoes}", $Validacoes, $Modelo);
    }
    //</editor-fold>    
    //<editor-fold  defaultstate="collapsed" desc="Classe de Dados">    
    else if ($classe == 2) {
        $nomeArquivo = "Banco{$Tabela}";
        $InsertSQL = "";
        $SQLNomes = array();
        $SQLNomesSET = array();
        $SQLCondicao = array();

        //<editor-fold  defaultstate="collapsed" desc="Declarações"> 
        if ($Comentario) {
            $Declaracoes .= "\r\t/**"
                    . "\r\t  *"
                    . "\r\t  * @var Valida{$Tabela}"
                    . "\r\t  */";
        }
        $Declaracoes .= "\r\tprotected \$Valida{$Tabela};";
        //</editor-fold> 
        //<editor-fold  defaultstate="collapsed" desc="SQL"> 


        foreach ($Campos as $Campo) {
            if (!$Campo->AutoIncremento) {
                $SQLNomes[] = ":{$Campo->Nome}";
            }

            if ($Campo->ChavePrimaria) {
                $SQLCondicao[] = "\r\t\t    {$Tabela}.{$Campo->Nome} =:{$Campo->Nome}";
            } else {
                $SQLNomesSET[] = "\r\t\t    {$Campo->Nome} =:{$Campo->Nome}";
            }
        }

        $SQLParametros = implode(",\r\t\t    ", $SQLNomes);
        $SQLNomesCampos = str_replace(":", "", $SQLParametros);

        $InsertSQL = "INSERT INTO {$Tabela}"
                . "\n\t\t(\r\t\t    {$SQLNomesCampos}"
                . "\n\t\t)"
                . "\n\t\tVALUES"
                . "\n\t\t(\r\t\t    {$SQLParametros}"
                . "\n\t\t)";

        $UpdateSQL = "UPDATE"
                . "\r\t\t    {$Tabela}"
                . "\n\t\tSET"
                . implode(",", $SQLNomesSET)
                . "\r\t\tWHERE"
                . implode(" AND ", $SQLCondicao);

        $SQLNomesCampos = array();

        foreach ($SQLNomes as $Nome) {
            $SQLNomesCampos[] = str_replace(":", "{$Tabela}.", $Nome);
        }

        $SQLNomesCampos = implode(",\r\t\t    ", $SQLNomesCampos);

        $SelectSQL = "SELECT"
                . "\n\t\t    {$SQLNomesCampos}"
                . "\n\t\tFROM"
                . "\r\t\t    {$Tabela}";

        $SelectObterSQL = $SelectSQL
                . "\r\t\tWHERE"
                . implode(" AND ", $SQLCondicao);


        $DeleteSQL = "DELETE FROM"
                . "\r\t\t    {$Tabela}"
                . "\r\t\tWHERE"
                . implode(" AND ", $SQLCondicao);

        //</editor-fold> 
        //  $Modelo = str_replace("\${Setters}", $Setters, $Modelo);
        $Modelo = str_replace("\${Declaracoes}", $Declaracoes, $Modelo);
        $Modelo = str_replace("\${InsertSQL}", $InsertSQL, $Modelo);
        $Modelo = str_replace("\${UpdateSQL}", $UpdateSQL, $Modelo);
        $Modelo = str_replace("\${SelectSQL}", $SelectSQL, $Modelo);
        $Modelo = str_replace("\${SelectObterSQL}", $SelectObterSQL, $Modelo);
        $Modelo = str_replace("\${DeleteSQL}", $DeleteSQL, $Modelo);
    }
    //</editor-fold> 
    //<editor-fold  defaultstate="collapsed" desc="Classe de Estrutura">    
    else if ($classe == 3) {
        $nomeArquivo = "Estrutura{$Tabela}";
        $Atributos = "";

        if ($Comentario) {
            $Declaracoes .= "\r\t/**"
                    . "\r\t  *"
                    . "\r\t  * @var {$Tabela}"
                    . "\r\t  */";
        }
        $Declaracoes .= "\r\tprotected \${$Tabela};";

        foreach ($Campos as $Campo) {

            //<editor-fold  defaultstate="collapsed" desc="Declarações">  

            if ($Comentario) {
                $Declaracoes .= "\r\t/**"
                        . "\r\t  *"
                        . "\r\t  * @var BancoDadosEstrutura"
                        . "\r\t  */";
            }
            $Declaracoes .= "\r\tprotected \${$Campo->Nome};";
            //</editor-fold>  
            //<editor-fold  defaultstate="collapsed" desc="Inicializacões"> 
            $Inicializacoes .= "\r\t\t\$this->{$Campo->Nome} = NULL;";
            //</editor-fold>  
            //<editor-fold  defaultstate="collapsed" desc="Atributos">  

            if ($Comentario) {
                $Atributos .= "\r\t/**"
                        . "\r\t* "
                        . "\r\t* @return BancoDadosEstrutura"
                        . "\r\t*/";
            }

            $Atributos .="\r\tpublic function {$Campo->Nome}() {"
                    . "\r\t\tif (is_null(\$this->{$Campo->Nome})) {"
                    . "\r\t\t\$this->{$Campo->Nome} = new BancoDadosEstrutura();"
                    . (($Campo->ChavePrimaria) ? "\r\t\t\$this->{$Campo->Nome}->setChavePrimaria();" : "")
                    . (($Campo->ChaveEstrangeira) ? "\r\t\t\$this->{$Campo->Nome}->setChaveEstrangeira();" : "")
                    . ((!$Campo->Nulo) ? "\r\t\t\$this->{$Campo->Nome}->setObrigatorio();" : "")
                    . "\r\t\t\$this->{$Campo->Nome}->setTamanho({$Campo->Tamanho});"
                    . "\r\t\t\$this->{$Campo->Nome}->setTipo(\"{$Campo->Tipo}\");"
                    . "\r\t\t}"
                    . "\r\t\t\$Valor = " . (($Campo->ChaveEstrangeira) ? "!is_null(\$this->{$Tabela}->get{$Campo->ChaveEstrangeira["Tabela"]}()) ? \$this->{$Tabela}->get{$Campo->ChaveEstrangeira["Tabela"]}()->get{$Campo->ChaveEstrangeira["Coluna"]}() : NULL" : "\$this->{$Tabela}->get{$Campo->Nome}()") . ";"
                    . "\r\t\tif(!is_null(\$Valor)){"
                    . "\r\t\t\t\$this->{$Campo->Nome}->setValor(\$Valor);"
                    . "\r\t\t}"
                    . "\r\t\treturn \$this->{$Campo->Nome};"
                    . "\r\t}";

            // echo $Campo->ChaveEstrangeira;
            //</editor-fold>  

            /*

             *  public function ProdutoID() {
              if (is_null($this->ProdutoID)) {
              $this->ProdutoID = new BancoDadosEstrutura();
              $this->ProdutoID->setChavePrimaria();
              $this->ProdutoID->setObrigatorio();
              $this->ProdutoID->setTamanho(200);
              $this->ProdutoID->setTipo(BancoDadosEstrutura::TipoInteger);
              }

              $this->ProdutoID->setValor($this->Produto->getProdutoID());
              return $this->ProdutoID;
              }
             *              */
        }

        $Modelo = str_replace("\${Declaracoes}", $Declaracoes, $Modelo);
        $Modelo = str_replace("\${Inicializacoes}", $Inicializacoes, $Modelo);
        $Modelo = str_replace("\${Atributos}", $Atributos, $Modelo);
    }
    //
    //</editor-fold>   
    //<editor-fold  defaultstate="collapsed" desc="Criando arquivo PHP">    
    $nomeArquivo = "{$nomeArquivo}.php";
    $CaminhoArquivo = "./Arquivo/{$nomeArquivo}";

    if (file_exists($CaminhoArquivo)) {
        unlink($CaminhoArquivo);
    }

    file_put_contents($CaminhoArquivo, $Modelo);
    echo json_encode(array("Nome" => $nomeArquivo));
    //</editor-fold>    
} catch (Exception $exc) {
//    retornoAjax(false , "Ops! Desculpe, falha ao tentar cadastrar o novo" Informe o erro);
}
?>

