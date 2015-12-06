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

namespace Xinc\Core\Gui;

use Xinc\Core\Registry\RegistryAbstract;

/**
 * Registry for widget objects
 * @ingroup registry
 * @ingroup logger
 */
class WidgetRegistry extends RegistryAbstract
{
    /**
     * @var typeOf The Name of the class this elements should be.
     */
    protected $typeOf = '\Xinc\Core\Gui\WidgetInterface';

    /**
     * @var array Array of registered widgets
     */
    private $paths = array();

    public function registerWidgets($widgets)
    {
        foreach ($widgets as $widget) {
            $this->register(get_class($widget),$widget);
        }
    }

    /**
     *
     * @param string $name
     * @param object $task
     * @throws Xinc::Core::Registry::RegistryException
     * @throws Xinc::Core::Validation::Exception::TypeMismatch
     */
    public function register($name, $widget)
    {
        parent::register($name, $widget);
        $paths = $widget->getPaths();
        if (!is_array($paths)) {
			$this->log->warn(get_class($widget) . "::getPaths has invalid return value.");
            $paths = array();
        }
        
        foreach ($paths as $path) {
            $this->paths[$path] = $widget;
        }
    }

    /**
     *
     * @param string $name
     * @return object
     * @throws Xinc\Core\Registry\Exception
     */
    public function unregister($name)
    {
        $widget = parent::unregister($name);
        $paths = $widget->getPaths();
        if (!is_array($paths)) {
			$this->log->warn(get_class($widget) . "::getPaths has invalid return value.");
            $paths = array();
        }
        
        foreach ($paths as $path) {
            unset($this->paths[$path]);
        }
        return $widget;
    }

    /**
     * Determines the Widget that should be used
     * for the specified Http-Request by the Pathname that 
     * is called
     *
     * @param String $path Pathname of the HTTP-Request
     *
     * @return WidgetInterface
     * @todo optimize match
     */    
    public function getWidgetForPath($path)
    {
        $this->log->info('Getting widget for path: ' . $path);
        $widget = null;
        if (!isset($this->paths[$path])) {
            // find the largest match
            $largest = 0;
            foreach ($this->paths as $pathReg => $widgetItem) {
                
                if (($match = strstr($path, $pathReg)) !== false && strpos($path, $pathReg)==0) {
                    if (strlen($pathReg)>$largest) {
                        
                        $largest = strlen($pathReg);
                        $widget = $widgetItem;
                    }
                }
            }
        } else {
            $widget = $this->paths[$path];
        }
        return $widget;
    }
}
