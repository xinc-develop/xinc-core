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
 * @link      https://github.com/xinc-develop/xinc-core/
 */
namespace Xinc\Core\Registry;

use Xinc\Core\Traits\Logger;
use Xinc\Core\Validation\Exception\TypeMismatch;

/**
 * Abstract Registry Class to be extended by projects, buildqueue etc.
 */
abstract class RegistryAbstract implements RegistryInterface
{
    use Logger;
    /**
     * @var typeOf The Name of the class this elements should be.
     */
    protected $typeOf = null;

    /**
     * @var array Array of registered elements
     */
    private $registry = array();

    /**
     * @param string $name
     * @param object $object
     *
     * @throws Xinc\Core\Registry\RegistryException
     * @throws Xinc\Core\Validation\Exception\TypeMismatch
     */
    public function register($name, $object)
    {
        if (isset($this->registry[$name])) {
            throw new RegistryException('Object with name "'.$name.'" is already registered');
        }
        if (!is_a($object, $this->typeOf)) {
            throw new TypeMismatch(get_class($object), $this->typeOf);
        }

        $this->registry[$name] = $object;
    }

    /**
     * @param string $name
     *
     * @return object
     *
     * @throws Xinc\Core\Registry\RegistryException
     */
    public function unregister($name)
    {
        if (!isset($this->registry[$name])) {
            throw new Exception('Object with name "'.$name.'" is not registered');
        }

        $object = $this->registry[$name];
        unset($this->registry[$name]);

        return $object;
    }

    /**
     * @param string $name
     *
     * @return object
     *
     * @throws Xinc\Core\Registry\Exception
     */
    public function get($name)
    {
        if (!isset($this->registry[$name])) {
            throw new RegistryException('Object with name "'.$name.'" is not registered');
        }

        return $this->registry[$name];
    }

    /**
     * Check if a given name was registred.
     *
     * @param string $name
     *
     * @return bool
     */
    public function knows($name)
    {
        return isset($this->registry[$name]);
    }

    /**
     * Made a iterator from stored objects.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->registry);
    }
}
