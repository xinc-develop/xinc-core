<?php
/**
 * Xinc - Continuous Integration.
 * Engine to build projects
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
 * @homepage  https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core\Engine;

use Xinc\Core\Build\BuildInterface;
use Xinc\Core\Project\Project;

interface EngineInterface
{

    /**
     * get the name of the engine
     *
     * @return string Name of the engine.
     */
    public function getName();

    /**
     * process the build
     *
     * @param Xinc_Build_Interface $build
     */
    public function build(BuildInterface $build);

    /**
     * Adds a project to the engine.
     *
     * @param \Xinc\Core\Models\Project $project A project inside this engine.
     *
     * @return void
     */
    public function addProject(Project $project);

    /**
     * returns the interval in seconds in which the engine checks for new builds
     *
     * @return integer
     */
    public function getHeartBeat();

    /**
     * Set the interval in which the engine checks for modified builds, necessary builds etc
     *
     * @param string $seconds
     *
     * @see <xinc engine="name" heartbeat="10"/>
     */
    public function setHeartBeat($seconds);
}
