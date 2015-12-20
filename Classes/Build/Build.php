<?php
/**
 * Xinc - Continuous Integration.
 *
 * @author    Arno Schneider <username@example.org>
 * @author    Sebastian Knapp
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
 * @link      https://github.com/xinc-develop/xinc-core/
 */
namespace Xinc\Core\Build;

use Xinc\Core\Build\TaskRegistry as BuildTaskRegistry;
use Xinc\Core\Engine\EngineInterface;
use Xinc\Core\Project\Project;
use Xinc\Core\Project\Status as ProjectStatus;
use Xinc\Core\Properties as BuildProperties;
use Xinc\Core\Traits\Logger;
use Xinc\Core\Traits\TaskRegistry;

/**
 * This class represents the build that is going to be run with Xinc.
 *
 * @ingroup logger
 */
class Build implements BuildInterface
{
    use Logger;
    use TaskRegistry;
    /**
     * Are we queued?
     *
     * @var bool
     */
    private $isQueued = false;

    /**
     * @var Xinc::Core::Engine::EngineInterface
     */
    private $engine;

    /**
     * @var Xinc::Core::Project::Project
     */
    private $project;

    /**
     * @var Xinc_Build_Properties
     */
    private $properties;

    /**
     * @var Xinc_Build_Properties
     */
    private $internalProperties;

    /**
     * @var Xinc_Build_Statistics
     */
    private $statistics;

    /**
     * @var int
     */
    private $buildTimestamp;

    /**
     * @var int
     */
    private $nextBuildTimestamp;

    /**
     * Build status, as defined in Xinc_Build_Interface.
     *
     * @var int
     */
    private $status;

    /**
     * @var Xinc_Build_Interface
     */
    private $lastBuild;

    /**
     * The build no of this build.
     *
     * @var int
     */
    private $no;

    /**
     * The label for this build.
     *
     * @var string
     */
    private $label;

    /**
     * Build scheduler.
     *
     * @var Xinc_Build_Scheduler_Interface
     */
    private $scheduler;

    /**
     * @var Xinc_Build_Labeler_Interface
     */
    private $labeler;

    /**
     * Holding config values for this build.
     *
     * @var array
     */
    private $config = array();

    /**
     * sets the project, engine
     * and timestamp for the build.
     *
     * @param Xincengine_Interface $engine
     * @param Xincproject          $project
     * @param int                  $buildTimestamp
     */
    public function __construct(EngineInterface $engine,
                                Project $project,
                                $buildTimestamp = null
    ) {
        $this->engine = $engine;
        $this->setLogger($engine->getLogger());
        $this->project = $project;

        if (ProjectStatus::MISCONFIGURED == $this->project->getStatus()) {
            $this->setStatus(BuildInterface::MISCONFIGURED);
        }

        $this->buildTimestamp = $buildTimestamp;
        $this->properties = new BuildProperties();
        $this->internalProperties = new BuildProperties();
        $this->statistics = new Statistics();
        $this->setLabeler(new Labeler\DefaultLabeler());
        $this->setScheduler(new Scheduler\DefaultScheduler());
        
        $taskRegistry = new BuildTaskRegistry;
        $taskRegistry->setLogger($engine->getLogger());
        $this->setTaskRegistry($taskRegistry);
    }

    public function setLabeler(Labeler\LabelerInterface $labeler)
    {
        $this->labeler = $labeler;
    }

    /**
     * Returns the last build.
     *
     * @return Xinc_Build_Interface
     */
    public function getLastBuild()
    {
        if ($this->lastBuild == null) {
            $build = new self($this->getEngine(), $this->getProject());

            return $build;
        }

        return $this->lastBuild;
    }

    /**
     * @return Xinc::Core::Properties
     */
    public function getProperties()
    {
        return $this->properties;
    }

    public function getProperty($name)
    {
        return $this->properties->get($name);
    }

    /**
     * @param string $name
     * @param $value
     */
    public function setProperty($name, $val)
    {
        $this->properties->set($name, $val);
    }

    /**
     * @return Xinc::Core::Properties
     */
    public function getInternalProperties()
    {
        return $this->internalProperties;
    }

    /**
     * @return Xinc_Build_Statistics
     */
    public function getStatistics()
    {
        return $this->statistics;
    }
    /**
     * sets the build time for this build.
     *
     * @param int $buildTime unixtimestamp
     */
    public function setBuildTime($buildTime)
    {
        $this->getProperties()->set('build.timestamp', $buildTime);
        $this->buildTimestamp = $buildTime;
    }

    /**
     * returns the timestamp of this build.
     *
     * @return int Timestamp of build (unixtimestamp)
     */
    public function getBuildTime()
    {
        return $this->buildTimestamp;
    }

    /**
     * Returns the next build time (unix timestamp)
     * for this build.
     */
    public function getNextBuildTime()
    {
        return $this->scheduler->getNextBuildTime($this);
    }

    /**
     * @return Xinc::Core::Project::Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return Xinc::Core::Engine::EngineInterface
     */
    public function getEngine()
    {
        return $this->engine;
    }

    public function setLastBuild()
    {
        /*
         * to prevent recursion, unset the reference to the lastBuild
         * and then clone
         */
        $this->lastBuild = null;
        $this->lastBuild = clone $this;
    }

    /**
     * stores the build information.
     *
     * @throws Xinc_Build_Exception_NotRun
     * @throws Xinc_Build_Exception_Serialization
     * @throws Xinc_Build_History_Exception_Storage
     *
     * @return bool
     */
    public function serialize()
    {
        Xinc_Logger::getInstance()->flush();
        $this->setLastBuild();

        if (!in_array($this->getStatus(), array(self::PASSED, self::FAILED, self::STOPPED))) {
            throw new Xinc_Build_Exception_NotRun();
        } elseif ($this->getBuildTime() == null) {
            throw new Xinc_Build_Exception_Serialization($this->getProject(),
                                                         $this->getBuildTime());
        }
        $statusDir = Xinc::getInstance()->getStatusDir();

        $buildHistoryFile = $statusDir.DIRECTORY_SEPARATOR
                          .$this->getProject()->getName().'.history';

        $subDirectory = self::generateStatusSubDir($this->getProject()->getName(), $this->getBuildTime());

        $fileName = $statusDir.DIRECTORY_SEPARATOR
                  .$subDirectory
                  .DIRECTORY_SEPARATOR.'build.ser';
        $logfileName = $statusDir.DIRECTORY_SEPARATOR
                  .$subDirectory
                  .DIRECTORY_SEPARATOR.'buildlog.xml';
        $lastBuildFileName = $statusDir.DIRECTORY_SEPARATOR.$this->getProject()->getName()
                           .DIRECTORY_SEPARATOR.'build.ser';
        $lastLogFileName = $statusDir.DIRECTORY_SEPARATOR.$this->getProject()->getName()
                           .DIRECTORY_SEPARATOR.'buildlog.xml';
        if (!file_exists(dirname($fileName))) {
            mkdir(dirname($fileName), 0755, true);
        }
        $contents = serialize($this);

        $written = file_put_contents($lastBuildFileName, $contents);
        if ($written == strlen($contents)) {
            $res = copy($lastBuildFileName, $fileName);
            if (!$res) {
                throw new Xinc_Build_Exception_Serialization($this->getProject(),
                                                             $this->getBuildTime());
            } else {
                if (file_exists($lastLogFileName)) {
                    copy($lastLogFileName, $logfileName);
                    unlink($lastLogFileName);
                }
                Xinc_Build_History::addBuild($this, $fileName);
            }

            return true;
        } else {
            throw new Xinc_Build_Exception_Serialization($this->getProject(),
                                                         $this->getBuildTime());
        }
    }

    /**
     * Unserialize a build by its project and buildtimestamp.
     *
     * @param Xincproject $project
     * @param int         $buildTimestamp
     *
     * @return Xinc_Build
     *
     * @throws Xinc_Build_Exception_Unserialization
     * @throws Xinc_Build_Exception_NotFound
     */
    public static function unserialize(Project $project, $buildTimestamp = null, $statusDir = null)
    {
        if ($statusDir == null) {
            $statusDir = Xinc::getInstance()->getStatusDir();
        }

        if ($buildTimestamp == null) {
            //$fileName = $statusDir . DIRECTORY_SEPARATOR . $project->getName()
            //          . DIRECTORY_SEPARATOR . 'build.ser';
            $fileName = Xinc_Build_History::getLastBuildFile($project);
        } else {
            //$subDirectory = self::generateStatusSubDir($project->getName(), $buildTimestamp);

            // throws Xinc_Build_Exception_NotFound
            $fileName = Xinc_Build_History::getBuildFile($project, $buildTimestamp);
        }

        //Xinc_Build_Repository::getBuild($project, $buildTimestamp);
        if (!file_exists($fileName)) {
            throw new Xinc_Build_Exception_NotFound($project,
                                                    $buildTimestamp);
        } else {
            $serializedString = file_get_contents($fileName);
            $unserialized = @unserialize($serializedString);
            if (!$unserialized instanceof Xinc_Build) {
                throw new Xinc_Build_Exception_Unserialization($project,
                                                               $buildTimestamp);
            } else {
                /*
                 * compatibility with old Xinc_Build w/o statistics object
                 */
                if ($unserialized->getStatistics() === null) {
                    $unserialized->_statistics = new Xinc_Build_Statistics();
                }
                if ($unserialized->getConfigDirective('timezone.reporting') == true) {
                    $unserialized->setConfigDirective('timezone', null);
                }
                if (!isset($unserialized->_internalProperties)) {
                    if (method_exists($unserialized, 'init')) {
                        $unserialized->init();
                    }
                }

                return $unserialized;
            }
        }
    }

    /**
     * returns the status of this build.
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the status of this build.
     *
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function __sleep()
    {
        /*
         * minimizing the storage for the project,
         * we just want the name
         */
        $project = new Project();
        $project->setName($this->getProject()->getName());
        $this->project = $project;

        return array('no', 'project', 'buildTimestamp',
                     'properties', 'status', 'lastBuild',
                     'labeler', 'engine', 'statistics', 'config',
                     'internalProperties', );
    }

    public function init()
    {
        $this->internalProperties = new Xinc_Build_Properties();
    }

    /**
     * Sets the sequence number for this build.
     *
     * @param int $no
     */
    public function setNumber($no)
    {
        $this->info('Setting Buildnumber to:'.$no);
        $this->getProperties()->set('build.number', $no);
        $this->no = $no;
    }

    /**
     * returns the build no for this build.
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->no;
    }

    /**
     * returns the label of this build.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->labeler->getLabel($this);
    }

    /**
     * returns the labeler of this build.
     *
     * @return Xinc_Build_Labeler
     */
    public function getLabeler()
    {
        return $this->labeler;
    }

    /**
     * Sets a build scheduler,
     * which calculates the next build time based
     * on the configuration.
     *
     * @param Xinc_Build_Scheduler_Interface $scheduler
     */
    public function setScheduler(Scheduler\SchedulerInterface $scheduler)
    {
        $this->scheduler = $scheduler;
    }

    /**
     * @return Xinc_Build_Scheduler_Interface
     */
    public function getScheduler()
    {
        return $this->scheduler;
    }

    /**
     * @deprecated - should be deprecated
     * @return Xinc_Build_Tasks_Registry
     */
    public function getTaskRegistry()
    {
        return $this->taskRegistry;
    }

    /**
     * processes the tasks that are registered for the slot.
     *
     * @param mixed $slot
     */
    public function process($slot)
    {
        $tasks = $this->getTaskRegistry()->getTasksForSlot($slot);
        while ($tasks->hasNext()) {
            $task = $tasks->next();
            Xinc_Logger::getInstance()->info('Processing task: '.$task->getName());
            try {
                $task->process($this);
            } catch (Exception $e) {
                var_dump($e);
            }

            /*
             * The Post-Process continues on failure
             */
            if ($slot != Xinc_Plugin_Slot::POST_PROCESS) {
                if ($this->getStatus() != Xinc_Build_Interface::PASSED) {
                    $tasks->rewind();
                    break;
                }
            }
        }
        $tasks->rewind();
    }

    /**
     * Logs a message of priority info.
     *
     * @param string $message
     */
    public function info($message)
    {
        $this->log->info('[build] '.$this->getProject()->getName()
            .': '.$message);
    }

    /**
     * Logs a message of priority warn.
     *
     * @param string $message
     */
    public function warn($message)
    {
        $this->log->warn('[build] '.$this->getProject()->getName()
            .': '.$message);
    }

    /**
     * Logs a message of priority verbose.
     *
     * @param string $message
     */
    public function verbose($message)
    {
        $this->log->verbose('[build] '.$this->getProject()->getName()
           .': '.$message);
    }

    /**
     * Logs a message of priority debug.
     *
     * @param string $message
     */
    public function debug($message)
    {
        $this->log->debug('[build] '.$this->getProject()->getName()
             .': '.$message);
    }

    /**
     * Logs a message of priority error.
     *
     * @param string $message
     */
    public function error($message)
    {
        $this->log->error('[build] '.$this->getProject()->getName()
            .': '.$message);
    }

    public function build()
    {
        Xinc_Logger::getInstance()->setBuildLogFile(null);
        Xinc_Logger::getInstance()->emptyLogQueue();
        Xinc::setCurrentBuild($this);

        $buildLogFile = Xinc::getInstance()->getStatusDir()
                        .DIRECTORY_SEPARATOR
                        .$this->getProject()->getName()
                        .DIRECTORY_SEPARATOR
                        .'buildlog.xml';
        if (file_exists($buildLogFile)) {
            self::info('Removing old logfile "'.$buildLogFile.'" with size: '.filesize($buildLogFile));
            unlink($buildLogFile);
        }
        Xinc_Logger::getInstance()->setBuildLogFile($buildLogFile);

        $this->getEngine()->build($this);
        //Xinc_Logger::getInstance()->flush();
        Xinc_Logger::getInstance()->setBuildLogFile(null);

        if (Xinc_Build_Interface::STOPPED != $this->getStatus()) {
            $this->setStatus(Xinc_Build_Interface::INITIALIZED);
        }
    }

    public function updateTasks()
    {
        $this->setters = Xinc_Plugin_Repository::getInstance()->getTasksForSlot(Xinc_Plugin_Slot::PROJECT_SET_VALUES);

        $this->getProperties()->set('project.name', $this->getProject()->getName());
        $this->getProperties()->set('build.number', $this->getNumber());
        $this->getProperties()->set('build.label', $this->getLabel());

        $builtinProps = Xinc::getInstance()->getBuiltinProperties();

        foreach ($builtinProps as $prop => $value) {
            $this->getProperties()->set($prop, $value);
        }

        $tasks = $this->getTaskRegistry()->getTasks();

        while ($tasks->hasNext()) {
            $task = $tasks->next();

            $this->_updateTask($task);
        }
    }

    public static function generateStatusSubDir($projectName, $buildTime)
    {
        $oldTimeZone = ini_get('date.timezone');
        if (Xinc_Timezone::getIniTimezone() == null) {
            ini_set('date.timezone', 'UTC');
        }
        $yearMonthDay = date('Ymd', $buildTime);
        $subDirectory = $projectName;
        $subDirectory .= DIRECTORY_SEPARATOR;
        $subDirectory .= $yearMonthDay.DIRECTORY_SEPARATOR.$buildTime;
        if (Xinc_Timezone::getIniTimezone() == null) {
            ini_set('date.timezone', $oldTimeZone);
        }

        return $subDirectory;
    }

    public function getStatusSubDir()
    {
        $subDirectory = self::generateStatusSubDir($this->getProject()->getName(), $this->getBuildTime());

        return $subDirectory;
    }

    private function _updateTask(Xinc_Plugin_Task_Interface &$task)
    {
        $element = $task->getXml();
        foreach ($element->attributes() as $name => $value) {
            $setter = 'set'.$name;

            /*
             * Call PROJECT_SET_VALUES plugins
             */
            while ($this->_setters->hasNext()) {
                $setterObj = $this->_setters->next();
                $value = $setterObj->set($this, $value);
            }
            $this->_setters->rewind();
            $task->$setter((string) $value, $this);
        }

        $subtasks = $task->getTasks();

        while ($subtasks->hasNext()) {
            $this->_updateTask($subtasks->next());
        }
    }

    public function enqueue()
    {
        $this->isQueued = true;
    }

    /**
     * check if build is in queue mode.
     */
    public function isQueued()
    {
        return $this->isQueued;
    }

    /**
     * remove build from queue mode.
     */
    public function dequeue()
    {
        $this->isQueued = false;
    }

    /**
     * Sets custom config value for the current build.
     *
     * @param string $name
     * @param string $value
     */
    public function setConfigDirective($name, $value)
    {
        $this->config[$name] = $value;
    }
    /**
     * Returns the configuration directive for the name.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getConfigDirective($name)
    {
        return isset($this->_config[$name]) ? $this->_config[$name] : null;
    }

    public function resetConfigDirective()
    {
        $this->config = array();
    }
}
