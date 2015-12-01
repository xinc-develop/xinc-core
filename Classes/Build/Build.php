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
 * @link      https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core\Build;

use Xinc\Core\Build\BuildInterface;
use Xinc\Core\Engine\EngineInterface;
use Xinc\Core\Models\Project;

use Xinc\Core\Project\Status as ProjectStatus;
use Xinc\Core\Properties as BuildProperties;

use Xinc\Core\Build\Exception\NotRun;
use Xinc\Core\Build\Exception\NotFound;
use Xinc\Core\Build\Exception\Serialization;

/**
 * This class represents the build that is going to be run with Xinc
 */
class Build implements BuildInterface
{
    /**
     * Are we queued?
     *
     * @var boolean
     */
    private $isQueued=false;
    
    /**
     * @var Xincengine_Interface
     */
    private $engine;
    
    /**
     * @var Xincproject
     */
    private $project;
    
    /**
     * @var Xinc_Build_Properties
     */
    private $_properties;
    
    /**
     * @var Xinc_Build_Properties
     */
    private $_internalProperties;
    
    /**
     *
     * @var Xinc_Build_Statistics
     */
    private $_statistics;
    
    /**
     * 
     *
     * @var integer
     */
    private $_buildTimestamp;
    
    /**
     * 
     *
     * @var integer
     */
    private $_nextBuildTimestamp;
    
    /**
     * Build status, as defined in Xinc_Build_Interface
     *
     * @var integer
     */
    private $_status;
    
    /**
     *
     * @var Xinc_Build_Interface
     */
    private $_lastBuild;
    
    /**
     * The build no of this build
     *
     * @var integer
     */
    private $_no;
    
    /**
     * The label for this build
     *
     * @var string
     */
    private $_label;
    
    /**
     * Contains tasks that need to be executed for each Process Step
     *
     * @var Xinc_Build_Tasks_Registry
     */
    private $_taskRegistry;
    
    /**
     * Build scheduler
     *
     * @var Xinc_Build_Scheduler_Interface
     */
    private $_scheduler;
    
    /**
     * @var Xinc_Build_Labeler_Interface
     */
    private $_labeler;
    
    /**
     * Holding config values for this build
     *
     * @var array
     */
    private $_config = array();
    
    /**
     * sets the project, engine
     * and timestamp for the build
     *
     * @param Xincengine_Interface $engine
     * @param Xincproject          $project
     * @param integer               $buildTimestamp
     */
    public function __construct(EngineInterface $engine,
                                Project $project,
                                $buildTimestamp = null
    ) {
        $this->engine = $engine;
        $this->project = $project;
        
        if (ProjectStatus::MISCONFIGURED == $this->project->getStatus()) {
            $this->setStatus(BuildInterface::MISCONFIGURED);
        }
        
        $this->_buildTimestamp = $buildTimestamp;
        $this->_properties = new BuildProperties();
        $this->_internalProperties = new BuildProperties();
        $this->_statistics = new Statistics();
        $this->setLabeler(new Labeler\DefaultLabeler());
        $this->setScheduler(new Scheduler\DefaultScheduler());
    }

    public function setLabeler(Labeler\LabelerInterface $labeler)
    {
        $this->_labeler = $labeler;
    }

    /**
     * 
     * Returns the last build
     * @return Xinc_Build_Interface
     */
    public function &getLastBuild()
    {
        if ($this->_lastBuild == null) { 
            $build = new Xinc_Build($this->getEngine(), $this->getProject());
            return $build;
        }
        return $this->_lastBuild;
    }

    /**
     *
     * @return Xinc_Build_Properties
     */
    public function &getProperties()
    {
        return $this->_properties;
    }
    
    /**
     *
     * @return Xinc_Build_Properties
     */
    public function &getInternalProperties()
    {
        return $this->_internalProperties;
    }
    
    /**
     * @return Xinc_Build_Statistics
     */
    public function &getStatistics()
    {
        return $this->_statistics;
    }
    /**
     * sets the build time for this build
     *
     * @param integer $buildTime unixtimestamp
     */
    public function setBuildTime($buildTime)
    {
        $this->getProperties()->set('build.timestamp', $buildTime);
        $this->_buildTimestamp = $buildTime;
    }

    /**
     * returns the timestamp of this build
     *
     * @return integer Timestamp of build (unixtimestamp)
     */
    public function getBuildTime()
    {
        return $this->_buildTimestamp;
    }
    
    /**
     * Returns the next build time (unix timestamp)
     * for this build
     *
     */
    public function getNextBuildTime()
    {
        return $this->_scheduler->getNextBuildTime($this);
    }

    /**
     * 
     * @return Xincproject
     */
    public function &getProject()
    {
        return $this->project;
    }
    
    /**
     * 
     * @return Xincengine_Interface
     */
    public function &getEngine()
    {
        return $this->engine;
    }
    
    public function setLastBuild()
    {
        /**
         * to prevent recursion, unset the reference to the lastBuild
         * and then clone
         */
        $this->_lastBuild = null;
        $this->_lastBuild = clone $this;
    }
    
    /**
     * stores the build information
     *
     * @throws Xinc_Build_Exception_NotRun
     * @throws Xinc_Build_Exception_Serialization
     * @throws Xinc_Build_History_Exception_Storage
     * @return boolean
     */
    public function serialize()
    {
        Xinc_Logger::getInstance()->flush();
        $this->setLastBuild();
        
        if (!in_array($this->getStatus(), array(self::PASSED, self::FAILED, self::STOPPED))) {
            throw new Xinc_Build_Exception_NotRun();
        } else if ($this->getBuildTime() == null) {
            throw new Xinc_Build_Exception_Serialization($this->getProject(),
                                                         $this->getBuildTime());
        }
        $statusDir = Xinc::getInstance()->getStatusDir();
        
        $buildHistoryFile = $statusDir . DIRECTORY_SEPARATOR 
                          . $this->getProject()->getName() . '.history';
        
        $subDirectory = self::generateStatusSubDir($this->getProject()->getName(), $this->getBuildTime());

        $fileName = $statusDir . DIRECTORY_SEPARATOR 
                  . $subDirectory
                  . DIRECTORY_SEPARATOR . 'build.ser';
        $logfileName = $statusDir . DIRECTORY_SEPARATOR 
                  . $subDirectory
                  . DIRECTORY_SEPARATOR . 'buildlog.xml';
        $lastBuildFileName = $statusDir . DIRECTORY_SEPARATOR . $this->getProject()->getName()
                           . DIRECTORY_SEPARATOR . 'build.ser';
        $lastLogFileName = $statusDir . DIRECTORY_SEPARATOR . $this->getProject()->getName()
                           . DIRECTORY_SEPARATOR . 'buildlog.xml';
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
     * Unserialize a build by its project and buildtimestamp
     *
     * @param Xincproject $project
     * @param integer $buildTimestamp
     *
     * @return Xinc_Build
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
        
        
            /**
             * @throws Xinc_Build_Exception_NotFound
             */
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
                /**
                 * compatibility with old Xinc_Build w/o statistics object
                 */
                if ($unserialized->getStatistics() === null) {
                    $unserialized->_statistics = new Xinc_Build_Statistics();
                }
                if ($unserialized->getConfigDirective('timezone.reporting') == true) {
                    $unserialized->setConfigDirective('timezone', null);
                }
                if (!isset($unserialized->_internalProperties)) {
                    if (method_exists($unserialized,'init')) {
                        $unserialized->init();
                    }
                    
                }
                return $unserialized;
            }
        }
    }
    
    /**
     * returns the status of this build
     *
     */
    public function getStatus()
    {
        return $this->_status;
    }
    
    /**
     * Set the status of this build
     *
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->_status = $status;
    }
    
    
    public function __sleep()
    {
        /**
         * minimizing the storage for the project,
         * we just want the name
         */
        $project = new Project();
        $project->setName($this->getProject()->getName());
        $this->project = $project;
        if (!isset($this->_config['timezone'])) {
            /**
             * if no timezone was configured in the project xml, we store the
             * used timezone and mark it as only a reporting timezone,
             * like this the timezone will not get restored for future
             * builds, but we have it for the record
             */
            $this->_config['timezone'] = Xinc_Timezone::get();
            $this->_config['timezone.reporting'] = true;
        }
        
        return array('_no','project', '_buildTimestamp',
                     '_properties', '_status', '_lastBuild',
                     '_labeler', 'engine', '_statistics', '_config',
                     '_internalProperties');
    }
    
    public function init()
    {
        $this->_internalProperties = new Xinc_Build_Properties();
    }

    /**
     * Sets the sequence number for this build
     *
     * @param integer $no
     */
    public function setNumber($no)
    {
        $this->info('Setting Buildnumber to:' . $no);
        $this->getProperties()->set('build.number', $no);
        $this->_no = $no;
    }
    
    /**
     * returns the build no for this build
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->_no;
    }
    
    
    /**
     * returns the label of this build
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->_labeler->getLabel($this);
    }
    
    /**
     * returns the labeler of this build
     *
     * @return Xinc_Build_Labeler
     */
    public function getLabeler()
    {
        return $this->_labeler;
    }
    
    /**
     *
     * @param Xinc_Build_Tasks_Registry $taskRegistry
     */
    public function setTaskRegistry(Xinc_Build_Tasks_Registry $taskRegistry)
    {
        $this->_taskRegistry = $taskRegistry;
    }

    /**
     * Sets a build scheduler,
     * which calculates the next build time based
     * on the configuration
     *
     * @param Xinc_Build_Scheduler_Interface $scheduler
     */
    public function setScheduler(Xinc_Build_Scheduler_Interface &$scheduler)
    {
        $this->_scheduler = $scheduler;
    }
    
    /**
     *
     * @return Xinc_Build_Scheduler_Interface
     */
    public function getScheduler()
    {
        return $this->_scheduler;
    }

    /**
     * @return Xinc_Build_Tasks_Registry
     *
     */
    public function getTaskRegistry()
    {
        return $this->_taskRegistry;
    }

    /**
     * processes the tasks that are registered for the slot
     *
     * @param mixed $slot
     */
    public function process($slot)
    {
        $tasks = $this->getTaskRegistry()->getTasksForSlot($slot);
        while ($tasks->hasNext()) {
            
            $task = $tasks->next();
            Xinc_Logger::getInstance()->info('Processing task: ' . $task->getName());
            try {
                $task->process($this);
            } catch (Exception $e) {
                var_dump($e);
            }

            /**
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
     * Logs a message of priority info
     *
     * @param string $message
     */
    public function info($message)
    {
        Xinc_Logger::getInstance()->info('[build] ' 
                                        . $this->getProject()->getName() 
                                        . ': '.$message);
            
    }

    /**
     * Logs a message of priority warn
     *
     * @param string $message
     */
    public function warn($message)
    {
        Xinc_Logger::getInstance()->warn('[build] ' 
                                        . $this->getProject()->getName() 
                                        . ': '.$message);
            
    }
    
    /**
     * Logs a message of priority verbose
     *
     * @param string $message
     */
    public function verbose($message)
    {
        Xinc_Logger::getInstance()->verbose('[build] ' 
                                        . $this->getProject()->getName() 
                                        . ': '.$message);
            
    }

    /**
     * Logs a message of priority debug
     *
     * @param string $message
     */
    public function debug($message)
    {
        Xinc_Logger::getInstance()->debug('[build] ' 
                                         . $this->getProject()->getName() 
                                         . ': '.$message);
            
    }

    /**
     * Logs a message of priority error
     *
     * @param string $message
     */
    public function error($message)
    {
        Xinc_Logger::getInstance()->error('[build] ' 
                                         . $this->getProject()->getName()
                                         . ': '.$message);
            
    }
    
    public function build()
    {
        Xinc_Logger::getInstance()->setBuildLogFile(null);
        Xinc_Logger::getInstance()->emptyLogQueue();
        Xinc::setCurrentBuild($this);
        
        $buildLogFile = Xinc::getInstance()->getStatusDir() 
                        . DIRECTORY_SEPARATOR 
                        . $this->getProject()->getName()
                        . DIRECTORY_SEPARATOR
                        . 'buildlog.xml';
        if (file_exists($buildLogFile)) {
            self::info('Removing old logfile "' . $buildLogFile . '" with size: ' . filesize($buildLogFile));
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
        $this->_setters = Xinc_Plugin_Repository::getInstance()->getTasksForSlot(Xinc_Plugin_Slot::PROJECT_SET_VALUES);
        
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
        $yearMonthDay = date("Ymd", $buildTime);
        $subDirectory = $projectName;
        $subDirectory .= DIRECTORY_SEPARATOR;
        $subDirectory .= $yearMonthDay . DIRECTORY_SEPARATOR . $buildTime;
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
            
            /**
             * Call PROJECT_SET_VALUES plugins
             */
            while ($this->_setters->hasNext()) {
                $setterObj = $this->_setters->next();
                $value = $setterObj->set($this, $value);
                
            }
            $this->_setters->rewind();
            $task->$setter((string)$value, $this);
            
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
     * check if build is in queue mode
     *
     */
    public function isQueued()
    {
        return $this->isQueued;
    }
    
    /**
     * remove build from queue mode
     *
     */
    public function dequeue()
    {
        $this->isQueued = false;
    }
    
    /**
     * Sets custom config value for the current build
     *
     * @param string $name
     * @param string $value
     */
    public function setConfigDirective($name, $value)
    {
        $this->_config[$name] = $value;
    }
    /**
     * Returns the configuration directive for the name
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getConfigDirective($name)
    {
        return isset($this->_config[$name])?$this->_config[$name]:null;
    }
    
    public function resetConfigDirective()
    {
        $this->_config = array();
    }
}