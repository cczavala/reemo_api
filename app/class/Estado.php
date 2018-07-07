<?php
/**
* Clase Estado 
*
* @version 1.0.0 Jul-18
*/

class Estado extends Dbconnection
{

	private $_cveEdo;
	private $_errorMsg;

	public function __construct($cveEdo,$dbMx = NULL)
	{
		parent::__construct(NULL,NULL,$dbMx,$cveEdo);
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
		    	throw new Exception($this->getMsgErrorConnection());
		    }
		} catch (Exception $e) {
			error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$this->setErrorMsg($e->getMessage() . "|" . __METHOD__ . "|");
			return false;
		}
	}
}