<?php
/**
 * Xinc - Continuous Integration.
 *
 * @author    Arno Schneider <username@example.org>
 * @copyright 2007 Arno Schneider, Barcelona
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

namespace Xinc\Core\Build;

use Xinc\Core\Validation\Exception\NotNumerical;

/**
 * Collects statistics from a build.
 *
 * Build Statistics carry numerical values for certain build aspects, like:
 * - build time
 * - number of unittests
 * - number of coding style violations etc
 */
class Statistics
{
    /**
     * Associative Array holding the nvp for the build statistics.
     *
     * @var array
     */
    private $statistics = array();

    /**
     * set a property.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @throws Xinc::Core::Build::Exception::NonNumerical
     */
    public function set($name, $value)
    {
        if (!is_numeric($value)) {
            throw new NotNumerical($name, $value);
        }
        $this->statistics[$name] = $value;
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
        if (isset($this->statistics[$name])) {
            return $this->statistics[$name];
        } else {
            return;
        }
    }

    /**
     * returns all the properties in an array.
     *
     * @return array
     */
    public function getAllStatistics()
    {
        return $this->statistics;
    }
}
