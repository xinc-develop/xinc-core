<?php
/*
 * Xinc - Continuous Integration.
 *
 *
 * @author    Sebastian Knapp
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

namespace Xinc\Core\Plugin\Builder;

use Xinc\Core\Build\BuildInterface;
use Xinc\Core\Task\Base;
use Xinc\Core\Task\Slot;
use Symfony\Component\Process\Process as Execute;

class Process extends BaseTask
{
    protected $command;

    protected $timeout;

    /**
     * The commandline for process exection
     */
    public function setCommand($cmd)
    {
        $this->command = $cmd;
    }

    /**
     * An optional timeout in seconds for the process
     */
    public function setTimeout($seconds)
    {
        $this->timeout = $seconds;
    }

    /**
     * Validates if a task can run by checking configs, directories and so on.
     *
     * @return boolean Is true if task can run.
     */
    public function validate(&$msg = null)
    {
        if(!isset($this->command)) {
            $msg = "Process task - no command given.";
            return false;
        }
        return true;
    }

    /**
     * Returns name of task.
     *
     * @return string Name of task.
     */
    public function getName()
    {
        return 'process';
    }

    public function build(BuildInterface $build)
    {
        $process = new Execute($this->command);
        $process->mustRun();
    }
}
