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
namespace Xinc\Core\Plugin;

use Xinc\Core\Registry\RegistryAbstract;
use Xinc\Core\Registry\RegistryInterface;
use Xinc\Core\Task\TaskRegistryInterface;
use Xinc\Core\Task\Iterator;
use Xinc\Core\Task\SetterInterface;
use Xinc\Core\Task\Slot;
use Xinc\Core\Traits\Logger;
use Xinc\Core\Traits\TaskRegistry;

/**
 * Registry holding the plugins.
 *
 * @ingroup registry
 * @ingroup logger
 */
class PluginRegistry extends RegistryAbstract 
  implements RegistryInterface, TaskRegistryInterface
{
    use Logger;
    use TaskRegistry;

    protected $typeOf = 'Xinc\Core\Plugin\PluginInterface';

    private $definedTasks = array();

    /**
     * Holding a reference from the task to
     * the slot they are working in.
     *
     * @var array
     */
    private $slotReference = array();

    /**
     * Register task for the slot
     */
    private function registerTaskForSlot($slot,$task)
    {
        $this->slotReference[$slot][] = $task;
    }

    public function registerPlugin(PluginInterface $plugin)
    {
        $pluginClass = get_class($plugin);
        if (!$plugin->validate($msg)) {
            $this->log->error(
                'Plugin '.$pluginClass.' is invalid.'.
                ($msg ? "\nValidation message: $msg" : '')
            );

            return false;
        }
        $this->register($plugin->getName(), $plugin);

        $tasks = $plugin->getTaskDefinitions();

        $task = null;
        foreach ($tasks as $task) {
            $taskClass = get_class($task);
            $fullTaskName = $task->getName();
            $taskSlot = $task->getPluginSlot();

            switch ($taskSlot) {
                case Slot::PROJECT_SET_VALUES:
                        // make sure the task implements the setter interface
                        if (!$task instanceof SetterInterface) {
                            $this->log->error(
                                'cannot register task '.$fullTaskName
                                .' it does not implement the required interface '
                                .'Xinc_Plugin_Task_Setter_Interface'
                            );
                            continue;
                        }
                    break;
                default:
                    break;
            }
            $this->registerTaskForSlot($taskSlot,$task);
        }
    }

    /**
     * Returns Plugin Iterator.
     *
     * @return Xinc_Iterator
     */
    public function getPlugins()
    {
        return $this->getIterator();
    }

    /**
     * Returns all tasks that are registered
     * for a specific slot.
     *
     * @param int $slot @see Xinc_Plugin_Slot
     *
     * @return Xinc::Core::Task::Iterator
     */
    public function getTasksForSlot($slot)
    {
        if (!isset($this->slotReference[$slot])) {
            return new Iterator();
        } else {
            return new Iterator($this->slotReference[$slot]);
        }
    }
}
