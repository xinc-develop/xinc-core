<?php
/**
 * Xinc - Continuous Integration.
 *
 * This namespace contains core functionality used by every part of
 * Xinc countinuous integration service.
 *
 * @author    Alexander Opitz <opitz.alexander@gmail.com>
 * @author    Andrei Zmievski <andrei@php.net>
 * @author    Arno Schneider
 * @author    David Ellis
 * @author    Gavin Foster
 * @author    Jamie Talbot
 * @author    Olivier Hoareau
 * @author    Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @author    Sebastian Knapp <news@young-workers.de>
 * @copyright 2007 Arno Schneider, Barcelona
 * @copyright 2007 David Ellis, One Degree Square
 * @copyright 2007 Jamie Talbot, England
 * @copyright 2008 Arno Schneider, Barcelona
 * @copyright 2011-2014 Alexander Opitz, Leipzig
 * @copyright 2015-2016 Xinc Development Team, https://github.com/xinc-develop/
 * @license   http://www.gnu.org/copyleft/lgpl.html GNU/LGPL, see license.php
 *            This file is part of Xinc.
 *            Xinc is free software; you can redistribute it and/or modify
 *            it under the terms of the GNU Lesser General Public License as
 *            published by the Free Software Foundation; either version 2.1 of
 *            the License, or (at your option) any later version
 * @license   Xinc is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *            GNU Lesser General Public License for more details
 * @license   You should have received a copy of the GNU Lesser General Public
 *            License along with Xinc, write to the Free Software Foundation,
 *            Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @homepage  https://github.com/xinc-develop/xinc-core/
 */

namespace Xinc\Core;

/*
 * @defgroup config Classes with a reference to config object
 */

/*
 * @defgroup exceptions Exceptions
 */

/*
 * @defgroup interfaces Interfaces
 */

/*
 * @defgroup logger Classes using a logger
 */

/*
 * @defgroup registry Classes for registering objects
 */

/*
 * @defgroup scheduler Project build scheduler
 *
 * A scheduler must implement the Xinc::Core::Build::Scheduler::SchedulerInterface.
 * Currently a project build contains only one scheduler. When multiple schedulers
 * are defined the last one will be used.
 *
 * @todo create a schedulers task which allows to contain multible subtasks. This
 * is most useful with cron entries, because not every senseful schedule scheme
 * could be expressed with single cron entry.
 */
