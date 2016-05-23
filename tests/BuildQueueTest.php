<?php
/*
 * @author Arno Schneider
 * @author Sebastian Knapp
 * @version 3.0
 * @copyright 2007 Arno Schneider, Barcelona
 * @copyright 2015-2016 Xinc Development Team, https://github.com/xinc-develop/
 * @license  http://www.gnu.org/copyleft/lgpl.html GNU/LGPL, see license.php
 *    This file is part of Xinc.
 *    Xinc is free software; you can redistribute it and/or modify
 *    it under the terms of the GNU Lesser General Public License as published
 *    by the Free Software Foundation; either version 2.1 of the License, or    
 *    (at your option) any later version.
 *
 *    Xinc is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Lesser General Public License for more details.
 *
 *    You should have received a copy of the GNU Lesser General Public License
 *    along with Xinc, write to the Free Software
 *    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

use Xinc\Core\Build\Build;
use Xinc\Core\Build\BuildQueue;
use Xinc\Core\Build\BuildIterator;
use Xinc\Core\Build\Scheduler\DefaultScheduler;
use Xinc\Core\Project\Project;
use Xinc\Core\Test\BaseTest;
use Xinc\Core\Test\Engine;

/**
 * @test Test class for Xinc::Core::Build::BuildQueue
 */
class BuildQueueTest extends BaseTest
{
    public function testOneBuildToBuild()
    {
        $build = new Build(new Engine(),new Project());
        $queue = new BuildQueue();
        $scheduler = new DefaultScheduler();
        
        $build->setScheduler($scheduler);
        $queue->addBuild($build);
        $nextBuildTime = $queue->getNextBuildTime();
        
        $this->assertTrue($nextBuildTime != null, 
            'We should have a default builttime');
        $nextBuild = $queue->getNextBuild();
        $this->assertEquals($build, $nextBuild, 'The Builds should be equal');
    }

    public function testOneBuildToBuildAddBuilds()
    {
        $build = new Build(new Engine(),new Project());
        $queue = new BuildQueue();
        $scheduler = new DefaultScheduler();
        
        $build->setScheduler($scheduler);
        $queue->addBuild($build);
        
        $nextBuildTime = $queue->getNextBuildTime();
        $this->assertTrue($nextBuildTime != null, 
            'We should have a default builttime');
        
        $nextBuild = $queue->getNextBuild();
        $this->assertEquals($build, $nextBuild, 'The Builds should be equal');
    }
   
}
