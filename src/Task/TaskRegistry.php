<?php
/**
 * Xinc - Continuous Integration.
 *
 * @author    Alexander Opitz <opitz.alexander@googlemail.com>
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

namespace Xinc\Core\Task;

use Xinc\Core\Registry\RegistryAbstract;

/**
 * Registry for task objects.
 *
 * @ingroup registry
 * @ingroup logger
 */
class TaskRegistry extends RegistryAbstract
{
    /**
     * @var typeOf The Name of the class this elements should be
     */
    protected $typeOf = '\Xinc\Core\Task\TaskInterface';

    /**
     * @var array Array of registered elements
     */
    private $slot = array();

    public function registerTasks($tasks)
    {
        foreach ($tasks as $task) {
            $this->registerTask($task);
        }
    }

    public function registerTask(TaskInterface $task)
    {
        $this->register($task->getName(), $task);
    }

    /**
     * @param string $name
     * @param object $task
     *
     * @throws Xinc\Core\Registry\Exception
     */
    public function register($name, $task)
    {
        parent::register($name, $task, true);
        $this->slot[$task->getPluginSlot()][] = $task;
    }

    /**
     * @todo this does not work correctly in BuildTaskRegistry
     *
     * @param string $name
     *
     * @return Xinc::Core::Task::TaskInterface - the deleted task
     *
     * @throws Xinc\Core\Registry\Exception
     */
    public function unregister($name)
    {
        $task = $parent::unregister($name);
        foreach ($this->slot[$task->getPluginSlot()] as $i => $check) {
            if ($check === $task) {
                unset($this->slot[$task->getPluginSlot()][$i]);
            }
        }

        return $task;
    }

    /**
     * Returns all tasks that are registered
     * for a specific slot.
     *
     * @param int $slot @see Xinc::Core::Plugin::Slot
     *
     * @return Xinc::Core::Task::Iterator
     */
    public function getTasksForSlot($slot)
    {
        if (!isset($this->slot[$slot])) {
            return new Iterator();
        } else {
            return new Iterator($this->slot[$slot]);
        }
    }

    public function getTask($taskname, $parentElement = null)
    {
        if ($parentElement !== null) {
            $taskname2 = $parentElement.'/'.$taskname;
            if ($this->knows($taskname2)) {
                return $this->get($taskname2);
            }
        }

        return $this->get($taskname);
    }
}
