<?php
/**
* Clase SysAuthentication
*
* @version 1.0.0 Oct-18
*/

class SysAuthentication extends Dbconnection
{
	
	private $_msgError;
	private $_cveEdo;

	public function __construct($cveEdo = null,$dbReemo = null,$dbMx = null,$dbSiniiga = null)
	{
		parent::__construct($dbSiniiga,$dbReemo,$dbMx,$cveEdo);
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

	/**
	 * Valida usuario y token en la BD *
	 *
	 * @access public
	 * @param array $data
	 * @return json
	 */
	public function validateToken($data)
	{
		$response = [];
		$result   = [];
		try {
			$sql = "SELECT u.id,u.usuario,u.nombre,u.id_centro,u.estatus,u.tipo,e.cve_edo 
					FROM usuarios u 
					INNER JOIN estados e ON e.id_centro = u.id_centro 
					WHERE u.id = ? AND e.cve_edo = 21";
			$query = $this->executeStmt($this->connectionToSiniiga(),$sql,[$data['idUsuario']]);
			//$query = $this->executeStmt($this->connectionToSiniiga(),$sql,[2153]);
			if ($query) {
				$row = $query->fetch(PDO::FETCH_ASSOC);
				if (!empty( $row )) {
					if ($row['estatus'] == 1) {
						$result['usuario'] = [
							"id"        => $row['id'],
							"user"      => $row['usuario'],
							"name"      => $row['nombre'],
							"id_centro" => $row['id_centro'],
							"estatus"   => $row['estatus'],
							"tipo"      => $row['tipo'],
							"cveEdo"    => $row['cve_edo'],
							//"cveEdo"    => '21'
						];
						$sql = "SELECT api_key  
								FROM usuarios_ganadera 
								WHERE id_usuario = ?";
						$query = $this->executeStmt($this->connectionToReemo($row['cve_edo']),$sql,[$row['id']]);
						if ($query) {
							$row = $query->fetch(PDO::FETCH_ASSOC);
							if (!empty( $row )) {
								if ($row['api_key'] == $data['token']) {
									$response = [
										"success" => true,
										"msg"     => "Token correcto",
										"result"  => $result
									];
									$access = date("d/m/Y H:i:s");
    								$textoArchivo = "Cliente: $access {$_SERVER['REMOTE_ADDR']} ({$_SERVER['HTTP_USER_AGENT']})\n";
    								file_put_contents("../../logs/LoginLog.log",$textoArchivo,FILE_APPEND | LOCK_EX);
								} else {
									$response = [
										"success" => false,
										"msg"     => "Token expiró o cambio"
									];
								}
							} else {
								$response = [
									"success" => false,
									"msg"     => "Token expiró o cambio"
								];
							}
						} else {
							throw new ErrorException($this->getMsgErrorConnection());
						}
						/*if (true) {
							$response = [
								"success" => true,
								"msg"     => "Token correcto",
								"result"  => $result
							];
							$access = date("d/m/Y H:i:s");
							$textoArchivo = "Cliente: $access {$_SERVER['REMOTE_ADDR']} ({$_SERVER['HTTP_USER_AGENT']})\n";
							file_put_contents("../../logs/LoginLog.log",$textoArchivo,FILE_APPEND | LOCK_EX);
						} else {
							$response = [
								"success" => false,
								"msg"     => "Token expiró o cambio"
							];
						}*/
					} else {
						throw new Exception("Usuario (" . $data['idUsuario'] . ") se encuentra inactivo.");
					}
				} else {
					throw new Exception("Usuario (" . $data['idUsuario'] . ") no existe en su estado.");
				}
			} else {
				throw new ErrorException($this->getMsgErrorConnection());
			}
		} catch (ErrorException $e) {
			error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			$response = [
				"success" => false,
				"msg"     => $e->getMessage()
			];
		} catch (Exception $e) {
			$response = [
				"success" => false,
				"msg"     => $e->getMessage()
			];
		}
		return (object) $response;
	}
}