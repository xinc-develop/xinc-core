<?php
/**
 * Xinc - Continuous Integration.
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
 * 
 * @homepage  https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core\Engine;

use Xinc\Core\Registry\RegistryException;

/**
 * Registry for engines
 */
class EngineRegistry extends \Xinc\Core\Registry\RegistryAbstract
{
    /**
     * @var typeOf The Name of the class this elements should be.
     */
    protected $typeOf = 'Xinc\Core\Engine\EngineInterface';
    
    protected $default;
    
    public function setDefaultEngine($name, $force = false)
    {
	    if(isset($this->default) && $name == $this->default) {
			$this->log->info("$name is already the default engine.");
		} 
	    if(!$force && isset($this->default)) {
			throw new RegistryException("There is already a default engine: {$this->default}.");
		}
		$this->default = $name;
	}
    
    public function getDefaultEngine()
    {
		if(isset($this->default)) {
		    return $this->get($this->default);	
		}
		$iterator = $this->getIterator();
		if($iterator->count()) {
			$this->log->info("using first engine as default.");
		    return $iterator->current();	
		}
		throw new RegistryException("There are no registered engines.");
	}
}
