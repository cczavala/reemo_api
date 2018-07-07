<?php
/**
* Clase Rastro.php
*
* @version 1.0.0 Jul-18
*/

class Rastro extends Dbconnection
{

	private $_cveEdo;
	private $_errorMsg;
	private $_responseMsg;

	public function __construct($cveEdo)
	{
		parent::__construct(null,null,null,$cveEdo);
		$this->_cveEdo = $cveEdo;
	}

	public function setErrorMsg($value)
	{
		$this->_errorMsg = $value;
	}

	public function getErrorMsg()
	{
		return $this->_errorMsg;
	}

	public function setResponseMsg($value)
	{
		$this->_responseMsg = $value;
	}
	
	public function getResponseMsg()
	{
		return $this->_responseMsg;
	}

	/**
	* Regresa la etiqueta del estatus *
	*
	* @access public
	* @param integer $cveEstatus
	* @return string
	*/
	public function getStatus($cveEstatus)
	{
		switch ($cveEstatus) {
			case 0:
				return "BAJA";
				break;
			case 1:
				return "ACTIVO";
				break;
			case 2:
				return "-";
				break;
		}
	}

	/**
	* Obtiene la información del rastro por su clave *
	*
	* @access public
	* @param string $cveRastro
	* @return array
	*/
	public function getRastroInfo($cveRastro)
	{
		$datos = [];
		try {
			$sql = "SELECT rt.clave,upper(rt.nombre) AS nombre,upper(rt.direccion) AS direccion,rt.localidad,e.edo_corto AS edoCorto,
			          upper(e.estado) AS estado,upper(e.municipio) AS municipio,e.cve_edo,e.cve_mun,rt.estatus 
					FROM rastros_tif rt,estados e 
					WHERE e.cve_edo = rt.cve_edo AND e.cve_mun = rt.cve_mun AND rt.clave = ?";
			$query = $this->executeStmt($this->connectionToSiniiga(),$sql,[$cveRastro]);
			if (!empty( $query )) {
				$row = $query->fetch(PDO::FETCH_ASSOC);
				if (!empty( $row )) {
					$datos = [
						"clave"        => $row['clave'],
						"nombre"       => $row['nombre'],
						"domicilio"    => (($row['domicilio'] == NULL || $row['domicilio'] == "") ? '-' : $row['domicilio']),
						"localidad"    => (($row['localidad'] == NULL || $row['localidad'] == "") ? '-' : $row['localidad']),
						"cveEdo"       => str_pad( $row['cve_edo'],2,'0',STR_PAD_LEFT ),
						"estado"       => $row['estado'],
						"edoCorto"     => $row['edoCorto'],
						"cveMun"       => str_pad( $row['cve_mun'],3,'0',STR_PAD_LEFT ),
						"municipio"    => $row['municipio'],
						"estatus"      => $row['estatus'],
						"calificacion" => ($row['estatus'] == 1 ? "Vigente" : "Baja o suspendido"),
					];
				} else {
					$datos = [
						"clave"        => $cveRastro,
						"nombre"       => '',
						"domicilio"    => '',
						"localidad"    => '',
						"cveEdo"       => '',
						"estado"       => '',
						"edoCorto"     => '',
						"cveMun"       => '',
						"municipio"    => '',
						"estatus"      => 2,
						"calificacion" => 'Rastro no existe',
					];
				}
			} else {
				throw new Exception($this->getMsgErrorConnection());
			}
		} catch (Exception $e) {
			error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$this->setErrorMsg($e->getMessage() . "|" . __METHOD__ . "|");
		}
		return $datos;
	}
}