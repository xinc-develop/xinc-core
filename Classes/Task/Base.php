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
 * @homepage  https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core\Task;

use Xinc\Core\Task\TaskInterface;
use Xinc\Core\Plugin\PluginInterface;
use Xinc\Core\Build\BuildInterface;

abstract class Base implements TaskInterface
{
    /**
     * @var array Subtasks for this task
     */
    protected $arSubtasks = array();

    protected $_plugin;
    protected $_xml;
    
    /**
     * Constructor, stores a reference to the plugin for
     * usage of functionality
     *
     * @param Xinc_Plugin_Interface $plugin
     */
    public function __construct(PluginInterface $plugin)
    {
        $this->_plugin = $plugin;
    }

    public function init(BuildInterface $build = null)
    {
    }

    /**
     * Support for subtasks, empty by default.
     *
     * @param Xinc_Plugin_Task_Interface $task Task to register
     *
     * @return void
     */
    public function registerTask(Xinc_Plugin_Task_Interface $task)
    {
        Xinc_Logger::getInstance()->debug('Registering Task: ' . get_class($task));
        $this->arSubtasks[] = $task;
    }

    /**
     * Returns name of task by lowercasing class name.
     *
     * @return string Name of task.
     */
    public function getName()
    {
        return strtolower(get_class($this));
    }

    public function getTasks()
    {
        return new Xinc_Build_Tasks_Iterator($this->arSubtasks);
    }

    public function getXml()
    {
        return $this->_xml;
    }

    public function setXml(SimpleXMLElement $element)
    {
        $this->_xml = $element;
    }
}
