<?php
/**
* Clase SysTemplate
*
* @version 1.0.0 Oct-18
*/

class SysTemplate
{

	/**
	 * Genera la plantilla sin complementos *
	 *
	 * @access public
	 * @param string $templateName
	 * @return object
	 */
	public static function renderPage($templateName)
	{
		$tpl = new TemplatePower($templateName);
		$tpl->prepare();
		return $tpl;
	}

	/**
	 * Genera el pie de página sin el tag section *
	 *
	 * @access public
	 * @return void
	 */
	public static function renderSimpleFooterPage()
	{
		$tpl = new TemplatePower('resources/views/app/footerPage.tpl');
		$tpl->prepare();
		$tpl->assign('SYSTEMNAME',SYSTEM_SHORT_NAME);
		$tpl->assign('SYSTEMVERSION',SYSTEM_VERSION);
		$tpl->assign('SYSTEMYEAR',SYSTEM_YEAR);
		$tpl->printToScreen();
	}
}