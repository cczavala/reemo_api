<?php
/**
* Clase Dbconnection gestiona las conexiones a las Base de Datos.
*
* @version 1.0.0 Oct-18
*/

class Dbconnection extends SysForm
{

	private   $_errorMsg;
	private   $_errorCode;
	private   $_cveEdo;
	protected $dbSiniiga;
	protected $dbReemo;
	protected $dbMx;
	
	public function __construct($dbSiniiga = null,$dbReemo = null,$dbMx = null,$cveEdo = null)
	{
		if (is_object( $dbSiniiga )) {
			$this->dbSiniiga = $dbSiniiga;
		}
		if (is_object( $dbReemo )) {
			$this->dbReemo = $dbReemo;
		}
		if (is_object( $dbMx )) {
			$this->dbMx = $dbMx;
		}
		if (!is_null( $cveEdo )) {
			$this->_cveEdo = $cveEdo;
		}
	}

	/**
	 * Coloca mensaje de error al realizar una conexión *
	 *
	 * @access public
	 * @param string $value
	 * @return void
	 */	
	public function setMsgErrorConnection($value)
	{
		$this->_errorMsg = $value;
	}

	/**
	 * Regresa mensaje de error al realizar una conexión *
	 *	
	 * @access public
	 * @return string
	 */	
	public function getMsgErrorConnection()
	{
		return $this->_errorMsg;
	}

	/**
	 * Coloca código de error al realizar una petición a la base de datos *
	 *
	 * @access public
	 * @param $value valor numérico con el código de error.
	 * @return void
	 */	
	public function setErrorCode($value)
	{
		$this->_errorCode = $value;
	}

	/**
	 * Regresa código de error *
	 *	
	 * @access public
	 * @return integer
	 */	
	public function getErrorCode()
	{
		return $this->_errorCode;
	}

	/**
	 * Asigna conexión REEMO *
	 *	
	 * @access public
	 * @param object $value
	 * @return void
	 */	
	public function setReemoConnection($value)
	{
		$this->dbReemo = $value;
	}

	/**
	 * Regresa conexión REEMO *
	 *	
	 * @access public
	 * @return object
	 */	
	public function getReemoConnection()
	{
		return $this->dbReemo;
	}

	/**
	 * Asigna conexión SINIIGA *
	 *	
	 * @access public
	 * @param object $value
	 * @return void
	 */	
	public function setSiniigaConnection($value)
	{
		$this->dbSiniiga = $value;
	}

	/**
	 * Regresa conexión SINIIGA *
	 *	
	 * @access public
	 * @return object
	 */	
	public function getSiniigaConnection()
	{
		return $this->dbSiniiga;
	}

	/**
	 * Asigna conexión MX *
	 *	
	 * @access public
	 * @param object $value
	 * @return void
	 */	
	public function setMxConnection($value)
	{
		$this->dbMx = $value;
	}
	
	/**
	 * Regresa conexión MX *
	 *	
	 * @access public
	 * @return object
	 */	
	public function getMxConnection()
	{
		return $this->dbMx;
	}

	/**
	 * Conecta a base de datos REEMO *
	 *
	 * @access public
	 * @param string $cveEdo cadena con la clave del estado.
	 * @return object $db
	 */
	public function connectionToReemo($cveEdo)
	{
		$db = null;
		try {
			$db = $this->getReemoConnection();
			if (!is_object( $db )) {
				if (!is_null( $cveEdo )) {
					if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
						$dsn = "mysql:host=" . DB_HOST_REEMO . ";dbname=" . DB_NAME_REEMO . $cveEdo . ";charset=UTF8";
				    	$db = new PDO($dsn,DB_USER_REEMO,DB_PASS_REEMO,[PDO::ATTR_PERSISTENT => true]);
				    } else {
				    	$dsn = "mysql:host=" . DB_HOST_REEMO . ";dbname=" . DB_NAME_REEMO . $cveEdo;
				    	$db = new PDO($dsn,DB_USER_REEMO,DB_PASS_REEMO,[
				    		PDO::ATTR_PERSISTENT         => true,
				    		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]);
				    }
			    	if (!$db) {
			    		throw new PDOException(" 0x02 Connection to REEMO cannot established. ");
			    	}
			    	$this->setReemoConnection($db);
			    	//if (DEBUG_MODE) echo "NUEVA CONEXIÓN REEMO Objeto que invoca " . get_class($this) . "<br>";
			    	return $db;
			    } else {
			    	throw new PDOException(" 0x01 Invalid cveEdo code. ");
			    }
			} else {
				//if (DEBUG_MODE) echo "CONECTO REEMO CON Objeto que invoca " . get_class($this) . "<br>";
				$this->setReemoConnection($db);
				return $db;
			}
		} catch (PDOException $e) {
		    $message = "[" . __METHOD__ . "] [PDO] " . $e->getMessage() . "<br>";
		    $this->setMsgErrorConnection($message);
            error_log( $message );
		}
	}

	/**
	 * Conecta a base de datos SINIIGA *
	 *
	 * @access public
	 * @return object $db
	 */
	public function connectionToSiniiga()
	{
		$db = null;
		try {
			$db = $this->getSiniigaConnection();
			if (!is_object( $db )) {
				if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
					$dsn = "mysql:host=" . DB_HOST_PGN . ";dbname=" . DB_NAME_PGN . ";charset=UTF8";
			    	$db = new PDO($dsn,DB_USER_PGN,DB_PASS_PGN,[PDO::ATTR_PERSISTENT => true]);
			    } else {
			    	$dsn = "mysql:host=" . DB_HOST_PGN . ";dbname=" . DB_NAME_PGN;
			    	$db = new PDO($dsn,DB_USER_PGN,DB_PASS_PGN,[
			    		PDO::ATTR_PERSISTENT         => true,
			    		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]);
			    }
		    	if (!$db) {
		    		throw new PDOException(" 0x01 Connection to SINIIGA cannot established. ");
		    	}
		    	$this->setSiniigaConnection($db);
		    	//if (DEBUG_MODE) echo "NUEVA CONEXIÓN SINIIGA Objeto que invoca " . get_class($this) . "<br>";
		    	return $db;
		    } else {
		    	$this->setSiniigaConnection($db);
		    	//if (DEBUG_MODE) echo "CONECTO SINIIGA CON Objeto que invoca " . get_class($this) . "<br>";
		    	return $db;
		    }
		} catch (PDOException $e) {
		    $message = "[" . __METHOD__ . "] [PDO] " . $e->getMessage() . "<br>";
		    $this->setMsgErrorConnection($message);
            error_log( $message );
		}
	}

	/**
	 * Conecta a base de datos MX *
	 *	
	 * @access public
	 * @return object $db
	 */
	public function connectionToMx()
	{
		$db = null;
		try {
			$db = $this->getMxConnection();
			if (!is_object( $db )) {
				if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
					$dsn = "mysql:host=" . DB_HOST_REEMOMX . ";dbname=" . DB_NAME_REEMOMX . ";charset=UTF8";
			    	$db = new PDO($dsn,DB_USER_REEMOMX,DB_PASS_REEMOMX,[PDO::ATTR_PERSISTENT => true]);
			    } else {
			    	$dsn = "mysql:host=" . DB_HOST_REEMOMX . ";dbname=" . DB_NAME_REEMOMX;
			    	$db = new PDO($dsn,DB_USER_REEMOMX,DB_PASS_REEMOMX,[
			    		PDO::ATTR_PERSISTENT         => true,
			    		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"]);
			    }
		    	if (!$db) {
		    		throw new PDOException(" 0x01 Connection to MX cannot established. ");
		    	}
		    	$this->setMxConnection($db);
		    	//if (DEBUG_MODE) echo "NUEVA CONEXIÓN MX Objeto que invoca " . get_class($this) . "<br>";
		    	return $db;
		    } else {
		    	$this->setMxConnection($db);
		    	//if (DEBUG_MODE) echo "CONECTO MX CON Objeto que invoca " . get_class($this) . "<br>";
		    	return $db;
		    }
		} catch (PDOException $e) {
		    $message = "[" . __METHOD__ . "] [PDO] " . $e->getMessage() . "<br>";
		    $this->setMsgErrorConnection($message);
            error_log( $message );
		}
	}

	/**
	 * Ejecuta una sentencia preparada PDO *
	 *	
	 * @access public
	 * @param object $dbConnection
	 * @param string $sql
	 * @param array $params
	 * @return object PDO $query
	 */
	public function executeStmt($dbConnection = null,$sql = null,$params = [])
	{
		try {
			if (!is_null( $dbConnection ) || !is_object( $dbConnection )) { 
				if (!is_null( $sql )) {
					$query = $dbConnection->prepare($sql);
					if (!$query) {
						throw new PDOException("[0x03] Failed prepare query sentence");
					}
					if (!empty( $params )) {
						$res = $query->execute($params);
						if (!$res) {
							throw new PDOException("[0x04] Failed execute query sentence");
						}
					} else {
						$res = $query->execute();
						if (!$res) {
							throw new PDOException("[0x05] Failed execute query sentence");
						}
					}
					return $query;
				} else {
					throw new PDOException("[0x02] Invalid or null SQL sentence");
				}
			} else {
				throw new PDOException("[0x01] Invalid DB connection");
			}
		} catch (PDOException $e) {
			$message = "[" . __METHOD__ . "][PDO]" . $e->getMessage() . " [" . $query->errorInfo()[0] . "]" . $query->errorInfo()[2];
            $this->setMsgErrorConnection($message);
            error_log( $message );
            $this->setErrorCode($query->errorInfo()[0]);
            return false;
		}
	}
}