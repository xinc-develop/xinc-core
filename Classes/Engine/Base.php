<?php
/*
 * Xinc - Continuous Integration.
 *
 * @author    Sebastian Knapp
 * @author    Arno Schneider
 * @copyright 2007 Arno Schneider, Barcelona
 * @copyright 2015-2016 Xinc Development Team, https://github.com/xinc-develop/
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
use Xinc\Core\Exception\MalformedConfigException;
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
 * An engine controls a build process.
 * @ingroup config
 * @ingroup logger
 */
abstract class Base implements EngineInterface
{
    use Config;
    use Logger;
    use PluginRegistry;
    use TaskRegistry;

    /**
     * get the name of this engine
     *
     * @return string
     */
    public function getName()
    {
        return $this::NAME;
    }

    /**
     * An engine shares the logger with the controled build
     * @return Xinc::Core::Logger
     */
    public function getLogger()
    {
        return $this->log;
    }

    /**
     * The tasks which are performed during a slot.
     * @param Xinc::Core::Task::Slot
     */
    protected final function getTasksForSlot($slot)
    {
        return $this->pluginRegistry->getTasksForSlot($slot);
    }

    /**
     * copies some basic informations to the build object
     * @param Xinc::Core::Build::BuildInterface
     */
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
            $build->debug("Register task {$taskObject->getName()}");
            $build->registerTask($taskObject);

            if(!$this->validateTask($taskObject)) {
                $build->getProject()->setStatus(Status::MISCONFIGURED);
                return;
            }
        }
    }

    /**
     * Calls the validate method of a task.
     */
    protected function validateTask($taskObject)
    {
        try {
            if ( !$taskObject->validate($msg) ) {
                $this->log->warn("Task {$taskObject->getName()} is invalid.".
                    ($msg ? "\nError message: $msg" : '')
                );
                return false;
            }
            return true;
        }
        catch(MalformedConfigException $e) {
            $this->log->error("Error in task {$taskObject->getName()} configuration: " .
                $e->getMessage()
            );
            return false;
        }
    }

    /**
     * Called at the begin of a build
     * @return boolean false if the build is finished (misconfigured?)
     */
    protected function initBuild(BuildInterface $build)
    {
        $build->debug("PRE INIT STATUS " . $build->getStatusString());
        $build->init();
        $build->debug("POST INIT STATUS " . $build->getStatusString());
        return !$build->isFinished();
    }

    /**
     * Called at the end of a build
     */
    protected function endBuild(BuildInterface $build)
    {
        $status = $build->getStatusString();
        $build->info("END BUILD WITH STATUS $status.");
    }
}
