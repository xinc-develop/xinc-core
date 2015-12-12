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
namespace Xinc\Core\Plugin\ModificationSet;

use Xinc\Core\Gui\Gui;
use Xinc\Core\Gui\WidgetInterface;

#require_once 'Xinc/Plugin/Repos/Gui/ModificationSet/Extension/Summary.php';
#require_once 'Xinc/Plugin/Repos/Gui/ModificationSet/Extension/ChangeLog.php';

class Widget extends Gui implements WidgetInterface
{
    public function init()
    {
        try {
            $indexWidget = Xinc_Gui_Widget_Repository::getInstance()
                ->getWidgetByClassName('Xinc_Plugin_Repos_Gui_Dashboard_Detail');

            $extension = new Xinc_Plugin_Repos_Gui_ModificationSet_Extension_Summary();

            $indexWidget->registerExtension('BUILD_DETAILS', $extension);

            $extension2 = new Xinc_Plugin_Repos_Gui_ModificationSet_Extension_ChangeLog();

            $indexWidget->registerExtension('BUILD_DETAILS', $extension2);
        } catch (Exception $e) {
            echo 'Could not init on '.__FILE__.'<br>';
        }
    }
}
