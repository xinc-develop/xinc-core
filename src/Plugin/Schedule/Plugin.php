<?php
/*
 * Xinc - Continuous Integration.
 *
 *
 * @author    Arno Schneider <username@example.org>
 * @copyright 2015-2016 Xinc Development Team, https://github.com/xinc-develop/
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
 * @homepage      https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core\Plugin\Schedule;

use Xinc\Core\Plugin\Base;

/**
 * Plugin with core schedulers.
 */
class Plugin extends Base
{
    /**
     * Returns the defined tasks of the plugin.
     */
    public function getTaskDefinitions()
    {
        return array(new Task($this),
                     new Cron($this),
                     new Sensor($this), );
    }
}
