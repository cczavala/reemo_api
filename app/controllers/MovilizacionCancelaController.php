<?php
/**
* Controlador del proceso de cancelación de una movilización * 
*
* @version 1.0.0 May-18
*/

require_once '../init.php';

class MovilizacionCancelaController extends EndPointController
{

    private $_cveEdo;

    public function setCveEdo($value)
    {
        $this->_cveEdo = $value;
    }

	public function index()
	{
		return self::processRequest();
	}

    /**
     * Verifica y valida la información que recibe de la petición para almacenar una solicitud de cancelación *
     *
     * @access public
     * @param array $request
     * @return json
     */
    public function destroyMovilizacion($request)
    {   
        $params = [];
        try {
            if (isset( $request['folio'] )) {
                if (!empty( $request['folio'] )) {
                    if (!ctype_digit( $request['folio'] )) {
                        throw new Exception("El parámetro folio es un número inválido");
                    }
                    if ($request['folio'] <= 0) {
                        throw new Exception("El rango debe ser un número mayor a 0 para el parámetro folio");
                    }
                    $params[] = $request['folio'];
                } else {
                    throw new Exception("El parámetro folio esta vacío o es inválido");
                }
            } else {
                throw new Exception("No se cuenta con el parámetro folio");
            }
            if (isset( $request['motivoCancela'] )) {
                if (!empty( $request['motivoCancela'] )) {
                    if (!ctype_digit( $request['motivoCancela'] )) {
                        throw new Exception("El parámetro motivoCancela es un número inválido");
                    }
                    if ($request['motivoCancela'] <= 0 && $request['motivoCancela'] >= 3) {
                        throw new Exception("El rango debe ser un número mayor a 0 y menor o igual a 3 para el parámetro motivoCancela");
                    }
                    $params[] = $request['motivoCancela'];
                } else {
                    throw new Exception("El parámetro motivoCancela esta vacío o es inválido");
                }
            } else {
                throw new Exception("No se cuenta con el parámetro motivoCancela");
            }
            $objMovilizacion      = new Movilizacion($this->_cveEdo);
            $rsrc['movilizacion'] = $objMovilizacion->cancelaMovilizacion($params);
        } catch (Exception $e) {
            $rsrc['movilizacion'] = [
                "calificacion" => "No es posible almacenar la cancelación",
                "motivo"       => $e->getMessage()
            ];
        }
        return $this->successResponse(200,$rsrc);
    }
}
$objMov = new MovilizacionCancelaController();
$objMov->index();