<?php
/**
 * Xinc - Continuous Integration.
 * Exception, plugin file was not found
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
 * @link      https://github.com/xinc-develop/xinc-core/
 */

class Xinc_Plugin_Exception_FileNotFound extends Exception
{
    /**
     * Constructor, generates an Exception Message.
     *
     * @param string $strPluginName Name of the plugin which misses the file.
     * @param string $strFileName   Name of the file which isn't found.
     */
    public function __construct($strPluginName, $strFileName)
    {
        parent::__construct(
            'Plugin ' . $strPluginName . ': file not found: ' . $strFileName
        );
    }
}
