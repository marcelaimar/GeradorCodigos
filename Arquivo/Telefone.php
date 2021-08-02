<?php/*** Marcel Aimar Estácio* Desenvolvedor Web* * Humberto de Campos, 161 - Sumaré* Presidente Venceslau, CEP 19400-000, Brasil* Telefone: +55(18)997467617  * * Classe Telefone* Representa objeto de persistência de dados para entidade Telefone* * @name Telefone* @access public * @license https://creativecommons.org/licenses/by-nc-nd/4.0/legalcode CC BY-NC-ND* @copyright (c) 2020* @author Marcel Aimar <marcel_aimar@hotmail.com>* * @dbtable     Telefone* @dbengine    InnoDB* @dbcharset   utf8_general_ci*/class Telefone {	//<editor-fold  defaultstate="collapsed" desc="Declaração de variáveis">		/**	*	* @var integer	* @access protected	* 	* @dbcolumn CodigoTelefone	* @dbtype bigint	* @dbsize 21	* @dbprimarykey true	* @dbautoincrement true	* @dbunsigned true	* @dbrequired true	*/	protected $codigoTelefone;		/**	*	* @var string	* @access protected	* 	* @dbcolumn Numero	* @dbtype varchar	* @dbsize 16	* @dbunique TUC_Telefone_Numero	* @dbrequired true	*/	protected $numero;		/**	*	* @var string	* @access protected	* 	* @dbcolumn Tipo	* @dbtype enum	* @dbsize 8	* @dbrequired true	* @dbdefault Celular	*/	protected $tipo;		/**	*	* @var string	* @access protected	* 	* @dbcolumn DataCriacao	* @dbtype timestamp	* @dbsize 19	* @dbrequired true	* @dbdefault CURRENT_TIMESTAMP	*/	protected $dataCriacao;		//</editor-fold>	//<editor-fold  defaultstate="collapsed" desc="Atribuições">		/**	*	* @access public	* @param integer $codigoTelefone	*/	public function setCodigoTelefone($codigoTelefone){		$this->codigoTelefone = $codigoTelefone;	}		/**	*	* @access public	* @param string $numero	*/	public function setNumero($numero){		$this->numero = $numero;	}		/**	*	* @access public	* @param string $tipo	*/	public function setTipo($tipo){		$this->tipo = $tipo;	}		/**	*	* @access public	* @param string $dataCriacao	* @example 00/00/0000 00:00:00	*/	public function setDataCriacao($dataCriacao){		$this->dataCriacao = $dataCriacao;	}		//</editor-fold>	//<editor-fold  defaultstate="collapsed" desc="Obtenções">		/**	*	* @access public	* @return integer	*/	public function getCodigoTelefone(){		return $this->codigoTelefone;	}		/**	*	* @access public	* @return string	*/	public function getNumero(){		return $this->numero;	}		/**	*	* @access public	* @return string	*/	public function getTipo(){		return $this->tipo;	}		/**	*	* @access public	* @return string	*/	public function getDataCriacao(){		return $this->dataCriacao;	}		//</editor-fold>}?>