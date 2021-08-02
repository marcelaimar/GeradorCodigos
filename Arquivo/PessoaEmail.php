<?php/*** Marcel Aimar Estácio* Desenvolvedor Web* * Humberto de Campos, 161 - Sumaré* Presidente Venceslau, CEP 19400-000, Brasil* Telefone: +55(18)997467617  * * Classe PessoaEmail* Representa objeto de persistência de dados para entidade PessoaEmail* * @name PessoaEmail* @access public * @license https://creativecommons.org/licenses/by-nc-nd/4.0/legalcode CC BY-NC-ND* @copyright (c) 2020* @author Marcel Aimar <marcel_aimar@hotmail.com>* * @dbtable     PessoaEmail* @dbengine    InnoDB* @dbcharset   utf8_general_ci*/class PessoaEmail {	//<editor-fold  defaultstate="collapsed" desc="Declaração de variáveis">		/**	*	* @var integer	* @access protected	* 	* @dbcolumn CodigoEmail	* @dbtype bigint	* @dbsize 21	* @dbprimarykey true	* @dbunsigned true	* @dbrequired true	* @dbforeignkey Email_PessoaEmail	* 	* @dbreferencedtable   Email	* @dbreferencedcolumn  CodigoEmail	*/	protected $codigoEmail;		/**	*	* @var integer	* @access protected	* 	* @dbcolumn CodigoPessoa	* @dbtype bigint	* @dbsize 21	* @dbprimarykey true	* @dbunsigned true	* @dbrequired true	* @dbforeignkey Pessoa_PessoaEmail	* 	* @dbreferencedtable   Pessoa	* @dbreferencedcolumn  CodigoPessoa	*/	protected $codigoPessoa;		/**	*	* @var string	* @access protected	* 	* @dbcolumn DataCriacao	* @dbtype timestamp	* @dbsize 19	* @dbrequired true	* @dbdefault CURRENT_TIMESTAMP	*/	protected $dataCriacao;		/**	*	* @var boolean	* @access protected	* 	* @dbcolumn Principal	* @dbtype bit	* @dbsize 1	* @dbrequired true	*/	protected $principal;		//</editor-fold>	//<editor-fold  defaultstate="collapsed" desc="Atribuições">		/**	*	* @access public	* @param integer $codigoEmail	*/	public function setCodigoEmail($codigoEmail){		$this->codigoEmail = $codigoEmail;	}		/**	*	* @access public	* @param integer $codigoPessoa	*/	public function setCodigoPessoa($codigoPessoa){		$this->codigoPessoa = $codigoPessoa;	}		/**	*	* @access public	* @param string $dataCriacao	* @example 00/00/0000 00:00:00	*/	public function setDataCriacao($dataCriacao){		$this->dataCriacao = $dataCriacao;	}		/**	*	* @access public	* @param boolean $principal	* @example TRUE|FALSE	*/	public function setPrincipal($principal){		$this->principal = $principal;	}		//</editor-fold>	//<editor-fold  defaultstate="collapsed" desc="Obtenções">		/**	*	* @access public	* @return integer	*/	public function getCodigoEmail(){		return $this->codigoEmail;	}		/**	*	* @access public	* @return integer	*/	public function getCodigoPessoa(){		return $this->codigoPessoa;	}		/**	*	* @access public	* @return string	*/	public function getDataCriacao(){		return $this->dataCriacao;	}		/**	*	* @access public	* @return boolean	*/	public function getPrincipal(){		return $this->principal;	}		//</editor-fold>}?>