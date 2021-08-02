<?php

if (!session_id()) {
    session_start();
}

setlocale(LC_CTYPE, "ptb", "BR");
date_default_timezone_set('America/Sao_Paulo');

/**
 * Verifica se o site est� sendo usado localmente
 * @name Local
 * @access public
 * @author Marcel Aimar <marcel_aimar@hotmail.com> 
 * @var bool
 */
define("Local", in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', "::1")));
/**
 * Vers�o atual do sistema
 * @name Versao
 * @access public
 * @author Marcel Aimar <marcel_aimar@hotmail.com> 
 * @var date 
 */
define("Versao", (Local) ? date("Y.m.dH.i.s") : "23-07-2014");

/**
 * Verifica se o site est� em manuten��o
 * @name Manutencao
 * @access public
 * @author Marcel Aimar <marcel_aimar@hotmail.com> 
 * @var bool   
 */
define("Manutencao", FALSE);
/**
 * Caminho completo da aplica��o
 * @name Caminho
 * @access public
 * @author Marcel Aimar <marcel_aimar@hotmail.com> 
 * @var string 
 */
define("Caminho", "http://localhost/meus-plugins/gerador-codigo");

/**
 * Caminho completo do CSS
 * @name CaminhoCSS
 * @access public
 * @author Marcel Aimar <marcel_aimar@hotmail.com> 
 * @var string 
 */
define("CaminhoCSS", (Local ? Caminho . "/Conteudo/CSS" : Caminho . "/Conteudo/CSS"));

/**
 * Caminho completo do JavaScript
 * @name CaminhoJavaScript
 * @access public
 * @author Marcel Aimar <marcel_aimar@hotmail.com> 
 * @var string 
 */
define("CaminhoJavaScript", (Local ? Caminho . "/Conteudo/JavaScript" : Caminho . "/Conteudo/JavaScript"));

/**
 * Caminho completo das imagens
 * @name CaminhoImagem
 * @access public
 * @author Marcel Aimar <marcel_aimar@hotmail.com> 
 * @var string 
 */
define("CaminhoImagem", (Local ? Caminho . "/Imagem" : Caminho . "/Imagem"));
?>