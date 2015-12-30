<?php
/**
 * Xinc - Continuous Integration.
 *
 * PHP version 5
 *
 * @category  Development
 * @package   Xinc.Plugin.Repos.Builder
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

namespace Xinc\Core\Plugin\Builder;

use Xinc\Core\Build\BuildInterface;
use Xinc\Core\Task\Base;

abstract class BaseTask extends Base
{
    /**
     * abstract process for a builder
     *
     * @param Xinc_Build_Interface $build
     */
    public final function process(BuildInterface $build)
    {
        if ( ($status = $this->build($build)) === true ) {
            $build->setStatus(BuildInterface::PASSED);
        } else if ( $status == -1 ) {
            $build->setStatus(BuildInterface::STOPPED);
        } else {
            $build->setStatus(BuildInterface::FAILED);
        }
    }

    public function getPluginSlot()
    {
        return Xinc_Plugin_Slot::PROCESS;
    }

    public function validate()
    {
        try {
            return $this->validateTask();
        } catch(Exception $e){
            Xinc_Logger::getInstance()->error(
                'Could not validate: ' . $e->getMessage()
            );
            return false;
        }
    }


    /**
     * Validate if all information the task needs to run
     * properly have been set
     *
     * @return boolean
     */
    public abstract function validateTask();

    public abstract function build(BuildInterface $build);
}
