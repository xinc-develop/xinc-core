<?php
/*
 * Xinc - Continuous Integration.
 * 
 * @author    Arno Schneider <username@example.org>
 * @author    Sebastian Knapp <news@young-workers.de>
 * @copyright 2007 Arno Schneider, Barcelona
 * @copyright 2015-2016 Xinc Development Team, https://github.com/xinc-develop/
 * @license   http://www.gnu.org/copyleft/lgpl.html GNU/LGPL, see license.php
 *            This file is part of Xinc.
 *            Xinc is free software; you can redistribute it and/or modify
 *            it under the terms of the GNU Lesser General Public License as
 *            published by the Free Software Foundation; either version 2.1 of
 *            the License, or (at your option) any later version.
 *
 *            Xinc is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *            GNU Lesser General Public License for more details.
 *
 *            You should have received a copy of the GNU Lesser General Public
 *            License along with Xinc, write to the Free Software Foundation,
 *            Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @homepage  https://github.com/xinc-develop/xinc-core/
 */
namespace Xinc\Core\Plugin;

use Xinc\Core\Traits\Config;

/**
 * Plugin base class
 * 
 * @ingroup config
 */
abstract class Base implements PluginInterface
{
	use Config;
	
	/**
	 * acces to single values from the main configuration
	 * @param $key - configuration value name
	 * @return a configuration value
	 */
	public function getConfigValue($key)
	{
		return $this->config->get($key);
	}
	
	/**
	 * @return plugin name
	 */
    public function getName()
    {
        $class = get_class($this);
        $parts = explode('\\', $class);

        return $parts[count($parts) - 2];
    }
//! @{
    public function getApiModules()
    {
        return array();
    }

    public function getGuiWidgets()
    {
        return array();
    }
//! @}

    abstract public function getTaskDefinitions();

	/**
	 * Checks if task is able to run
	 * @param $msg reference to get a message which explains why the task is not valid
	 * @return boolean
	 */ 
    public function validate(&$msg = null)
    {
        return true;
    }
}
