<?php
/*
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

use Xinc\Core\Exception\Mistake;
use Xinc\Core\Task\TaskInterface;
use Xinc\Core\Task\TaskRegistry as Base;
use Xinc\Core\Task\Slot;

/**
 * A build contains a kind of this registry to store the concrete
 * task objects.
 */
class TaskRegistry extends Base
{
    /**
     * available slots
     */
    private $slots;

    public function __construct()
    {
        $this->slots = Slot::getSlots();
    }

    /**
     * Override because this registry can not work by name - the task name
     * is a class attribute.
     *
     * @param string $name
     * @param object $task
     */
    public function register($name, $task)
    {
        $this->registerTask($task);
    }

    /**
     * Register the object for the slot.
     */
    public function registerTask(TaskInterface $task)
    {
        $this->slot[$task->getPluginSlot()][] = $task;
    }

    /**
     * @param string $name
     * @throws Xinc\Core\Exception\Mistake
     */
    public function unregister($name)
    {
        throw new Mistake("This registry does not support unregister by name.");
    }

    /**
     * Unregister the task from the slot.
     */
    public function unregisterTask(TaskInterface $task)
    {
        foreach($this->slot[$task->getPluginSlot()] as $i => $check) {
            if($check === $task) {
                unset($this->slot[$task->getPluginSlot()][$i]);
            }
        }
        return $task;
    }
}
