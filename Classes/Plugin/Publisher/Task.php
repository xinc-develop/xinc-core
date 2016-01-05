<?php
/**
 * Xinc - Continuous Integration.
 * This interface represents a publishing mechanism to publish build results
 *
 * PHP version 5
 *
 * @category  Development
 * @package   Xinc.Plugin.Publisher
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
 * @link      http://code.google.com/p/xinc/
 */

namespace Xinc\Core\Plugin\Publisher;

use Xinc\Core\Build\BuildInterface;
use Xinc\Core\Task\Base;
use Xinc\Core\Task\Slot;

class Task extends Base
{
    /**
     * Returns name of task by lowercasing class name.
     *
     * @return string Name of task.
     */
    public function getName()
    {
        return 'publishers';
    }

    /**
     * Returns the slot of this task inside a build.
     *
     * @return integer The slot number.
     * @see Xinc::Core::Task::Slot for available slots
     */
    public function getPluginSlot()
    {
        return Slot::POST_PROCESS;
    }

    public function process(BuildInterface $build)
    {
        $build->info('Processing publishers done');
    }
}
