<?php
/**
 * Xinc - Continuous Integration.
 *
 * @author    Arno Schneider
 * @author    Sebastian Knapp <news@young-workers.de>
 * @copyright 2007 Arno Schneider, Barcelona
 * @copyright 2015 Xinc Development Team, https://github.com/xinc-develop/
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
 * @homepage  https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core\Config;

use SimpleXMLElement as XmlElement;
use Xinc\Core\Config\ConfigInterface;
use Xinc\Core\Config\ConfigLoaderInterface;
use Xinc\Core\Registry\XincRegistryInterface;

use Xinc\Core\Exception\IOException;
use Xinc\Core\Exception\XmlException;

/**
 * Xinc System Configuration File in XML Format
 */
class Xml extends Loader implements ConfigLoaderInterface
{
    public function load(ConfigInterface $conf, XincRegistryInterface $reg)
    {
        $file = $conf->getOption('config-file');
        if(isset($file)) {
		    if(!strstr($file,'/')) {
				$file = $conf->getOption('config-dir') . $file;
			}
			$this->loadFile($file,$conf,$reg);	
		}
		// load every xml file in config dir
		else {
			 $dir = $conf->getOption('config-dir');
			 $list = glob("{$dir}*.xml");
			 if($list === false) {
				 throw new IOException($dir,null,null,IOException::FAILURE_NOT_READABLE);
		     }
		     if(empty($list)) {
                 throw new IOException($dir,null,null,IOException::FAILURE_NOT_FOUND);
             }
             foreach($list as $file) {
				 $this->loadFile($file,$conf,$reg);
			 }
		}
	}
        
    public function loadFile($file,$conf,$reg)
    {   
        if (!file_exists($file)) {
            throw new IOException($file,null,null,IOException::FAILURE_NOT_FOUND);
        } 
        libxml_use_internal_errors(true);
        $this->log->verbose("Loading configuration file $file");
        $xml = simplexml_load_file($file);
        
        if(!$xml) {
            throw new XmlException(libxml_get_errors());
	    }
	    $this->loadSettings($xml,$conf);
	    $this->loadPlugins($xml,$reg);
	    $this->loadEngines($xml,$reg);
    }
    
    protected function loadSettings($xml,$conf)
    {
		foreach($xml->xpath('/xinc/configuration/setting') as $element) {
	        $conf->setSetting("{$element['name']}","{$element['value']}");		
		}
	}
	
	protected function loadPlugins($xml,$reg)
	{
		foreach($xml->xpath('/xinc/plugins') as $element) {
			$plugins = $element->xpath('plugin');

			if(isset($element['namespace'])) {
				foreach($plugins as $plugin) {
				    $class = join('\\',array("{$element['namespace']}",
				        "{$plugin['name']}",'Plugin'));
				    $this->log->verbose("Looking for plugin class $class.");
                    $reg->registerPluginClass($class);	
				}
			}
			else {
				foreach($plugins as $plugin) {
					$this->log->verbose("found plugin class {$plugin['class']}"); 
			        $reg->registerPluginClass("{$plugin['class']}");
			    }
			}
		}
	}
	
	protected function loadEngines($xml,$reg)
	{
		foreach($xml->xpath('/xinc/engines/engine') as $engine) {
		    $this->log->verbose("found engine class {$engine['class']}"); 
			$reg->registerEngineClass("{$engine['class']}","{$engine['default']}");	        		
		}
	}
}
