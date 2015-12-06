<?php
/**
 * @version 3.0
 * @author Sebastian Knapp
 * @copyright 2015 Xinc Development Team, https://github.com/xinc-develop/
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

/**
 * @test Test Class for various exceptions
 */
class TestExceptions extends Xinc\Core\Test\BaseTest
{
    public function testConstructExceptions()
    {
		$e[] = new Xinc\Core\Exception;
		$e[] = new Xinc\Core\Validation\Exception\ValidationException;
	    $e[] = new Xinc\Core\Validation\Exception\NotNumerical('arg','wrong');
	    $e[] = new Xinc\Core\Validation\Exception\TypeMismatch('has','epected');
	    $e[] = new Xinc\Core\Registry\RegistryException();
		foreach($e as $exp) {
			$this->assertTrue(($exp instanceof Xinc\Core\Exception), 
			   get_class($exp) . ' is instanceof Xinc\Core\Exception');
	    }
    }
    
    public function testExceptionMessages()
    {
	    $e = new Xinc\Core\Validation\Exception\NotNumerical('arg','wrong');	
	    $this->assertEquals(
	        'Validation of value "wrong" for argument "arg" is not numerical.',
	        $e->getMessage());
 	
	}
}
