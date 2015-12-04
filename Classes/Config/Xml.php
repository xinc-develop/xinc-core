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
 * @link      https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core\Config;

use SimpleXMLElement as XmlElement;
use Xinc\Core\Config\ConfigInterface;
use Xinc\Core\Config\ConfigLoaderInterface;

use Xinc\Core\Exception\IOException;
use Xinc\Core\Exception\XmlException;

use Xinc\Core\Exception\MalformedConfigException;

/**
 * Xinc System Configuration File in XML Format
 */
class Xml extends Loader implements ConfigLoaderInterface
{
	
    public function load(ConfigInterface $conf)
    {
        $file = $conf->getOption('config-file');
        if(isset($file)) {
		    if(!strstr($file,'/')) {
				$file = $conf->getOption('config-dir') . $file;
			}
			$this->loadFile($file,$conf);	
		}
		// load every xml file in config dir
		else {
			 $dir = $conf->getOption('config-dir');
			 $list = glob("{$dir}*.xml");
			 if($list === false) {
				 
		     }
		}
	}
        
    public function loadFile($file,$conf)
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
    }
    
    protected function loadSettings($xml,$conf)
    {
		foreach($xml->xpath('/xinc/configuration/setting') as $element) {
	        $conf->setSetting("{$element['name']}","{$element['value']}");		
		}
	}
}
