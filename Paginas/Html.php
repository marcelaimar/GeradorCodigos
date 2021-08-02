<form>
    <p>
        <textarea name="ConteudoHTML" placeholder="Cole seu código HTML aqui"></textarea>
    </p>
    <div class="CantoEsquerdo">
        <p>
            <label>
                <input type="checkbox" name="QuebraLinha" checked="checked"  />        
                <span>Adicionar Quebra Linha</span>
            </label>
        </p>        
        <p class="Texto">
            <label>Atribuir a variável:</label>
            <input type="text" name="Variavel" placeholder="Digite um prefixo"  />        
        </p>
        <p>
            <label>
                <input type="radio" name="Aspas" value="Duplas" checked="checked" />        
                <span>Usar aspas duplas</span>
            </label>
        </p>
        <p>
            <label>
                <input type="radio" name="Aspas" value="Simples" />        
                <span>Usar aspas simples</span>
            </label>
        </p>

    </div>
    <div class="CantoEsquerdo">
        <p>
            <label>
                <input type="checkbox" name="VariavelLinhas"  />        
                <span>Usar variável em todas as linhas</span>
            </label>
        </p> 
        <p>
            <label>
                <input type="radio" name="Converter" value="+"  />        
                <span>Converter para JS</span>
            </label>
        </p>
        <p>
            <label>
                <input type="radio" name="Converter" value="." checked="checked"  />        
                <span>Converter para PHP</span>
            </label>
        </p>
        
    </div>
    <div class="CantoEsquerdo Botao">      
        <p>
            <input type="submit" value="Gerar"/>
        </p>
    </div>
    <p>
        <textarea readonly="readonly" name="ConteudoCodigo" placeholder="Seu código será gerado aqui"></textarea>
    </p>
</form>
