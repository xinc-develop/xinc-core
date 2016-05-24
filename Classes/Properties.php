<?php
/*
 * Xinc - Continuous Integration.
 * Build Properties carry additional information about a build.
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
 *
 * @link  @rel team https://github.com/xinc-develop
 */
namespace Xinc\Core;

use ArrayAccess;
use Xinc\Core\Exception\Mistake;

/**
 * A simple key value store which allows callbacks for values.
 */
class Properties implements ArrayAccess
{
    private $properties = array();

    private $dynamic = array();

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new Mistake('Properties should not be written as arrays!');
    }

    public function offsetUnset($offset)
    {
        throw new Mistake('Properties should not be unset as arrays!');
    }

    public function has($offset)
    {
        return array_key_exists($offset, $this->properties) ||
            array_key_exists($offset, $this->dynamic);
    }

    /**
     * set a property.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->set($k, $v);
            }
        } else {
            if(is_callable($value)) {
                $this->dynamic[$name] = $value;
            }
            else {
                $this->properties[$name] = $value;
            }
        }
    }

    /**
     * Returns the property value of the questioned keyname.
     *
     * @param string $name
     *
     * @return mixed String or null if not found
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        } elseif (array_key_exists($name, $this->dynamic)) {
            return $this->dynamic[$name]();
        }
        else {
            return;
        }
    }

    /**
     * returns all the properties in an array.
     *
     * @return array
     */
    public function getAllProperties()
    {
        $props = array();
        foreach($this->dynamic as $k => $c) {
            $props[$k] = $c();
        }
        return array_replace($props,$this->properties);

    }
    /**
     * Parses a string and substitutes ${name} with $value
     * of property.
     *
     * @param string $string
     */
    public function parseString($string)
    {
        $string = (string) $string;
        $string = preg_replace_callback("/\\$\{(.*?)\}/",
            function ($k) { return $this->get($k[1]); }, $string);

        return $string;
    }
}
