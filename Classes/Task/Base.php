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
 * @homepage  https://github.com/xinc-develop/xinc-core/
 */
namespace Xinc\Core\Task;

use SimpleXmlElement;
use Xinc\Core\Plugin\PluginInterface;
use Xinc\Core\Build\BuildInterface;
use Xinc\Core\Traits\Logger;
use Xinc\Core\Task\TaskInterface;

/**
 * @todo getName method
 * @ingroup logger
 */
abstract class Base implements TaskInterface
{
    use Logger;

    protected $plugin;
    protected $xml;
    protected $frame;

    /**
     * Constructor, stores a reference to the plugin for
     * usage of functionality.
     *
     * @param Xinc_Plugin_Interface $plugin
     */
    public function __construct(PluginInterface $plugin)
    {
        $this->plugin = $plugin;
    }

    public function setFrame(TaskInterface $task)
    {
        $this->frame = $task;
    }

    /**
     * @return Xinc::Core::Plugin::PluginInterface
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    public function createTask(BuildInterface $build = null)
    {
        $new = new static($this->getPlugin());
        $new->init($build);
        return $new;
    }

    public function setup(BuildInterface $build = null)
    {
        //
    }

    protected function init(BuildInterface $build = null)
    {
        //
    }

    protected function getConfigValue($key)
    {
        return $this->getPlugin()->getConfigValue($key);
    }

    /**
     * Returns name of task by lowercasing class name.
     * @return string Name of task.
     */
    public function getName()
    {
        return strtolower(get_class($this));
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function getXml()
    {
        return $this->xml;
    }

    public function setXml(SimpleXMLElement $element)
    {
        $this->xml = $element;
    }

    /**
     * Validates if a task can run by checking configs, directories and so on.
     * @return bool Is true if task can run.
     */
    public function validate(&$msg = null)
    {
        return true;
    }
}
