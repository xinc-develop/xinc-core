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

use Xinc\Core\Config\ConfigLoaderInterface;
use Xinc\Core\Exception\IOException;
use Xinc\Core\Exception\MalformedConfigException;

/**
 * Xinc System Configuration File in XML Format
 */
class Xml extends SimpleXMLElement implements ConfigLoaderInterface
{
    
    private static $_allowedElements = array(
        'xinc',
        'xinc/configuration',
        'xinc/configuration/setting',
        'xinc/engines',
        'xinc/engines/engine',
        'xinc/plugins',
        'xinc/plugins/plugin'
    );
    
    /**
     * Constructs a SimpleXMLElement
     *
     * @param string $fileName
     *
     * @throws Xinc_Config_Exception_FileNotFound
     * @throws Xinc_Config_Exception_InvalidEntry
     */
    public static function load($fileName)
    {
       
        if (!file_exists($fileName)) {
            throw new Xinc_Config_Exception_FileNotFound($fileName);
        } else {
            
            $data = file_get_contents($fileName);
        }
        $file = new Xinc_Config_File($data);
        
        $file->_validate();
        
        return $file;
    }
    
    /**
     * validate the config file
     * - make sure only allowed tags are present
     *
     */
    protected function _validate()
    {
        $array = array('xinc');
        foreach ($this->children() as $elementName => $element) {
            $parent = 'xinc/' . $elementName;
            $array[] = $parent;
            $this->_walkXml($element, $parent, $array);
        }
        foreach ( $array as $path ) {
            if (!in_array($path, self::$_allowedElements)) {
                throw new Xinc_Config_Exception_InvalidEntry($path);
            }
        }
    }
    
    /**
     * Creates an xpath like array of all elements
     *
     * @param SimpleXMLElement $element
     * @param string $parent
     * @param array $array
     */
    private function _walkXml($element, $parent,array &$array)
    {
        foreach ($element->children() as $elementName => $element) {
            $newParent = $parent . '/' . $elementName;
            $array[] = $newParent;
            $this->_walkXml($element, $newParent, $array);
        }
    }
}
