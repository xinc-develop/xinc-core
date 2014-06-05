<?php
/**
 * Iterator over an array of SimpleXMLElement objects defining Projects
 * 
 * @package Xinc.Project
 * @author Arno Schneider
 * @version 2.0
 * @copyright 2007 Arno Schneider, Barcelona
 * @license  http://www.gnu.org/copyleft/lgpl.html GNU/LGPL, see license.php
 *    This file is part of Xinc.
 *    Xinc is free software; you can redistribute it and/or modify
 *    it under the terms of the GNU Lesser General Public License as published
 *    by the Free Software Foundation; either version 2.1 of the License, or    
 *    (at your option) any later version.
 *
 *    Xinc is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU Lesser General Public License for more details.
 *
 *    You should have received a copy of the GNU Lesser General Public License
 *    along with Xinc, write to the Free Software
 *    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once 'Xinc/Iterator.php';
require_once 'Xinc/Project/Exception.php';


class Xinc_Project_Iterator extends Xinc_Iterator
{
  
    /**
     *
     * @param array $array
     * @throws Xinc_Project_Exception
     */
    public function __construct(array $array)
    {
        foreach ($array as $element) {
            if (!$element instanceof Xinc_Project ) {
                throw new Xinc_Project_Exception();
            }
            
        }
        
        parent::__construct($array);
    }
  
}