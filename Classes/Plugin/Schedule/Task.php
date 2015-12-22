<?php
/**
 * Xinc - Continuous Integration.
 *
 * @category  Development
 * @package   Xinc.Plugin.Schedule
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
 * @homepage  http://code.google.com/p/xinc/
 */

namespace Xinc\Core\Plugin\Schedule;

use Xinc\Core\Build\BuildInterface;
use Xinc\Core\Task\Base;
use Xinc\Core\Task\Slot;
use Xinc\Core\Build\Scheduler\SchedulerInterface;

class Task extends Base implements SchedulerInterface
{

    private $_interval;
    
    /**
     * Enter description here...
     *
     * @var Xinc_Build_Interface
     */
    private $_build;

    public function process(BuildInterface $build)
    {
        /**if (!isset($this->_project)) {
            $build->setScheduler($this);
            $this->_build = $build;
            if (time() < $this->getNextBuildTime()) {
                $this->_build->setStatus(Xinc_Build_Interface::STOPPED);
            }
        }*/
    }

    public function setInterval($interval)
    {
        $this->_interval = $interval;
    }
    
    public function getInterval()
    {
        return $this->_interval;
    }
    
    public function registerTask(Xinc_Plugin_Task_Interface $task)
    {
        
    }
    
    public function setLastBuildTime($time)
    {
        
    }
    
    public function init(BuildInterface $build)
    {
        $build->setScheduler($this);
    }
    
    public function getNextBuildTime(BuildInterface $build)
    {
        if ($build->getStatus() == BuildInterface::STOPPED) {
            return null;
        }
        //var_dump($build);
        $lastBuild = $build->getLastBuild()->getBuildTime();
        
        if ($lastBuild != null ) {
            $nextBuild = $this->getInterval() + $lastBuild;
            /**
             * Make sure that we dont rerun every build if the daemon was paused
             */
            //echo time(). ' - ' . $lastBuild .'='.(time()-$lastBuild)."\n";
            if ($nextBuild + $this->getInterval() < time()) {
                
                $nextBuild = time();
            }
        } else {
            // never ran, schedule for now
            $nextBuild = time();
        }
        $build->debug('getNextBuildTime '
                              . ': lastbuild: ' 
                              . date('Y-m-d H:i:s', $lastBuild) 
                              . ' nextbuild: ' 
                              . date('Y-m-d H:i:s', $nextBuild).'');
        return $nextBuild;
    }

    public function getPluginSlot()
    {
        return Slot::INIT_PROCESS;
    }

    public function validate()
    {
        return $this->_interval > 0;
    }

    public function getName()
    {
        return 'schedule';
    }
}
