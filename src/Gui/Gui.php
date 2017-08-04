<?php
/**
 * Xinc - Continuous Integration.
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
namespace Xinc\Core\Gui;

use Xinc\Core\Plugin\PluginInterface;

abstract class Gui implements WidgetInterface
{
    protected $_plugin;

    private $_extensions = array();

    public $scripts = '';

    private $_projectName;

    public function __construct(PluginInterface $plugin)
    {
        $this->_plugin = $plugin;
    }

    public function handleEvent($eventId)
    {
    }

    public function getPaths()
    {
        return array();
    }

    public function init()
    {
    }

    public function registerExtension($extensionPoint, $extension)
    {
        if (!isset($this->_extensions[$extensionPoint])) {
            $this->_extensions[$extensionPoint] = array();
        }
        $this->_extensions[$extensionPoint][] = $extension;
    }

    public function getExtensions()
    {
        return $this->_extensions;
    }

    public function getExtensionPoints()
    {
        return array();
    }

    public function hasExceptionHandler()
    {
        return false;
    }

    public function handleException(\Exception $e)
    {
    }
}
