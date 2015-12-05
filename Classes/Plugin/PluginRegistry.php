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
 * @link      https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core\Plugin;

use Xinc\Core\Task\Slot;
use Xinc\Core\Traits\Logger;

/**
 * Registry holding the plugins
 * @ingroup registry
 * @ingroup logger
 */
class PluginRegistry
{
	use Logger;
	
    private $definedTasks = array();
    private $_plugins = array();
    
    /**
     * Holding a reference from the task to
     * the slot they are working in
     *
     * @var array
     */
    private $_slotReference = array();

    public function registerPlugin(PluginInterface $plugin)
    {
        $pluginClass = get_class($plugin);
        if (!$plugin->validate($msg)) {
            $this->log->error(
                'Plugin ' . $pluginClass . ' is invalid.' .
                ($msg ? "\nValidation message: $msg" : '')
            );
                                 
            return false;
        }
        $tasks = $plugin->getTaskDefinitions();

        $task = null;
        foreach ($tasks as $task) {
            $taskClass = get_class($task);
            $fullTaskName = $task->getName();
            $taskSlot = $task->getPluginSlot();

            switch ($taskSlot) {
                case Slot::PROJECT_SET_VALUES: 
                        // make sure the task implements the setter interface
                        if (!$task instanceof Xinc_Plugin_Task_Setter_Interface) {
                            Xinc_Logger::getInstance()->error(
                                'cannot register task ' . $fullTaskName
                                . ' it does not implement the required interface '
                                . 'Xinc_Plugin_Task_Setter_Interface'
                            );
                            continue;
                        }
                    break;
                default:
                    break;
            }

            /**
             * Register task for the slot
             */
            if (!isset($this->_slotReference[$taskSlot])) {
                $this->_slotReference[$taskSlot] = array();
            }
            $this->_slotReference[$taskSlot][] = &$task;

            $parentTasks  = array(); //$task->getAllowedParentElements(); // should return the tasks! not the string
            if (count($parentTasks)>0) {
                $this->_registerTaskDependencies($plugin, $task, $parentTasks);
            } else {

                $fullTaskName = strtolower($fullTaskName);
                
                if (isset($this->_definedTasks[$fullTaskName])) {
                        throw new Xinc_Plugin_Task_Exception();
                }
                $this->definedTasks[$fullTaskName] = array(
                    'classname'=> $taskClass,
                    'plugin'   => array('classname'=> $pluginClass)
                );

                    // register default classname as task
                $classNameTask = strtolower($taskClass);
                if (isset($this->definedTasks[$classNameTask])) {
                    throw new Xinc_Plugin_Task_Exception();
                }
                $this->definedTasks[$classNameTask] = array(
                    'classname'=> $taskClass,
                    'plugin'   => array('classname' => $pluginClass)
                );
            }
        }
        
        $this->_plugins[] = $plugin;
    }

    /**
     *
     * @param Xinc_Plugin_Interface $plugin
     * @param Xinc_Plugin_Task_Interface $task
     * @param array $parentTasks
     *
     * @throws Xinc_Plugin_Task_Exception
     */
    private function _registerTaskDependencies(Xinc_Plugin_Interface $plugin,
                                               Xinc_Plugin_Task_Interface $task,
                                               array $parentTasks
    ) {    
        $taskClass = get_class($task);
        $pluginClass = get_class($plugin);
        $fullTaskNames = array();
        foreach ($parentTasks as $parentTask) {
            if ($parentTask instanceof Xinc_Plugin_Task_Interface ) {
                $parentTaskClass = get_class($parentTask);
                $fullTaskNames[] = $parentTask->getName() . '/' . $task->getName();
                $fullTaskNames[] = $parentTaskClass . '/' . $taskClass;
            }
        }
        foreach ($fullTaskNames as $fullTaskName) {
            $fullTaskName = strtolower($fullTaskName);

            if (isset($this->definedTasks[$fullTaskName])) {
                throw new Xinc_Plugin_Task_Exception();
            }
            $this->definedTasks[$fullTaskName] = array(
                'classname'=> $taskClass,
                'plugin'   => array('classname'=> $pluginClass)
            );
        }
    }

    public function getTask($taskname, $parentElement = null)
    {
        $taskname = strtolower($taskname);
        if ($parentElement !== null) {
            $taskname2  = $parentElement . '/' . $taskname;
        }

        if (isset($this->definedTasks[$taskname2])) {
            $taskData = $this->definedTasks[$taskname2];
        } else if (isset($this->definedTasks[$taskname])) {
            $taskData = $this->definedTasks[$taskname];
        } else {
            
            throw new Xinc_Plugin_Task_Exception('undefined task '.$taskname);
        }

        if ( !isset($this->_plugins[$taskData['plugin']['classname']]) ) {
            
            $plugin = new $taskData['plugin']['classname'];
            $this->_plugins[$taskData['plugin']['classname']] = &$plugin;

        } else {
            $plugin = $this->_plugins[$taskData['plugin']['classname']];
        }

        $className = $taskData['classname'];
        $object = new $className($plugin);
        return $object;
    }
    
    /**
     * Returns Plugin Iterator
     *
     * @return Xinc_Iterator
     */
    public function getPlugins()
    {
        return new Xinc_Plugin_Iterator($this->_plugins);
    }

    /**
     * Returns all tasks that are registered
     * for a specific slot
     *
     * @param int $slot @see Xinc_Plugin_Slot
     *
     * @return Xinc_Iterator
     */
    public function getTasksForSlot($slot)
    {
        if (!isset($this->_slotReference[$slot])) {
            return new Xinc_Iterator();
        } else {
            return new Xinc_Iterator($this->_slotReference[$slot]);
        }
    }
}
