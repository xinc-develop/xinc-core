<?php
/*
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
 * @homepage  https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core\Task;

/**
 * Definition of Plugin Slots.
 */
class Slot
{
    /**
     * Plugin is loaded when Xinc-Daemon starts running.
     */
    const GLOBAL_INIT = 0;

    /**
     * Plugin is loaded when Xinc Daemon starts running
     * and listens globally on all events (across projects).
     */
    const GLOBAL_LISTENER = 1;

    /**
     * Plugin is run in any slot (listeners).
     */
    const PROJECT_LISTENER = 2;

    /**
     * Project is initialized when starting up Xinc daemon.
     */
    const PROJECT_INIT = 3;

    const PROJECT_SET_VALUES = 4;

    /**
     * Initiatoren Scheduler, Cron, ...
     */
    const INIT_PROCESS = 5;

    /**
     * First step, ModificiationSets, BootStrappers etc.
     */
    const PRE_PROCESS = 10;

    /**
     * Builders.
     */
    const PROCESS = 20;

    /**
     * Publishers.
     */
    const POST_PROCESS = 30;

    const SUBTASK = 40;

    public static function getSlots()
    {
        return array(
            self::GLOBAL_INIT,
            self::GLOBAL_LISTENER,
            self::PROJECT_LISTENER,
            self::PROJECT_INIT,
            self::PROJECT_SET_VALUES,
            self::INIT_PROCESS,
            self::PRE_PROCESS,
            self::PROCESS,
            self::POST_PROCESS,
            self::SUBTASK,
        );
    }
}
