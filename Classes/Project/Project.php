<?php
/**
 * Xinc - Continuous Integration.
 *
 * @author    Alexander Opitz <opitz.alexander@googlemail.com>
 * @author    Sebastian Knapp <news@young-workers.de>
 * @copyright 2014 Alexander Opitz, Leipzig
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
namespace Xinc\Core\Project;

/**
 * This class represents one project with its processes.
 *
 * It is loaded from the configuration.
 */
class Project
{
    /**
     * @var string The name of the project.
     */
    private $name = '';

    /**
     * @var string Name of the used engine.
     */
    private $engineName = '';

    /**
     * @var Xinc::Core::Project::Status
     */
    private $status;

    /**
     * @see Xinc::Core::Task::Slot
     *
     * @var array Used Processes
     */
    private $processes = array();

    // TODO: Not the right direction.
    private $config;

    public function __construct()
    {
        $this->status = new Status(Status::NEVERRUN);
    }

    /**
     * Sets the project name for display purposes.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns this name of the project.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the project name of the used engine.
     *
     * @param string $engine
     */
    public function setEngineName($engineName)
    {
        $this->engineName = $engineName;
    }

    /**
     * Returns this name of the engine of this project.
     *
     * @return string
     */
    public function getEngineName()
    {
        return $this->engineName;
    }

    /**
     * sets the status of the project.
     *
     * @see Xinc\Core\Project\Status
     *
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status->setValue($status);
    }

    /**
     * Retrieves the status of the current project.
     *
     * @see Xinc\Core\Project\Status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status->getValue();
    }

    public function setGroup(ProjectGroup $group)
    {
        $this->group = $group;
    }

    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Adds a process with appropriate slot to the project.
     *
     * @param int $slot
     * @param ?   $process
     */
    public function addProcess($slot, $process)
    {
        $this->processes[$slot][] = $process;
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }
}
