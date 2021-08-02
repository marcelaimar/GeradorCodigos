<?php/*** Marcel Aimar Est�cio* Desenvolvedor Web* * Humberto de Campos, 161 - Sumar�* Presidente Venceslau, CEP 19400-000, Brasil* Telefone: +55(18)997467617  * * Classe Agendamento* Representa objeto de persist�ncia de dados para entidade Agendamento* * @name Agendamento* @access public * @license https://creativecommons.org/licenses/by-nc-nd/4.0/legalcode CC BY-NC-ND* @copyright (c) 2020* @author Marcel Aimar <marcel_aimar@hotmail.com>* * @dbtable     Agendamento* @dbengine    InnoDB* @dbcharset   utf8_general_ci*/class Agendamento {	//<editor-fold  defaultstate="collapsed" desc="Declara��o de vari�veis">		/**	*	* @var integer	* @access protected	* 	* @dbcolumn CodigoAgendamento	* @dbtype bigint	* @dbsize 21	* @dbprimarykey true	* @dbunsigned true	* @dbrequired true	*/	protected $codigoAgendamento;		/**	*	* @var integer	* @access protected	* 	* @dbcolumn CodigoEndereco	* @dbtype bigint	* @dbsize 21	* @dbunsigned true	* @dbrequired true	* @dbforeignkey Endereco_Agendamento	* 	* @dbreferencedtable   Endereco	* @dbreferencedcolumn  CodigoEndereco	*/	protected $codigoEndereco;		/**	*	* @var integer	* @access protected	* 	* @dbcolumn CodigoPessoa	* @dbtype bigint	* @dbsize 21	* @dbunsigned true	* @dbforeignkey Pessoa_Agendamento	* 	* @dbreferencedtable   Pessoa	* @dbreferencedcolumn  CodigoPessoa	*/	protected $codigoPessoa;		/**	*	* @var integer	* @access protected	* 	* @dbcolumn CodigoFormaPagamento	* @dbtype tinyint	* @dbsize 4	* @dbunsigned true	* @dbforeignkey FormaPagamento_Agendamento	* 	* @dbreferencedtable   FormaPagamento	* @dbreferencedcolumn  CodigoFormaPagamento	*/	protected $codigoFormaPagamento;		/**	*	* @var string	* @access protected	* 	* @dbcolumn CodigoIdentificacao	* @dbtype char	* @dbsize 11	* @dbunique TUC_Agendamento_CodigoIdentificacao	* @dbrequired true	*/	protected $codigoIdentificacao;		/**	*	* @var string	* @access protected	* 	* @dbcolumn Observacao	* @dbtype text	* @dbsize 65535	*/	protected $observacao;		/**	*	* @var string	* @access protected	* 	* @dbcolumn DataHora	* @dbtype datetime	* @dbsize 19	* @dbrequired true	*/	protected $dataHora;		/**	*	* @var string	* @access protected	* 	* @dbcolumn DataCriacao	* @dbtype timestamp	* @dbsize 19	* @dbrequired true	* @dbdefault CURRENT_TIMESTAMP	*/	protected $dataCriacao;		/**	*	* @var string	* @access protected	* 	* @dbcolumn DataModificacao	* @dbtype datetime	* @dbsize 19	*/	protected $dataModificacao;		//</editor-fold>	//<editor-fold  defaultstate="collapsed" desc="Atribui��es">		/**	*	* @access public	* @param integer $codigoAgendamento	*/	public function setCodigoAgendamento($codigoAgendamento){		$this->codigoAgendamento = $codigoAgendamento;	}		/**	*	* @access public	* @param integer $codigoEndereco	*/	public function setCodigoEndereco($codigoEndereco){		$this->codigoEndereco = $codigoEndereco;	}		/**	*	* @access public	* @param integer $codigoPessoa	*/	public function setCodigoPessoa($codigoPessoa){		$this->codigoPessoa = $codigoPessoa;	}		/**	*	* @access public	* @param integer $codigoFormaPagamento	*/	public function setCodigoFormaPagamento($codigoFormaPagamento){		$this->codigoFormaPagamento = $codigoFormaPagamento;	}		/**	*	* @access public	* @param string $codigoIdentificacao	*/	public function setCodigoIdentificacao($codigoIdentificacao){		$this->codigoIdentificacao = $codigoIdentificacao;	}		/**	*	* @access public	* @param string $observacao	*/	public function setObservacao($observacao){		$this->observacao = $observacao;	}		/**	*	* @access public	* @param string $dataHora	* @example 00/00/0000 00:00:00	*/	public function setDataHora($dataHora){		$this->dataHora = $dataHora;	}		/**	*	* @access public	* @param string $dataCriacao	* @example 00/00/0000 00:00:00	*/	public function setDataCriacao($dataCriacao){		$this->dataCriacao = $dataCriacao;	}		/**	*	* @access public	* @param string $dataModificacao	* @example 00/00/0000 00:00:00	*/	public function setDataModificacao($dataModificacao){		$this->dataModificacao = $dataModificacao;	}		//</editor-fold>	//<editor-fold  defaultstate="collapsed" desc="Obten��es">		/**	*	* @access public	* @return integer	*/	public function getCodigoAgendamento(){		return $this->codigoAgendamento;	}		/**	*	* @access public	* @return integer	*/	public function getCodigoEndereco(){		return $this->codigoEndereco;	}		/**	*	* @access public	* @return integer	*/	public function getCodigoPessoa(){		return $this->codigoPessoa;	}		/**	*	* @access public	* @return integer	*/	public function getCodigoFormaPagamento(){		return $this->codigoFormaPagamento;	}		/**	*	* @access public	* @return string	*/	public function getCodigoIdentificacao(){		return $this->codigoIdentificacao;	}		/**	*	* @access public	* @return string	*/	public function getObservacao(){		return $this->observacao;	}		/**	*	* @access public	* @return string	*/	public function getDataHora(){		return $this->dataHora;	}		/**	*	* @access public	* @return string	*/	public function getDataCriacao(){		return $this->dataCriacao;	}		/**	*	* @access public	* @return string	*/	public function getDataModificacao(){		return $this->dataModificacao;	}		//</editor-fold>}?>