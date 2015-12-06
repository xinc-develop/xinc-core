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
use Xinc\Core\Config\Xml;
use Xinc\Core\Registry\Registry;

use Xinc\Core\Exception\ClassLoaderException;
use Xinc\Core\Exception\IOException;
use Xinc\Core\Exception\XmlException;

/**
 * @test Test Class for loading a xml configuration
 */
class TestProjectPropertyConfig extends Xinc\Core\Test\BaseTest
{
	public function xml(&$reg = null)
	{
		$xml = new Xml;
		$log = new Logger();
		$log->setLoglevel(0);
		$xml->setLogger($log);
		$reg = new Registry();
		$reg->setLogger($log);
		return $xml;
	}
	
	public function testFileNotFound()
	{
	    $conf = new Config();
	    $conf->setOption('config-file','./x-files-unknown.xml');
	    try {
			$this->xml()->load($conf,(new Registry));
			$this->assertTrue(false,'IO exception expected');
		}
		catch(IOException $e) {
			$this->assertTrue(true,'Exception: ' . $e->getMessage());
		}	
	}

	public function testXmlError()
	{
	    $conf = new Config();
	    $conf->setOption('config-file', __DIR__ . '/config/broken-settings.xml');
	    try {
			$this->xml()->load($conf,(new Registry));
			$this->assertTrue(false,'XML exception expected');
		}
		catch(XmlException $e) {
			$this->assertTrue(true,'Exception: ' . $e->getMessage());
		}	
	}
	
    public function testSampleSettings()
    {
	    $conf = new Config();
	    $conf->setOption('config-file', __DIR__ . '/config/sample-settings.xml');
	    
	    $this->xml()->load($conf,(new Registry));
	    $this->assertEquals($conf->get('heartbeat'),30);
	    $this->assertEquals($conf->get('timezone'),'Europe/Berlin');
	    $this->assertEquals($conf->get('loglevel'),2);	
	}
	
	
	public function testUnknownPluginClass()
	{
		$conf = new Config();
	    $conf->setOption('config-file', __DIR__ . '/config/unknown-plugin.xml');
	    
	    try {
	        $this->xml($reg)->load($conf,$reg);
	        $this->assertTrue(false,"Unknown plugin class should throw exception");
	    }
	    catch(ClassLoaderException $exp) {
			$this->assertTrue(true,"Unknown plugin throws exception");
		}
    }

	public function testPlugins()
	{
		$conf = new Config();
	    $conf->setOption('config-file', __DIR__ . '/config/plugins.xml');
	    
	    $this->xml($reg)->load($conf,$reg);
	 }
	 
	 
	public function testPlugins2()
	{
		$conf = new Config();
	    $conf->setOption('config-file', __DIR__ . '/config/plugins2.xml');
	    
	    $this->xml($reg)->load($conf,$reg);
	 }
}
