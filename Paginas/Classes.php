<form>
    <div class="CantoEsquerdo">
         <p class="Texto">
            <label>Namespace:</label><br />
            <input type="text" name="Namespace" placeholder="Namespace (opcional)"  />        
        </p>
        <p>
            <label>
                <input type="radio" name="Classe" checked="checked" value="0"  />        
                <span>Classe de Entidades</span>
            </label>
        </p>
        <p>
            <label>
                <input type="radio" name="Classe" value="1"  />        
                <span>Classe de Neg�cios</span>
            </label>
        </p>
        <p>
            <label>
                <input type="radio" name="Classe" value="2"  />        
                <span>Classe de Dados</span>
            </label>
        </p>      
        
        <?php
//        $t = 121061;
//        echo gettype($t);
        
        ?>
<!--        <p>
            <label>
                <input type="radio" name="Classe" value="3"  />        
                <span>Classe de Estrutura</span>
            </label>
        </p>-->
    </div>
    <div class="CantoEsquerdo">
        <!--<p>
            <label>Nomeclatura:</label>
            <input type="text" name="Nomeclatura" alt="" />
        </p>
        <p>
            <label>
                <input type="radio" name="PosicaoNomeclatura" value="Prefixo" checked="checked"  />        
                <span>Prefixo</span>
            </label>
             <label>
                <input type="radio" name="PosicaoNomeclatura" value="Sufixo"  />        
                <span>Sufixo</span>
            </label>
        </p>-->
        <p>
            <label>
                <input type="checkbox" name="ChaveEstrangeiraObjeto"  />        
                <span>Chave estrangeira � um objeto</span>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="Comentario" checked="checked"  />        
                <span>Incluir coment�rios</span>
            </label>
        </p>
         <p>
            <label>
                <input type="checkbox" name="ConverterUTF8"  />        
                <span>UTF-8</span>
            </label>
        </p>
        <p>
            <label>
                <input type="radio" name="Maiuscula" value="0" checked="checked" />        
                <span>Come�ar vari�veis com min�sculas</span>
            </label>
        </p>
        <p>
            <label>
                <input type="radio" name="Maiuscula" value="1" />        
                <span>Come�ar vari�veis com mai�sculas</span>
            </label>
        </p>
        <p>
            <input type="submit" value="Gerar"/>
        </p>
    </div>



</form>