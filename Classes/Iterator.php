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
 * @link      https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core;

use Xinc\Core\Validation\Exception\TypeMismatch;

/**
 * Iterator over an array of elements
 */
class Iterator extends \ArrayIterator
{
    /**
     * @var typeOf The Name of the class this elements should be.
     */
    protected $typeOf = null;

    /**
     * @throws Xinc::Core::Validation::Exception::TypeMismatch
     */
    public function __construct($array = array())
    {
        $this->testValues($array);
        parent::__construct($array);
    }
    /**
     * @throws Xinc::Core::Validation::Exception::TypeMismatch
     */
    public function append($value)
    {
        $this->testValue($value);
        parent::append($value);
    }
    /**
     * @throws Xinc::Core::Validation::Exception::TypeMismatch
     */
    public function offsetSet($index, $value)
    {
        $this->testValue($value);
        parent::offsetSet($index, $value);
    }

    /**
     * @throws Xinc::Core::Validation::Exception::TypeMismatch
     */
    public function testValues($array)
    {
        foreach ($array as $value) {
            $this->testValue($value);
        }
    }

    /**
     * @throws Xinc::Core::Validation::Exception::TypeMismatch
     */
    public function testValue($value)
    {
        if (!is_a($value, $this->typeOf)) {
            throw new TypeMismatch(get_class($value), $this->typeOf);
        }
    }
    
    /**
     * @deprecated
     * @throws Xinc::Core::Validation::Exception::TypeMismatch
     */
    public function add($item)
    {
        $this->append($item);
    }
    
    /**
     * @deprecated
     */
    public function hasNext()
    {
        return $this->valid();
    }
}
