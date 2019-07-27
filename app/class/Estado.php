<?php
/**
* Clase Estado 
*
* @version 1.0.0 Oct-18
*/

class Estado extends Dbconnection
{

	private $_cveEdo;
	private $_errorMsg;

	public function __construct($cveEdo,$dbMx = null)
	{
		parent::__construct(null,null,$dbMx,$cveEdo);
		if (!is_null( $cveEdo )) {
			$this->_cveEdo = $cveEdo;
		} else {
			$this->_cveEdo = '';
		}
	}

	public function setErrorMsg($value)
	{	
		$this->_errorMsg = $value;
	}

	public function getErrorMsg()
	{
		return $this->_errorMsg;
	}

	/**
	 * Verifica si el municipio del estado existe *
	 *
	 * @access public
	 * @return boolean
	 */
	public function verificaEstadoMunicipio($cveEdo,$cveMun)
	{
		$datos = [];
		try {
			$sql = "SELECT COUNT(*) AS existeMunicipio 
					FROM estados 
					WHERE cve_mun = ? AND cve_edo = ?";
			$query = $this->executeStmt($this->connectionToMx(),$sql,[$cveMun,$cveEdo]);
    		if (!empty( $query )) {
    			$row = $query->fetch(PDO::FETCH_ASSOC);
		    	if (!empty( $row )) {
		    		if ($row['existeMunicipio'] >= 1) {
		    			return true;
		    		} else {
		    			return false;
		    		}
				} else {
					return false;
				}
			} else {
		    	throw new ErrorException($this->getMsgErrorConnection());
		    }
		} catch (ErrorException $e) {
			error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$this->setErrorMsg($e->getMessage() . "|" . __METHOD__ . "|");
			return false;
		}
	}

	/**
	 * Obtiene la ventanilla (centro) SINIIGA de un predio *
	 *
	 * @access public
	 * @param string $cvePredio cadena con el número identificador del predio UPP/PSG/PG.
	 * @return array $datos
	 */
	public function getCentro($cvePredio)
	{	
		try {
			$sql = "SELECT id_centro 
					FROM estados 
					WHERE cve_edo = SUBSTR(?,1,2) AND cve_mun = SUBSTR(?,3,3)
					LIMIT 1";
			$query = $this->executeStmt($this->connectionToSiniiga(),$sql,[$cvePredio,$cvePredio]);
		    if ($query) {
				if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
					return "tarjetas_" . str_pad( $row['id_centro'],3,"0",STR_PAD_LEFT );
				} else {
					return "";
				}
			} else {
				throw new ErrorException($this->getMsgErrorConnection());
			}
		} catch (ErrorException $e) {
			error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			return false;
		}
	}
}