<?php
/**
 * Xinc - Continuous Integration.
 *
 * @author    Arno Schneider
 * @author    Alexander Opitz
 * @author    Sebastian Knapp
 * @copyright 2014 Alexander Opitz, Leipzig
 * @copyright 2015 Xinc Development Team, https://github.com/xinc-develop/
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

/** 
 * A Singleton Pattern Implementation.
 */
namespace Xinc\Core;

use Xinc\Core\Exception\Mistake;

/**
 * Base class for classes which needs to have a singleton instance.
 */
class Singleton
{
    /**
     * @var array<Singleton> Instance of the singleton class.
     */
    protected static $instances = array();

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }

    protected function __wakeup()
    {
        throw new Mistake('You can\'t wakeup Singletons.');
    }

    /**
     * Get an instance of the Singleton Object.
     *
     * @return \Xinc\Core\Singleton
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(static::$instances[$class])) {
            static::$instances[$class] = new static();
        }

        return static::$instances[$class];
    }

    public static function tearDown()
    {
        $class = get_called_class();
        unset(static::$instances[$class]);
    }
}
