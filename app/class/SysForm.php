<?php
/**
* Clase SysForm
*
* @version 1.0.0 Jul-18
*/

require_once '../init.php';

class SysForm extends SysCatalogo
{
    
    /**
     * Obtiene los parámetros enviados desde el método GET *
     *
     * @access public
     * @return array 
     */
    public function getValuesGET()
    {
        return $_GET;
    }

    /**
     * Obtiene algún parámetro enviado desde el método GET *
     *
     * @access public
     * @param string $param nombre de la variable enviada por la URL que se desea obtener.
     * @return string
     */
    public function getValueGET($param,$filterType = null)
    {
        if (isset( $_GET[$param] )) {
            $var = null;
            $var = filter_var( trim( $_GET[$param] ),FILTER_SANITIZE_URL );
            $var = filter_var( trim( $_GET[$param] ),FILTER_SANITIZE_SPECIAL_CHARS );
            switch ($filterType) {
                case 'int':
                    $var = filter_var( trim( $_GET[$param] ),FILTER_SANITIZE_NUMBER_INT );
                    $var = intval( $var );
                    break;
                default:
                    $var = filter_var( trim( $_GET[$param] ),FILTER_SANITIZE_STRING );
                    break;
            }
            return $var;
        } else {
            return '';
        }
    }

    /**
     * Obtiene un arreglo con los campos enviados desde el método POST *
     *
     * @access public
     * @return array
     */
    public function getValuesPOST()
    {
        return $_POST;
    }

    /**
     * Obtiene algún parámetro enviado desde el método POST *
     *
     * @access public
     * @param string $param nombre de la variable enviada por un formulario que se desea obtener.
     * @return string
     */
    public function getValuePOST($param,$filterType = null)
    {
        $var = null;
        if (isset( $_POST[$param] )) {
            switch ($filterType) {
                case 'int':
                    $var = filter_var( trim($_POST[$param]),FILTER_SANITIZE_NUMBER_INT );
                    $var = intval( $var );
                    break;
                case 'float':
                    $var = filter_var( trim($_POST[$param]),FILTER_SANITIZE_NUMBER_FLOAT );
                    break;
                default:
                    $var = filter_var( trim($_POST[$param]),FILTER_SANITIZE_STRING );
                    break;
            }
            return $var;
        }
        return '';
    }

    /**
     * Parsea la cadena URL del QUERY_STRING y regresa un arreglo con los datos *
     *
     * @access public
     * @return array $datos
     */
    public function parseString()
    {
        $datos = [];
        if (isset( $_SERVER['QUERY_STRING'] )) {
            if (!empty( $_SERVER['QUERY_STRING'] )) {
                $params = explode( "&",$_SERVER['QUERY_STRING'] );
                $params = implode( "=",$params );
                $params = explode( "=",$params );
                $i = 1;
                foreach ($params as $key => $value) {
                    if ($key%2 == 0) {
                       $datos[$value] = $params[$i];
                    }
                    $i++;
                }
            }
        }
        return $datos;
    }
}