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
namespace Xinc\Core\Task;

use Xinc\Core\Plugin\PluginInterface;
use Xinc\Core\Build\BuildInterface;
use Xinc\Core\Logger\UseLoggerInterface;

interface TaskInterface extends UseLoggerInterface
{
    /**
     * Constructor.
     */
    public function __construct(PluginInterface $plugin);

    /**
     * @return Xinc::Core::Plugin::PluginInterface
     */
    public function getPlugin();

    /**
     * Create a new task for a concrete build.
     */
    public function createTask(BuildInterface $build = null);

    /**
     * Validates if a task can run by checking configs, directries and so on.
     *
     * @return bool Is true if task can run.
     */
    public function validate(&$msg = null);

    /**
     * Process the task.
     *
     * @param Xinc\Core\Job\JobInterface $job Job to process this task for.
     */
    public function process(BuildInterface $build);

    /**
     * Returns name of task.
     *
     * @return string Name of task.
     */
    public function getName();

    /**
     * Returns the slot of this task inside a build.
     *
     * @return int The slot number.
     *
     * @see Xinc::Core::Plugin::Slot for available slots
     */
    public function getPluginSlot();
    
    /**
     * the parent frame
     */
    public function setFrame(TaskInterface $task);

    public function getXml();
    public function setXml(\SimpleXMLElement $element);
}
