<?php
/**
 * Xinc - Continuous Integration.
 *
 * PHP version 5
 *
 * @category   Development
 *
 * @author     Arno Schneider <username@example.org>
 * @copyright  2007 Arno Schneider, Barcelona
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU/LGPL, see license.php
 *             This file is part of Xinc.
 *             Xinc is free software; you can redistribute it and/or modify
 *             it under the terms of the GNU Lesser General Public License as
 *             published by the Free Software Foundation; either version 2.1 of
 *             the License, or (at your option) any later version.
 *
 *             Xinc is distributed in the hope that it will be useful,
 *             but WITHOUT ANY WARRANTY; without even the implied warranty of
 *             MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *             GNU Lesser General Public License for more details.
 *
 *             You should have received a copy of the GNU Lesser General Public
 *             License along with Xinc, write to the Free Software Foundation,
 *             Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @link       https://github.com/Xinc-org/Xinc.Trigger
 */

namespace Xinc\Core\Plugin\Schedule;

use Xinc\Core\Build\BuildInterface;
use Xinc\Core\Build\Scheduler\SchedulerInterface;

/**
 * A scheduler which triggers a build when a given file exists.
 *
 * @tag sensor
 * @attribute file - a filename
 * @slot INIT_PROCESS
 *
 * @ingroup scheduler
 */
class Sensor extends Base implements SchedulerInterface
{
    /**
     * File to test for existence.
     *
     * @var string
     */
    private $file = null;

    /**
     * Value to test inside the file.
     *
     * @var string
     */
    private $filevalue = null;

    /**
     * Tag name is sensor.
     */
    public function getName()
    {
        return 'sensor';
    }

    /**
     * Sets the sensor filename string.
     *
     * @param string $file The sensor filename string
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * Gets the sensor filename string.
     *
     * @return string The sensor filename string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Validates if a task can run by checking configs, directries and so on.
     *
     * @return bool Is true if task can run
     */
    public function validate(&$msg = null)
    {
        return $this->file !== null;
    }

    /**
     * Calculates the real next job runtime dependend on lastJob.
     *
     * @param Xinc::Core::Build::BuildInterface $lastJob
     *
     * @return int next job runtime as timestamp
     */
    public function getNextBuildTime(BuildInterface $lastJob = null)
    {
        if (file_exists($this->file)) {
            unlink($this->file);

            return time();
        }

        return null;
    }
}
