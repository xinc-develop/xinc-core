<?php
/**
 * Xinc - Continuous Integration.
 *
 * @author    Arno Schneider
 * @author    Sebastian Knapp <news@young-workers.de>
 * @copyright 2014 Alexander Opitz, Leipzig
 * @copyright 2015 Xinc Development Team, https://github.com/xinc-develop/
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

namespace Xinc\Core\Registry;

use Xinc\Core\Traits\Logger;

use Xinc\Core\Validation\Exception\TypeMismatch;
use Xinc\Core\Exception\ClassLoaderException;

use Xinc\Core\Engine\EngineInterface;
use Xinc\Core\Plugin\PluginInterface;

use Xinc\Core\Engine\EngineRegistry;
use Xinc\Core\Project\Project;
use Xinc\Core\Project\ProjectRegistry;
use Xinc\Core\Plugin\PluginRegistry;
use Xinc\Core\Task\TaskRegistry;
use Xinc\Core\Api\ApiRegistry;
use Xinc\Core\Gui\WidgetRegistry;

/**
 * The central registry for all types.
 * @ingroup logger
 * @ingroup registry
 * @todo api registry
 */
class Registry implements XincRegistryInterface
{
	use Logger;
	
	private $engineRegistry;
	private $projectRegistry;
    private $pluginRegistry;
    private $taskRegistry;
    private $widgetRegistry;
    private $apiRegistry;
    
    public function __construct()
    {
		$this->engineRegistry = new EngineRegistry();
		$this->projectRegistry = new ProjectRegistry();
	    $this->pluginRegistry = new PluginRegistry();
	    $this->taskRegistry = new TaskRegistry();
	    $this->widgetRegistry = new WidgetRegistry();
	    #$this->apiRegistry = new ApiRegistry();
	}
	
	public function setLogger($log)
	{
		$this->log = $log;
		$this->engineRegistry->setLogger($log);
		$this->projectRegistry->setLogger($log);
		$this->pluginRegistry->setLogger($log);
	    $this->taskRegistry->setLogger($log);
	    $this->widgetRegistry->setLogger($log);
	    #$this->apiRegistry->setLogger($log);
	}
	
    public function registerPluginClass($class)
    {
	    if(!class_exists($class)) {
			throw new ClassLoaderException($class);
		}
        $plugin = new $class;

        if (!($plugin instanceof PluginInterface)) {
            throw new TypeMismatch(get_class($plugin),
                '\Xinc\Core\Plugin\PluginInterface');
        }
        $this->pluginRegistry->registerPlugin($plugin);
        $this->taskRegistry->registerTasks($plugin->getTaskDefinitions());
        $this->widgetRegistry->registerWidgets($plugin->getGuiWidgets());
        return;
        
        $apiModules = $plugin->getApiModules();
        foreach ($apiModules as $apiMod) {
            Xinc_Api_Module_Repository::getInstance()->registerModule($apiMod);
        }
	}
	
	public function registerEngineClass($class,$default)
	{
		if(!class_exists($class)) {
			throw new ClassLoaderException($class);
		}
        $engine = new $class;

        if (!($engine instanceof EngineInterface)) {
            throw new TypeMismatch(get_class($engine),
                '\Xinc\Core\Engine\EngineInterface');
        }
        $this->engineRegistry->register($engine->getName(),$engine);
        if($default) {
			$this->engineRegistry->setDefaultEngine($engine->getName());
		}
	}
	
	public function registerProject(Project $project)
	{
		$this->projectRegistry->register($project->getName(),$project);
	}
	
	public function getPlugin($name)
	{
		return $this->pluginRegistry->get($name);
	}
	
	public function getEngine($name)
	{
		return $this->engineRegistry->get($name);
	}
	/**
	 * Get the default engine.
	 * @throw Xinc::Core::Registry::RegistryException
	 */
	public function getDefaultEngine()
	{
	    return $this->engineRegistry->getDefaultEngine();	
	}
}
