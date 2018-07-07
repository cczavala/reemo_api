<?php
/**
* Clase SysLog
*
* @version 1.0.0 Jul-18
*/

require_once '../init.php';

class SysLog extends Dbconnection
{

	private $_cveEdo;
	private $_errorMsg;
	private $_responseMsg;

	public function __construct($cveEdo,$dbSiniiga = NULL,$dbReemo = NULL,$dbMx = NULL)
	{
		parent::__construct($dbSiniiga,$dbReemo,$dbMx,$cveEdo);
		$this->_cveEdo = $cveEdo;
	}

	public function setErrorMsg($valor)
	{
		$this->_errorMsg = $valor;
	}

	public function getErrorMsg()
	{
		return $this->_errorMsg;
	}

	public function setResponseMsg($valor)
	{
		$this->_responseMsg = $valor;
	}

	public function getResponseMsg()
	{
		return $this->_responseMsg;
	}

	/**
	* Procesa el log de las validaciones de los identificadores en una movilización *
	*
	* @param array $dataMov arreglo con los datos de la pre movilización.
	* @param array $dataIdentificadores arreglo con los datos de los identificadores y los mensajes que resultaron.
	* @return void
	*/
	public function procesaLogValidacionesIdentificadores($dataMov,$dataIdentificadores)
	{
		$datos = [];
		try {
			// Se inserta el registro de la pre movilización *
			$query = "INSERT INTO log_validaciones (%s) 
					  VALUES (%s) ";
			$fields = array_keys( $dataMov );
			$values = array_values( $dataMov );
			foreach ($values as $key => $val) {
				$typeVal = explode( "|",$val );
				switch ($typeVal[0]) {
					case 's':
						$values[$key] = "'" . $typeVal[1] . "'";
						break;
					default:
						$values[$key] = $typeVal[1];
						break;
				}
    		}
    		$fields = @join( ',',$fields );
			$values = @join( ', ',$values );
			$query  = sprintf( $query,$fields,$values );
    		$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,[$values]);
			if ($query) {
				$idValidacion = $stmt->insert_id;
			}
		    // Se inserta los identificadores de la pre verificacion *
		    foreach ($dataIdentificadores as $item) {
		    	// Se agrega el identificador *
		    	$itemArete = [
		    		"id_validacion" => "i|" . $idValidacion,
		    		"no_arete"      => "s|" . $item['numArete']
		    	];
		    	$query = "INSERT INTO log_validaciones_aretes (%s) VALUES (%s)";
				$fields = array_keys( $itemArete );
				$values = array_values( $itemArete );
				foreach ($values as $key => $val) {
					$typeVal = explode( "|",$val );
					switch ($typeVal[0]) {
						case 's':
							$values[$key] = "'" . $typeVal[1] . "'";
							break;
						default:
							$values[$key] = $typeVal[1];
							break;
					}
        		}
        		$fields = @join( ',',$fields );
				$values = @join( ', ',$values );
				$query  = sprintf( $query,$fields,$values );
	    		$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,[$values]);
				if ($query) {
					$idValidacionIdentificador = $stmt->insert_id;
				}
			    // Se agregan los mensajes por cada identificador
			    if (!empty( $item['mensajes'] )) {
			    	$dataMsgs = [];
				    foreach ($item['mensajes'] as $itemMsg) {
				    	$itemMsg = [
				    		"id_validacion_arete" => "i|" . $idValidacionIdentificador,
				    		"no_mensaje"          => "i|" . $itemMsg['numAlerta'],
				    		"semaforo"            => "s|" . $itemMsg['semaforo']
				    	];
				    	$dataMsgs[] = $itemMsg;
				    }
				    if (!empty( $itemMsg )) {
					    $query     = "INSERT INTO log_validaciones_aretes_msgs (%s) 
					    			  VALUES %s";
						$fields    = array_keys( $itemMsg );
						$values    = array_values( $dataMsgs );
						$valuesArr = [];
						foreach ($values as $val) {
							$valuesVal = [];
							foreach ($val as $llave => $itemVal) {
								$typeVal = explode( "|",$itemVal );
								switch ($typeVal[0]) {
									case 's':
										$valuesVal[$llave] = "'" . $typeVal[1] . "'";
										break;
									default:
										$valuesVal[$llave] = $typeVal[1];
										break;
								}
							}
							$valuesArr[] = "(" . implode( ",",$valuesVal ) . ")";
		        		}
		        		$fields = @join( ',',$fields );
						$values = @join( ', ',$valuesArr );
						$query  = sprintf( $query,$fields,$values );
			    		$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,[$values]);
					}
				}
		    }
		} catch (Exception $e) {
			error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$this->setErrorMsg($e->getMessage() . "|" . __METHOD__);
		}
	}

	/**
	* Procesa el log de cuarentenas de los identificadores *
	*
	* @param array $dataMov arreglo con los datos de la cuarentena.
	* @param array $dataIdentificadores arreglo con los datos de los identificadores cuarentenados.
	* @param string $idUppOrigen cadena con el número identificador de una UPP/PSG/PG origen.
	* @return void
	*/
	public function procesaLogCuarentena($dataMov,$dataIdentificadores,$idUppOrigen)
	{
		try {
			// Se inserta el registro de la cuarentena *
			$query = "INSERT INTO log_cuarentenas (%s) 
					  VALUES (%s) ";
			$fields = array_keys( $dataMov );
			$values = array_values( $dataMov );
			foreach ($values as $key => $val) {
				$typeVal = explode( "|",$val );
				switch ($typeVal[0]) {
					case 's':
						$values[$key] = "'" . $typeVal[1] . "'";
						break;
					default:
						$values[$key] = $typeVal[1];
						break;
				}
    		}
    		$fields = @join( ',',$fields );
			$values = @join( ', ',$values );
			$query = sprintf( $query,$fields,$values );
		    $idCuarentena = $stmt->insert_id;
		    // Se insertan los identificadores cuarentenados *
		    $data = [];
		    foreach ($dataIdentificadores as $item) {
		    	$item = [
		    		"id_cuarentena" => "i|" . $idCuarentena,
		    		"no_arete"      => "s|" . $item['numArete'],
		    		"id_upp"        => "s|" . $item['predioArete'],
		    	];
		    	$data[] = $item;
		    }
		    $query = "INSERT INTO log_cuarentenas_aretes (%s) 
		    		  VALUES %s";
			$fields = array_keys( $item );
			$values = array_values( $data );
			$valuesArr = [];
			foreach ($values as $val) {
				$valuesVal = [];
				foreach ($val as $llave => $itemVal) {
					$typeVal = explode( "|",$itemVal );
					switch ($typeVal[0]) {
						case 's':
							$valuesVal[$llave] = "'" . $typeVal[1] . "'";
							break;
						default:
							$valuesVal[$llave] = $typeVal[1];
							break;
					}
				}
				$valuesArr[] = "(" . implode( ",",$valuesVal ) . ")";
    		}
    		$fields = @join( ',',$fields );
			$values = @join( ', ',$valuesArr );
			$query = sprintf( $query,$fields,$values );
    		$query = $this->executeStmt($this->connectionToReemo($this->_cveEdo),$sql,[$values]);
		} catch (Exception $e) {
			error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$this->setErrorMsg($e->getMessage() . "|" . __METHOD__);
		}
	}
}