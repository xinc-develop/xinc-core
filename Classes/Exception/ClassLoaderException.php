<?php
/** 
 * Xinc - Continuous Integration.
 *
 * @author     Sebastian Knapp <news@young-workers.de>
 * @copyright  2015 Xinc Development Team, https://github.com/xinc-develop/
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU/LGPL, see license.php
 *             This file is part of Xinc.
 *             Xinc is free software; you can redistribute it and/or modify
 *             it under the terms of the GNU Lesser General Public License as
 *             published by the Free Software Foundation; either version 2.1 of
 *             the License, or (at your option) any later version.
 *
 *             Xinc is distributed in the hope that it will be useful,
 *             but WITHOUT ANY WARRANTY; without even the implied warranty of
 *             MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *             GNU Lesser General Public License for more details.
 *
 *             You should have received a copy of the GNU Lesser General Public
 *             License along with Xinc, write to the Free Software Foundation,
 *             Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * @homepage   https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core\Exception;

use Xinc\Core\Exception;

/**
 * The Exception is used when a PHP class is not loadable.
 * 
 * For example this could happen if a plugin is configured, but the class
 * is not avaiable.
 * @ingroup exceptions
 */
class ClassLoaderException extends Exception
{
	public function __construct($class)
	{
		parent::__construct("Class $class could not be loaded.");
	}
}
