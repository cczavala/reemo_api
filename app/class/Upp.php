<?php
/**
* Clase Upp
*
* @version 1.0.0 Jul-18
*/
Class Upp extends Dbconnection
{
	
	private $_cveEdo;
    public $fillable = [
    	'origen',
    	'destino'
    ];

	public function __construct($cveEdo,$dbSiniiga = null,$dbMx = null)
	{
		parent::__construct($dbSiniiga,null,$dbMx,$cveEdo);
		$this->_cveEdo = $cveEdo;
	}

	/**
     * Obtiene los datos de un predio UPP *
     *
     * @access public
     * @param string $cvePredio
     * @return array $rsrc
     */
    public function getUppInfo($cvePredio)
    {   
        try {
        	$sql = "SELECT IF(p.tipo_per = 2,p.razon_soc,CONCAT(p.nombre,' ',p.paterno,' ',p.materno)) AS propietario,du.estatus,
        			  (SELECT id_centro FROM centros c WHERE c.cve_edo = SUBSTR(?,1,2) AND (c.tipo_centro = 1 OR c.tipo_centro = 5) 
        			  AND c.dep_aretes <> '' LIMIT 1) AS id_centro 
					FROM productores p, datos_upp du 
					WHERE du.id_upp = ? AND du.id_upp = p.id_upp ";
			$query = $this->executeStmt($this->connectionToSiniiga(),$sql,[$cvePredio,$cvePredio]);
			if ($query) {
				$row = $query->fetch(PDO::FETCH_ASSOC);
				if (!empty( $row )) {
					$rsrc = [
		    			"predio"       => $cvePredio,
		    			"propietario"  => $row['propietario'],
		    			"estatus"      => $row['estatus'],
		    			"calificacion" => ($row['estatus'] == 1 ? "Vigente" : "Baja o suspendido"),
		    			"id_centro"    => $row['id_centro']
		    		];
				} else {
					$rsrc = [
		    			"predio"       => $cvePredio,
		    			"propietario"  => '',
		    			"estatus"      => 2,
		    			"calificacion" => "Clave predio UPP no existe"
		    		];
				}
			} else {
				throw new Exception($this->getMsgErrorConnection());
			}
        	return $rsrc;
        } catch (Exception $e) {
            error_log("Error Runtime-API(REEMO_" . __METHOD__ . "): " . $e->getMessage() . " en " . __FILE__);
			return false;
        }
    }
}