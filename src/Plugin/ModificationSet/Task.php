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
 * @link      https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core\Plugin\ModificationSet;

use Xinc\Core\Task\Base;
use Xinc\Core\Task\Slot;
use Xinc\Core\Build\BuildInterface;

class Task extends Base implements ModificationSetInterface
{
    protected $results = array();

    public function addResult(Result $result)
    {
        $this->results[] = $result;
    }

    /**
     * Returns name of Task.
     *
     * @return string Name of task
     */
    public function getName()
    {
        return 'modificationset';
    }

    /**
     * Returns the slot of this task inside a build.
     *
     * @return int The slot number
     */
    public function getPluginSlot()
    {
        return Slot::PRE_PROCESS;
    }

    public function process(BuildInterface $build)
    {
        foreach ($this->results as $result) {
            $build->info($result);
            if ($result->getStatus() == Result::CHANGED) {
                $build->setStatus(BuildInterface::PASSED);
                break;
            } elseif ($result->getStatus() === Result::STOPPED) {
                $build->setStatus(BuildInterface::STOPPED);
            } elseif ($result->getStatus() === Result::FAILED) {
                $build->setStatus(BuildInterface::FAILED);
            } elseif ($result->getStatus() === Result::ERROR) {
                $build->setStatus(BuildInterface::STOPPED);
                break;
            } else {
                $build->setStatus(BuildInterface::STOPPED);
            }

            return;
        }
    }
}
