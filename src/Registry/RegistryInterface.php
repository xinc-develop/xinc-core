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

use Xinc\Core\Logger\UseLoggerInterface;

/**
 * Xinc Registry Interface.
 *
 * @ingroup interfaces
 * @ingroup registry
 */
interface RegistryInterface extends UseLoggerInterface
{
    public function register($name, $object);

    /**
     * generates an array of all configured projects
     * Unregisters an Object.
     *
     * @param string $name
     *
     * @return object the unregistered Object
     *
     * @throws Xinc\Core\Registry\RegistryException
     */
    public function unregister($name);

    /**
     * Returns the registered object.
     *
     * @param string $name
     *
     * @return object
     *
     * @throws Xinc\Core\Registry\Exception
     */
    public function get($name);

    /**
     * Is the name registered.
     *
     * @param string $name
     *
     * @return bool
     */
    public function knows($name);
}
