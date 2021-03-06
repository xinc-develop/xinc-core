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
 
use Xinc\Core\Config\Config;
use Xinc\Core\Config\ConfigException;

/**
 * @test Test Class for Xinc::Core::Iterator and subclasses
 */
class TestConfig extends Xinc\Core\Test\BaseTest
{
    public function testOptions()
    {
		$conf = new Config();
		$fn = './config/start.xml';
		$conf->setOption('config-file',$fn);
		$this->assertEquals($fn,$conf->getOption('config-file'));
		$this->assertEquals($fn,$conf->getOption('configfile'));
		$this->assertEquals($fn,$conf->get('config-file'));
		$this->assertEquals($fn,$conf->get('configfile'));
		$this->assertTrue(is_array($conf->getOptions()));
    }
    
    public function testGetException()
    {
		$conf = new Config();
		try {
			$conf->getOption('not-set');
			$this->assertTrue(False,'unknown option should not be ignored');
		}
		catch(ConfigException $e) {
			$this->assertTrue(true,'Expected exception thrown');
		}
	}
}
