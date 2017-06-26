<?php
/**
 * Xinc - Continuous Integration.
 * Property setter task.
 *
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
 * @link      https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core\Plugin\Property;

use Xinc\Core\Build\BuildInterface;
use Xinc\Core\Task\Base;
use Xinc\Core\Task\Slot;
use Xinc\Core\Task\SetterInterface;

class SubstituteTask extends Base implements SetterInterface
{
    /**
     * Returns name of Task.
     *
     * @return string Name of task
     */
    public function getName()
    {
        return 'propertySubstitution';
    }

    /**
     * Returns the slot of this task inside a build.
     *
     * @return int The slot number
     */
    public function getPluginSlot()
    {
        return Slot::PROJECT_SET_VALUES;
    }

    public function process(BuildInterface $build)
    {
        $build->debug('Setting property "${'.$this->_name.'}" to "'.$this->_value.'"');
        //$build->getProperties()->set($this->_name, $this->_value);
    }

    public function set(BuildInterface $build, $value)
    {
        $newvalue = $build->parseProperty($value);

        return $newvalue;
    }
}
