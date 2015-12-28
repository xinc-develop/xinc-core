<?php
/**
 * Xinc - Continuous Integration.
 *
 * PHP version 5
 *
 * @category  Development
 * @package   Xinc.Plugin.Repos.ModificationSet.BuildAlways
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
 * @link      http://code.google.com/p/xinc/
 */

namespace Xinc\Core\Plugin\ModificationSet;

use Xinc\Core\Build\BuildInterface;

class BuildAlways extends BaseTask
{
    /**
     * Returns name of task.
     *
     * @return string Name of task.
     */
    public function getName()
    {
        return 'buildalways';
    }

    /**
     * Check if this modification set has been modified
     *
     * @return Xinc_Plugin_Repos_ModificationSet_Result
     */
    public function checkModified(BuildInterface $build)
    {
		
        $result = new Result();
        $result->setChanged(true);
        $result->setStatus(Xinc_Plugin_Repos_ModificationSet_AbstractTask::CHANGED);
        return $result;
    }

    /**
     * Validates if a task can run by checking configs, directries and so on.
     *
     * @return boolean Is true if task can run.
     */
    public function validateTask()
    {
        return true;
    }
}
