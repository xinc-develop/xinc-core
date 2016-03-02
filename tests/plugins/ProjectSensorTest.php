<?php
/**
 * @version 3.0
 * @author Sebastian Knapp
 * @copyright 2015 Xinc Development Team, https://github.com/xinc-develop/
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


use Xinc\Core\Plugin\Schedule\Plugin;
use Xinc\Core\Plugin\Schedule\Task;

/**
 * @test Test Class for loading a xml configuration
 */
class TestProjectSensor extends Xinc\Core\Test\BaseTest
{	 
	public function testProjectTriggerSensor()
	{
		$conf = $this->defaultConfig();
		$conf->setOption('working-dir', __DIR__ . '/../temp','working-dir');
		if(!is_dir($conf->getOption('working-dir'))) {
			mkdir($conf->getOption('working-dir'));
		}
	    $conf->setOption('config-file', __DIR__ . '/../config/plugins2.xml');
	    $conf->setOption('project-file', __DIR__ . '/../config/project-sensor.xml');
	    
	    $this->projectXml($conf,$reg)->load($conf,$reg);
	    $build2 = $this->aBuildWithConfig($conf);
	    $scheduler = $build2->getScheduler();
        $this->assertInstanceOf('Xinc\Core\Plugin\Schedule\Sensor',$scheduler);
        $this->assertNull($scheduler->getNextBuildTime($build2));
        $this->assertEquals("trigger.project",$scheduler->getFile());
	 }
	 
	 public function XestTaskValidation()
	 {
		  $ok = new Task(new Plugin());
		  $ok->setInterval(55);
		  $this->assertTrue($ok->validate());
		  
		  $default = new Task(new Plugin());
		  $this->assertTrue($default->validate());
		  $this->assertEquals(60,$default->getInterval());
		  
		  $wrong = new Task(new Plugin());
		  $wrong->setInterval('daily');
		  $this->assertFalse($wrong->validate($msg));
		  $this->assertEquals('Invalid schedule interval value \'daily\'',$msg);
     }		 
}
