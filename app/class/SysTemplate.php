<?php
/**
* Clase SysTemplate
*
* @version 1.0.0 Jul-18
*/

class SysTemplate
{

	/**
	* Función para generar una plantilla sin complementos *
	*
	* @return object
	* @access public
	*/
	public static function renderPage($templateName)
	{
		$tpl = new TemplatePower($templateName);
		$tpl->prepare();
		return $tpl;
	}

	/**
	* Función para generar el pie de la página sin el tag section *
	*
	* @return void
	* @access public
	*/
	public static function renderSimpleFooterPage()
	{
		$tpl = new TemplatePower('resources/views/app/footerPage.inc');
		$tpl->prepare();
		$tpl->assign('SYSTEMNAME',SYSTEM_SHORT_NAME);
		$tpl->assign('SYSTEMVERSION',SYSTEM_VERSION);
		$tpl->assign('SYSTEMYEAR',SYSTEM_YEAR);
		$tpl->printToScreen();
	}
}