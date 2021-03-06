<?php
/*
 * Xinc - Continuous Integration.
 *
 * @author    Sebastian Knapp <news@young-workers.de>
 * @copyright 2007 Arno Schneider, Barcelona
 * @copyright 2015 Xinc Development Team, https://github.com/xinc-develop/
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

namespace Xinc\Core\Plugin;

/**
 * A group is a list of the available plugins.
 *
 * Each xinc extension should define a group with all provided plugin classes.
 * This allows to load all plugins with a single configuration line.
 *
 *      <plugins group="Xinc\Core\Plugin\Group"/>
 */
class Group implements PluginGroupInterface
{
    /**
     * @return a list of plugin classes
     */
    public function getPluginClasses()
    {
        $base = __NAMESPACE__;
        $plugins = array(
            'Configuration',
            'Builder',
            'ModificationSet',
            'Property',
            'Schedule',
        );

        return array_map(function ($name) use ($base) {
            return implode('\\', [$base, $name, 'Plugin']);
        }, $plugins);
    }
}
