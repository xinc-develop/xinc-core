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
 * @link      https://github.com/xinc-develop/xinc-core/
 */
 
namespace Xinc\Core\Build;

/**
 * Queue that is holding all the builds
 */
class BuildQueue implements BuildQueueInterface
{
    /**
     * @var Xinc::Core::Build::BuildIterator
     */
    private $builds;
    /**
     * @var Xinc::Core::Build::Build
     */
    private $lastBuild;

    /**
     * @var array
     */
    private $queue=array();

    /**
     * constructor for build queue
     *
     */
    public function __construct()
    {
        $this->builds = new BuildIterator();
    }

    /**
     * adds a build to the queue
     *
     * @param Xinc_Build_Interface $build
     */
    public function addBuild(BuildInterface $build)
    {
        $this->builds->add($build);
    }

    /**
     * Adds a number of builds to the queue
     *
     * @param Xinc_Build_Iterator $builds
     */
    public function addBuilds(BuildIterator $builds)
    {
		foreach($builds as $build) {
            $this->builds->add($build);
        }
    }

    /**
     * Returns the next build time of all the builds scheduled
     * in this queue
     *
     * @return integer unixtimestamp
     */
    public function getNextBuildTime()
    {
        $nextBuildTime = null;
        $build = null;
        while ($this->builds->valid()) {
            $build = $this->builds->current();
            if ( $build->getNextBuildTime() <= $nextBuildTime 
               || $nextBuildTime === null) {
                if ($build->getStatus() != BuildInterface::STOPPED) {
                    $buildTime = $build->getNextBuildTime();

                    if ($buildTime !== null && !$build->isQueued()) {
                        $nextBuildTime = $buildTime;
                        /**
                         * Need to write to queue here and have a FIFO
                         * check before if not already in queue
                         */
                        //if (!in_array($build, $this->_queue)) {
                            $this->queue[] = $build;
                            $build->enqueue();
                        //}
                    } else {
                        /**
                         * we need to check if a scheduled build has a lower build time
                         * but we dont want to queue it again
                         */
                        $nextBuildTime = $buildTime;
                    }
                }
            }
            $this->builds->next();
        }
        usort($this->queue, array($this, 'sortQueue'));
        $this->builds->rewind();
        return $nextBuildTime;
    }

    /**
     * Sorts the builds in the queue by buildtime
     *
     * @param Xinc_Build_Interface $a
     * @param Xinc_Build_Interface $b
     *
     * @return integer
     */
    public function sortQueue($a, $b)
    {
        $buildTimeA = $a->getNextBuildTime();
        $buildTimeB = $b->getNextBuildTime();

        if ($buildTimeA == $buildTimeB) return 0;
        return $buildTimeA < $buildTimeB ? -1 : 1;
    }

    /**
     * Removes the next scheduled build from the queue
     * and returns it
     *
     * @return Xinc_Build_Interface
     */
    public function getNextBuild()
    {
        //if (count($this->_queue)<1) {
        //    $this->getNextBuildTime();
        //}
        usort($this->queue, array($this, 'sortQueue'));
        if (isset($this->queue[0])) {
            if ($this->queue[0]->getNextBuildTime() <= time()) {

                $build = array_shift($this->queue);
                $build->dequeue();
                return $build;
            }
        }
        return null;
    }
}
