<?php
/**
 * Xinc - Continuous Integration.
 * Abstract Registry Class to be extended by projects, buildqueue etc.
 *
 *
 * @author    Arno Schneider <username@example.com>
 * @copyright 2014 Alexander Opitz, Leipzig
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
 * @link      https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core\Registry;

use Xinc\Core\Plugin\PluginRegistry;
use Xinc\Core\Traits\Logger;

class Registry implements RegistryInterface
{
	use Logger;
	
    private $pluginRegistry;
    
    public function __construct()
    {
	    $this->pluginRegistry = new PluginRegistry(); 	
	}
	
    public function registerPluginClass($class)
    {
	     	
	}
}
