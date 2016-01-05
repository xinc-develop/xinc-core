<?php
/**
 * Xinc - Continuous Integration.
 * Engine to build projects.
 *
 * @author    Sebastian Knapp
 * @author    Arno Schneider
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
namespace Xinc\Core\Engine;

use Xinc\Core\Build\BuildInterface;
use Xinc\Core\Project\Project;
use Xinc\Core\Project\Status;
use Xinc\Core\Task\TaskInterface;
use Xinc\Core\Task\Slot;
use Xinc\Core\Traits\Config;
use Xinc\Core\Traits\Logger;
use Xinc\Core\Traits\PluginRegistry;
use Xinc\Core\Traits\TaskRegistry;

/**
 * Base class for engines with common functionality.
 *
 * @ingroup config
 * @ingroup logger
 */
abstract class Base implements EngineInterface
{
    use Config;
    use Logger;
    use PluginRegistry;
    use TaskRegistry;

    public function getLogger()
    {
        return $this->log;
    }
    
    protected final function getTasksForSlot($slot)
    {
		return $this->pluginRegistry->getTasksForSlot($slot);
	}

    protected function setupBuildProperties(BuildInterface $build)
    {
        $project = $build->getProject();
        $build->setProperty('project.name', $project->getName());
        $build->setProperty('build.number', $build->getNumber());
        $build->setProperty('build.label', $build->getLabel());
    }

    protected function setupConfigProperties(BuildInterface $build)
    {
        $options = array('workingdir', 'projectdir', 'statusdir');
        foreach ($options as $option) {
            $build->setProperty($option, $this->config->get($option));
        }
    }
    
    protected function parseProjectConfig(BuildInterface $build, $xml, $parent=null)
    {
        $filtertasks = $this->getTasksForSlot(Slot::PROJECT_SET_VALUES);

        foreach ($xml->children() as $taskName => $task) {            
            try{
                $taskObject = $this->taskRegistry->getTask($taskName, (string)$parent);
                $taskObject = $taskObject->createTask($build);
                $taskObject->setXml($task);
            } 
            catch(Exception $e){
                $this->log->error('Task "'.$taskName.'" not found.');
                $build->getProject()->setStatus(Status::MISCONFIGURED);
                return;
            }
            foreach ($task->attributes() as $name => $value) {
                $setter = 'set'.$name;
                foreach($filtertasks as $filter) {
					$value = $filter->set($build,$value);
				}
                $taskObject->$setter((string)$value, $build);
            }

            $this->parseProjectConfig($build, $task, $taskObject);
            
            if($parent instanceof TaskInterface) {
                $taskObject->setFrame($parent);
            }
            $build->registerTask($taskObject);
            
            if ( !$taskObject->validate() ) {
				$this->log->warn("Task {$taskObject->getName()} is invalid.".
				    ($msg ? "\nError message: $msg" : '') 
				);
                $build->getProject()->setStatus(Status::MISCONFIGURED);
                return;
            }
        }
    }
}
