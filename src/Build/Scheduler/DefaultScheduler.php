<?php
/*
 * Xinc - Continuous Integration.
 *
 *
 * @author    Arno Schneider <username@example.org>
 * @copyright 2007 Arno Schneider, Barcelona
 * @copyright 2015-2016 Xinc Development Team, https://github.com/xinc-develop/
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

namespace Xinc\Core\Build\Scheduler;

use Xinc\Core\Build\BuildInterface;

/**
 * Build-Scheduler, will only build once if not built yet.
 *
 * @ingroup scheduler
 */
class DefaultScheduler implements SchedulerInterface
{
    private $_nextBuildTime = null;

    /**
     * Calculates the next build timestamp
     * this is a build once scheduler.
     *
     * @return int
     */
    public function getNextBuildTime(BuildInterface $build)
    {
        if ($build->getLastBuild()->getBuildTime() == null
            && $build->getStatus() !== BuildInterface::STOPPED
        ) {
            if (!isset($this->_nextBuildTime)) {
                $this->_nextBuildTime = time();
            }

            return $this->_nextBuildTime;
        } else {
            return;
        }
    }
}
