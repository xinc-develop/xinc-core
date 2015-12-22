<?php
/**
 * Xinc - Continuous Integration.
 *
 *
 * @author    Arno Schneider <username@example.org>
 * @copyright 2007 Arno Schneider, Barcelona
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
 * @link      https://github.com/xinc-develop/xinc-core/
 */
namespace Xinc\Core\Plugin\Property;

use Xinc\Core\Plugin\Base;
use Xinc\Core\Build\BuildInterface;

/**
 * Properties plugin, allows to set properties on a project
 * and substitutes values of the property in the form ${name}.
 */
class Plugin extends Base
{
    /**
     * loads properties from a property file.
     * 
     * @param BuildInterface $build
     * @param string $fileName
     */
    public function parsePropertyFile(BuildInterface $build, $fileName)
    {
        $activeProperty = false;
        $trimNextLine = false;
        $arr = array();
        $fh = fopen($fileName, 'r');
        if (is_resource($fh)) {
            while ($line = fgets($fh)) {
                if (preg_match('/^[!#].*/', $line)) {
                    // comment
                } elseif (preg_match("/^.*?([\._-\w]+?)\s*[=:]+\s*(.*)$/", $line, $matches)) {
                    // we have a key definition
                    $activeProperty = true;
                    $key = $matches[1];
                    $valuePart = $matches[2];
                    $arr[$key] = trim($valuePart);
                    if ($arr[$key]{strlen($arr[$key]) - 1} == '\\') {
                        $arr[$key] = substr($arr[$key], 0, -1);
                        $trimNextLine = true;
                    } else {
                        $trimNextLine = false;
                    }
                } elseif ($activeProperty) {
                    $trimmed = trim($line);
                    if (empty($trimmed)) {
                        $activeProperty = false;
                        continue;
                    } elseif ($trimNextLine) {
                        $line = $trimmed;
                    } else {
                        $line = rtrim($line);
                    }
                    $arr[$key] .= "\n".$line;
                    if ($arr[$key]{strlen($arr[$key]) - 1} == '\\') {
                        $arr[$key] = substr($arr[$key], 0, -1);
                        $trimNextLine = true;
                    } else {
                        $trimNextLine = false;
                    }
                }
            }
            foreach ($arr as $key => $value) {
                $build->debug('Setting property "${'.$key.'}" to "'.$value.'"');
                $build->setProperty($key, stripcslashes($value));
            }
        } else {
            $build->error('Cannot read from property file: '.$fileName);
        }
    }

    public function getTaskDefinitions()
    {
        return array(new SetTask($this),
                     new SubstituteTask($this), );
    }
}
