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
 * @link      https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core\Task;

use Xinc\Core\Registry\RegistryAbstract;

/**
 * Registry for task objects
 * @ingroup registry
 * @ingroup logger
 */
class WidgetRegistry extends RegistryAbstract
{
    /**
     * @var typeOf The Name of the class this elements should be.
     */
    protected $typeOf = '\Xinc\Core\Task\TaskInterface';

    /**
     * @var array Array of registered elements
     */
    private $slot = array();

    /**
     *
     * @param string $name
     * @param object $task
     * @throws Xinc\Core\Registry\Exception
     */
    public function register($name, $task)
    {
        parent::register($name, $task);

        $this->slot[$task->getPluginSlot()][$name] = $task;
    }

    /**
     *
     * @param string $name
     * @return object
     * @throws Xinc\Core\Registry\Exception
     */
    public function unregister($name)
    {
        $task = $parent::unregister($name);

        unset($this->slot[$task->getPluginSlot()][$name]);

        return $task;
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
        if (!isset($this->slot[$slot])) {
            return new \Xinc\Core\Iterator\Task();
        } else {
            return new \Xinc\Core\Iterator\Task($this->slot[$slot]);
        }
    }
}
<?php
declare(encoding = 'utf-8');
/**
 * Xinc - Continuous Integration.
 * The Widget Repository holds all the Widgets
 * that are defined by the loaded plugins.
 *
 * PHP version 5
 *
 * @category  Development
 * @package   Xinc.Gui.Widget
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
 * @link      http://xincplus.sourceforge.net
 */

require_once 'Xinc/Gui/Widget/Exception/NotFound.php';

/**
 * The Widget-Repository allows the Web-Frontend of Xinc to choose the right
 * plugin for execution based on the Http-Request
 *
 * @package Xinc.Gui.Widget
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU/LGPL, see license.php
 * @version    2.0
 * @author     Arno Schneider
 * @since      Class available since Release 2.0
 */
class Xinc_Gui_Widget_Repository
{
    /**
     * @var Xinc_Gui_Widget_Repository
     */
    private static $_instance;

    /**
     * @var Xinc_Gui_Widget_Interface[]
     */
    private $_definedWidgets = array();
    
    private $_widgetClasses = array();

    /**
     * Contains all the registered widgets
     *
     * @var Xinc_Gui_Widget_Interface[]
     */
    private $_widgets = array();
    
    /**
     * Return an instance of the Widget Repository
     *
     * @return Xinc_Gui_Widget_Repository
     */
    public static function getInstance()
    {
        if (!Xinc_Gui_Widget_Repository::$_instance) {
            Xinc_Gui_Widget_Repository::$_instance = new Xinc_Gui_Widget_Repository();
        }
        return Xinc_Gui_Widget_Repository::$_instance;
    }
    
    /**
     * Register a widget with the Repository
     *
     * @param Xinc_Gui_Widget_Interface $widget
     */
    public function registerWidget(Xinc_Gui_Widget_Interface &$widget)
    {
        /**
         *  Determine the Pathnames which
         * the Widget will be called for
         */
        $paths = $widget->getPaths();
        //echo "Registering: " . get_class($widget) . "\n<br>";
        /**
         * protect us against "bad" plugins which dont return the expected array
         */
        if (!is_array($paths)) {
            $paths = array();
        }
        
        foreach ($paths as $path) {
            /**
             * register the widget for the specified pathname
             */
            $this->_definedWidgets[$path] = $widget;
        }
        $this->_widgets[] = $widget;
        $this->_widgetClasses[get_class($widget)] = $widget;
        
    }

    /**
     * Gets a registered widget by its classname
     *
     * @param string $name
     *
     * @return Xinc_Gui_Widget_Interface or null
     * @throws Xinc_Gui_Widget_Exception_NotFound
     */
    public function &getWidgetByClassName($name)
    {
        if (isset($this->_widgetClasses[$name])) {
            return $this->_widgetClasses[$name];
        }
        throw new Xinc_Gui_Widget_Exception_NotFound($name);
    }
    /**
     * Determines the Widget that should be used
     * for the specified Http-Request by the Pathname that 
     * is called
     *
     * @param String $path Pathname of the HTTP-Request
     *
     * @return Xinc_Gui_Widget_Interface
     */    
    public function &getWidgetForPath($path)
    {
        Xinc_Logger::getInstance()->info('Getting widget for path: ' . $path);
        $widget = null;
        if (!isset($this->_definedWidgets[$path])) {
            // find the largest match
            $largest = 0;
            foreach ($this->_definedWidgets as $pathReg => $widgetItem) {
                
                if (($match = strstr($path, $pathReg)) !== false && strpos($path, $pathReg)==0) {
                    if (strlen($pathReg)>$largest) {
                        
                        $largest = strlen($pathReg);
                        $widget = $widgetItem;
                    }
                }
            }
        } else {
            $widget = $this->_definedWidgets[$path];
        }
        return $widget;
    }
    
    /**
     * Returns all the registered Widgets
     *
     * @return Xinc_Gui_Widget_Interface[]
     */
    public function getWidgets()
    {
        return $this->_widgets;
    }
}
