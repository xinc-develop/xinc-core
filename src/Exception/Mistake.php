<?php
/**
 * Xinc - Continuous Integration.
 *
 * @author     Sebastian Knapp <news@young-workers.de>
 * @copyright  2015 Xinc Development Team, https://github.com/xinc-develop/
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU/LGPL, see license.php
 *             This file is part of Xinc.
 *             Xinc is free software; you can redistribute it and/or modify
 *             it under the terms of the GNU Lesser General Public License as
 *             published by the Free Software Foundation; either version 2.1 of
 *             the License, or (at your option) any later version.
 *
 *             Xinc is distributed in the hope that it will be useful,
 *             but WITHOUT ANY WARRANTY; without even the implied warranty of
 *             MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *             GNU Lesser General Public License for more details.
 *
 *             You should have received a copy of the GNU Lesser General Public
 *             License along with Xinc, write to the Free Software Foundation,
 *             Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * @homepage   https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core\Exception;

use Xinc\Core\Exception;

/**
 * The Mistake Exception is for coding problems. It should be never thrown
 * in production code.
 *
 * @ingroup exceptions
 */
class Mistake extends Exception
{
}
