<?php
/**
 * @version 3.0
 * @author Arno Schneider
 * @author Sebastian Knapp
 * @copyright 2007 Arno Schneider, Barcelona
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

use Xinc\Core\Config\Config;
use Xinc\Core\Project\Project;
use Xinc\Core\Project\Status as ProjectStatus;

use Xinc\Core\Test\BaseTest;
/**
 * @test Test class for Xinc::Core::Project::Project
 */
class TestProject extends BaseTest
{
//! @{  
    public function testSetters()
    {
        $project = new Project();
        
        $name = 'test ' . rand(21213,123213);
        $project->setName($name);        
        $this->assertEquals($name, $project->getName(), 'Names should match');
        
        $status = ProjectStatus::MISCONFIGURED;
        $project->setStatus($status);
        
        $this->assertEquals($status, $project->getStatus(), 'Stati should match');
    }


	public function testProjectFromConfig1()
	{
		$conf = new Config();
	    $conf->setOption('config-file', __DIR__ . '/config/plugins2.xml');
	    $conf->setOption('project-file', __DIR__ . '/config/project-property.xml');
	    
	    $this->projectXml($conf,$reg)->load($conf,$reg);
	    $project = $reg->getProject("TestProjectProperty");
	    $this->assertInstanceOf('Xinc\Core\Project\Project',$project);
	    $iterator = $reg->getProjectIterator();
	    $this->assertInstanceOf('ArrayIterator',$iterator);
	    $project2 = $iterator->current();
	    $this->assertSame($project,$project2);
	    $engine = $reg->getEngine($project->getEngineName());
	    $this->assertInstanceOf('Xinc\Core\Engine\EngineInterface',$engine);
	 }
	 
	public function testProjectWithoutName()
	{
		$conf = new Config();
	    $conf->setOption('config-file', __DIR__ . '/config/plugins2.xml');
	    $conf->setOption('project-file', __DIR__ . '/config/project-anonym.xml');
	    
	    $this->projectXml($conf,$reg)->load($conf,$reg);
	    
	    $projects = $reg->getProjectIterator();
	    $this->assertEquals(1,$projects->count());
	    $project = $projects->current();
	    
	    $this->assertEquals('3ff74917e5698d0e418b55b592416df2',$project->getName());
	 }
//! @}
}
