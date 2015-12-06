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

use Xinc\Core\Logger;
use Xinc\Core\Config\Config;
use Xinc\Core\Config\Xml as ConfigXml;
use Xinc\Core\Project\Config\Xml as ProjectXml;
use Xinc\Core\Registry\Registry;

use Xinc\Core\Exception\ClassLoaderException;
use Xinc\Core\Exception\IOException;
use Xinc\Core\Exception\XmlException;

/**
 * @test Test Class for loading a xml configuration
 */
class TestProjectPropertyConfig extends Xinc\Core\Test\BaseTest
{
	public function projectXml($conf,&$reg = null)
	{
		$xml = new ConfigXml;
		$log = new Logger();
		$log->setLoglevel(0);
		$xml->setLogger($log);
		$reg = new Registry();
		$reg->setLogger($log);
		$xml->load($conf,$reg);
		$pro = new ProjectXml();
		$pro->setLogger($log);
		return $pro;
	}
	 
	public function testPlugins2()
	{
		$conf = new Config();
	    $conf->setOption('config-file', __DIR__ . '/config/plugins2.xml');
	    $conf->setOption('project-file', __DIR__ . '/config/project-property.xml');
	    
	    $this->projectXml($conf,$reg)->load($conf,$reg);
	 }
}
