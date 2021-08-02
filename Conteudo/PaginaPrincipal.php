<?php
include_once './Conteudo/Definicoes.php';
include_once './Conteudo/Funcoes.php';

$URL = getURL();
$Pagina = getURL()->Pagina;
$jQueryDados = array(
    "Caminho" => Caminho,
    "Parametros" => $URL->Parametros,
    "Pagina" => $Pagina,
    "PaginaAtual" => $Pagina,
    "Local" => Local
);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="ISO-8859-1">        
        <title>Gerador de Códigos</title>
        <link rel="stylesheet" href="<?php echo CaminhoCSS; ?>" type="text/css" />
        <script language="javascript" src="<?php echo CaminhoJavaScript; ?>"></script>        
    </head>
    <body>  
        <input type="hidden" id="DadosAplicacao" value="<?php echo base64_encode(base64_encode(json_encode($jQueryDados))); ?>" />        
        <iframe id="Download"></iframe>
        <div id="Conexao">
            <form method="post" name="Conexao">
                <p>
                    <label>Banco de Dados:</label>
                    <select name="BancoDados" data-placeholder="Selecione">
                        <option value=""></option>
                        <?php
                        $NaoExibir = array("information_schema", "mysql", "performance_schema", "test");
                        foreach (getConsulta("SHOW DATABASES") as $ListaBanco) {
                            if (!in_array($ListaBanco->Database, $NaoExibir)) {
                                $NovoNome = preg_replace('/([a-z0-9])([A-Z])/', "$1 $2", $ListaBanco->Database);
                                echo "<option value=\"{$ListaBanco->Database}\">{$NovoNome}</option>";
                            }
                        }
                        ?>
                    </select>            
                </p>
                <p>
                    <label>Tabela:</label>
                    <select name="Tabela">
                        <option value=""></option>
                    </select> 
                </p>
                <p>
                    <label>Padrão do Código:</label>
                    <select name="Codigo">
                        <option value="{NomeTabela}ID">{NomeTabela}ID</option>
                        <option value="{NomeTabela}_ID">{NomeTabela}_ID</option>                        
                        <option value="Codigo{NomeTabela}">Codigo{NomeTabela}</option>
                        <option value="Cod{NomeTabela}">Cod{NomeTabela}</option>                        
                        <option value="Codigo_{NomeTabela}">Codigo_{NomeTabela}</option>                        
                        <option value="Cod_{NomeTabela}">Cod_{NomeTabela}</option>                        
                    </select>
                </p>
            </form>
        </div>
        <div id="Menu">
            <nav>
                <ul>
                    <li class="Atual"><a href="principal">Principal</a></li>
                    <li><a href="classes">Classes</a></li>
                    <li><a href="formularios">Formularios</a></li>                    
                    <li><a href="html">HTML p/ Código</a></li>   
                    <li><a href="utilitarios">Utilitários</a></li>   
                </ul>
            </nav>
        </div>
        <div id="Conteudo" class="<?php echo $Pagina; ?>">            
        </div>

        <!-- end navigation -->



        <!--<fieldset>
            <legend>Conexão Banco de Dados</legend>
            
        </fieldset>-->
    </body>
</html>