<?php

defined( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;

class PlgCaptchaSallectaInstallerScript
{
	public function uninstall($parent)
    {
        echo '<p>' . Text::_('PLG_CAPTCHA_SALLECTA_UNINSTALL_TEXT') . '</p>';
    }
	
	/**
     * Метод, который исполняется до install/update/uninstall.
     * @param   object  $type    Тип изменений: install, update или discover_install
     * @param   object  $parent  Класс, который вызывает этом метод.
     * @return  void
     */
	public function preflight($type, $parent)
    {
		return;
    }
	
	/**
     * Метод, который исполняется после install/update/uninstall.
     * @param   object  $type    Тип изменений: install, update или discover_install
     * @param   object  $parent  Класс, который вызывает этом метод.
     *
     * @return  void
     */
	public function postflight($type, $parent)
	{
		if ($type == 'install')
		{
			// Enable plugin
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->update('#__extensions')
			->set('enabled = 1')
			->where('element = '.$db->quote('sallecta'))      // Plugin name
			->where('type = '.$db->quote('plugin'))        // Type
			->where('folder = '.$db->quote('captcha'));    // Group
			
			$db->setQuery($query)->execute();
			echo '<p>' . Text::_('PLG_CAPTCHA_SALLECTA_INSTALL_TEXT') . '</p>';
		}
	}
}
