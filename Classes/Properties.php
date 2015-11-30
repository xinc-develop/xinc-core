<?php
/**
 * Xinc - Continuous Integration.
 * Build Properties carry additional information about a build
 *
 *
 * @author    Arno Schneider <username@example.org>
 * @copyright 2007 Arno Schneider, Barcelona
 * @copyright 2015 Xinc Developers, Leipzig
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
 * @link      https://github.com/xinc-develop
 */

namespace Xinc\Core;

use ArrayAccess;
use Xinc\Core\Exception\Mistake;

class Properties implements ArrayAccess
{   
    /**
     * Associative Array holding the nvp for the build properties
     *
     * @var array
     */
    private $properties = array();

    public function offsetExists ( $offset)
    {
        return array_key_exists($offset, $this->properties);		
	}

    public function offsetGet ( $offset )
    {
		return $this->properties[$offset];
	}

    public function offsetSet ( $offset , $value )
    {
		throw new Mistake("Properties should not be written as arrays!");
	}
	
    public function offsetUnset ( $offset )
    {
        unset($this->properties[$offset]);
	}
    
    /**
     * set a property
     *
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value = null)
    {
		if(is_array($name)) {
			foreach($name as $k => $v) {
				$this->properties[$k] = $v;
			}
		}
		else {
            $this->properties[$name] = $value;
        }
    }
    
    /**
     * Returns the property value of the questioned keyname
     *
     * @param String $name
     * @return mixed String or null if not found
     */
    public function get($name)
    {
        if (isset($this->properties[$name])) {
            return $this->properties[$name];
        } else {
            return null;
        }
    }
    
    /**
     * returns all the properties in an array
     *
     * @return array
     */
    public function getAllProperties()
    {
        return $this->properties;
    }
    /**
     * Parses a string and substitutes ${name} with $value
     * of property
     *
     * @param string $string
     */
    public function parseString($string)
    {
        $string = (string) $string;
        $string = preg_replace_callback("/\\$\{(.*?)\}/", 
            function ($k) { return $this->properties[$k[1]]; }, $string);
        return $string;
    }
}
