<?php
/*
 * Xinc - Continuous Integration.
 *
 * @author    Arno Schneider
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
 * @link      https://github.com/xinc-develop/xinc-core/
 */
namespace Xinc\Core\Config;

use Xinc\Core\Properties;

/**
 * Xinc Configuration Object.
 */
class Config implements ConfigInterface
{
    private $options;
    private $settings;

    public function __construct()
    {
        $this->options = new Properties();
        $this->settings = new Properties();
    }

    public function get($key)
    {
        if ($this->options->has($key)) {
            return $this->options[$key];
        }

        return $this->settings[$key];
    }

    public function hasOption($key)
    {
      return $this->options->has($key);
    }

    public function has($key)
    {
      if($this->hasOption($key)) return true;
      return $this->settings->has($key);
    }

    public function getOption($key)
    {
	if($this->hasOption($key)) {
            return $this->options[$key];
	}
	throw new ConfigException("Option '$key' is undefined.");
    }

    /**
     * @return array with all options
     */
    public function getOptions()
    {
        return $this->options->getAllProperties();
    }

    public function setOptions($opts)
    {
      foreach($opts as $opt => $val) {
	$this->setOption($opt,$val);
      }
    }

    public function setOption($key, $value)
    {
        $key2 = str_replace(array('-'), '', $key, $cnt);
        if ($cnt > 0) {
            $this->options->set($key2, $value);
        }
        $this->options->set($key, $value);
    }

    public function setSettings($opts)
    {
        $this->settings->set($opts);
    }

    public function setSetting($key, $value)
    {
        $this->settings->set($key, $value);
    }
}
