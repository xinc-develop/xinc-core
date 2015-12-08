<?php
/**
 * Base test
 * 
 * @author Sebastian Knapp
 * @version 3.0
 * @copyright 2007 Arno Schneider, Barcelona
 * @copyright 2015 Xinc Developer, Leipzig
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

namespace Xinc\Core\Test;

use Xinc\Core\Build\Build;
use Xinc\Core\Logger;
use Xinc\Core\Config\Config;
use Xinc\Core\Config\Xml as ConfigXml;
use Xinc\Core\Project\Config\Xml as ProjectXml;
use Xinc\Core\Registry\Registry;

abstract class BaseTest extends \PHPUnit_Framework_TestCase
{
	public function xincLoglevel()
	{
		$loglevel = getenv('XINC_LOGLEVEL');
		if(FALSE===$loglevel) {
			$loglevel = Logger::INFO;
		}
		return $loglevel;		
	}
	
	public function projectXml($conf,&$reg = null)
	{
		$xml = new ConfigXml;
		$log = new Logger();
		$log->setLoglevel($this->xincLoglevel());
		$xml->setLogger($log);
		$reg = new Registry();
		$reg->setLogger($log);
		$reg->registerEngineClass('Xinc\Core\Test\Engine',true);
		$xml->load($conf,$reg);
		$pro = new ProjectXml();
		$pro->setLogger($log);
		return $pro;
	}
	
	public function aBuildWithConfig($conf)
	{
	    $xml = $this->projectXml($conf,$reg);
	    $xml->load($conf,$reg);
	    $iterator = $reg->getProjectIterator();
	    $project = $iterator->current();
	    $engine = $reg->getEngine($project->getEngineName());
	    $build = new Build($engine,$project);
	    return $build;	
	}
}
