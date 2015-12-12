<?php
/**
 * Xinc - Continuous Integration.
 *
 * @author    Sebastian Knapp
 * @author    Arno Schneider
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
namespace Xinc\Core\Test;

use Xinc\Core\Engine\Base;
use Xinc\Core\Engine\EngineInterface;
use Xinc\Core\Build\Build;
use Xinc\Core\Build\BuildInterface;
use Xinc\Core\Project\Project;

class Engine extends Base implements EngineInterface
{
    /**
     * get the name of the engine.
     *
     * @return string Name of the engine.
     */
    public function getName()
    {
        return 'TestEngine';
    }

    /**
     * process the build.
     *
     * @param Xinc_Build_Interface $build
     */
    public function build(BuildInterface $build)
    {
    }

    /**
     * Setup a project for the engine and setup a build object from
     * project configuration. 
     *
     * @param Xinc::Core::Project::Project $project A project inside this engine.
     *
     * @return BuildInterface
     */
    public function setupBuild(Project $project)
    {
        $build = new Build($this, $project);
        $build->setLogger($this->log);
        $build->setNumber(1);
        $this->setupBuildProperties($build);
        $this->setupConfigProperties($build);

        return $build;
    }

    /**
     * returns the interval in seconds in which the engine checks for new builds.
     *
     * @return int
     */
    public function getHeartBeat()
    {
        return 30;
    }

    /**
     * Set the interval in which the engine checks for modified builds, necessary builds etc.
     *
     * @param string $seconds
     *
     * @see <xinc engine="name" heartbeat="10"/>
     */
    public function setHeartBeat($seconds)
    {
    }
}
