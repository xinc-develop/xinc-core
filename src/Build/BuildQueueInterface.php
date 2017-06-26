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

namespace Xinc\Core\Build;

/**
 * A Build queue holds all Build Jobs queued for execution.
 *
 * Build Jobs are registered with the Build Queue to be
 * executed at a certain time
 */
interface BuildQueueInterface
{
    /**
     * adds a build to the queue.
     *
     * Calls the getNextBuildTime() method to put
     * the builds into the right order in the queue
     *
     * @param Xinc_Build_Interface $build
     */
    public function addBuild(BuildInterface $build);

    /**
     * Adds a number of builds to the queue.
     *
     * @param Xinc_Build_Iterator $builds
     */
    public function addBuilds(BuildIterator $builds);

    /**
     * Returns the next build time of all the builds scheduled
     * in this queue.
     *
     * @return int unixtimestamp
     */
    public function getNextBuildTime();

    /**
     * Removes the next scheduled build from the queue
     * and returns it.
     *
     * @return Xinc_Build_Interface
     */
    public function getNextBuild();
}
