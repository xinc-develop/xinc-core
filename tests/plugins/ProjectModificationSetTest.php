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

use Xinc\Core\Exception\MalformedConfigException;
use Xinc\Core\Build\BuildInterface;
use Xinc\Core\Task\Slot;

/**
 * @test Test Class for loading a xml configuration
 */
class TestProjectModificationSet extends Xinc\Core\Test\BaseTest
{	 
	public function testProjectBuildAlways()
	{
		$conf = $this->defaultConfig();
	    $conf->setOption('config-file', __DIR__ . '/../config/plugins3.xml');
	    $conf->setOption('project-file', __DIR__ . '/../config/project-buildalways.xml');
	    
	    $this->projectXml($conf,$reg)->load($conf,$reg);
	    $build2 = $this->aBuildWithConfig($conf);
	    $build2->process(Slot::PRE_PROCESS);
	    
	    $this->assertEquals($build2->getStatus(), BuildInterface::PASSED);
	 }
	 
	 
	public function testErrorBuildAlways()
	{
		$conf = $this->defaultConfig();
	    $conf->setOption('config-file', __DIR__ . '/../config/plugins3.xml');
	    $conf->setOption('project-file', __DIR__ . '/../config/error-buildalways.xml');
	    
	    $this->projectXml($conf,$reg)->load($conf,$reg);
	    try {
	        $build2 = $this->aBuildWithConfig($conf);
	        $this->assertTrue(false,'Should throw exception');
	    }
	    catch(MalformedConfigException $e) {
			$this->assertTrue(true,"Exception: " . $e->getMessage());
		}
	 }
}
