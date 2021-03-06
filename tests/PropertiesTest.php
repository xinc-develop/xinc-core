<?php
/*
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
 
use Xinc\Core\Properties;

/**
 * @test Test Class for Xinc::Core::Properties
 */
class TestProperties extends Xinc\Core\Test\BaseTest
{
//! @{
    public function testProperties()
    {
        $properties = new Properties();
        
        $value = time();
        $properties->set('test', $value);
        
        $this->assertEquals($properties->get('test'), $value, 'Values should be equal');
        
        $this->assertEquals($properties->get('NonExistant'), null, 'Value should not exist');

        
        $stringToParse = 'test: ${test} :test';
        $expectedString = 'test: ' . $value . ' :test';
        
        $stringParsed = $properties->parseString($stringToParse);
        
        $this->assertTrue($stringToParse != $stringParsed, 'Should not be the same anymore');
        $this->assertTrue($stringParsed == $expectedString, 'Should match "' 
                                                        . $expectedString . '" but is "'
                                                        . $stringParsed . '"');
    }
    
    public function testGetAllProperties()
    {
        $propertiesArr = array();
        for ($i=0; $i< 100; $i++) {
            $propertiesArr[$i] = $i;
        }
        
        $properties = new Properties();
        foreach ($propertiesArr as $key => $value) {
            $properties->set($key, $value);
        }
        $properties->set('50',function () { return 'in the middle of the road'; });
        
        $allProperties = $properties->getAllProperties();
        
        $this->assertEquals($allProperties, $propertiesArr, 'Arrays should match');
    }

    public function testArrayAccess()
    {
		$properties = new Properties();
		$properties->set('test-option','XYZ');
		$properties->set('unset-option','123');
		
		$this->assertTrue(isset($properties['test-option']));
		$this->assertFalse(isset($properties['XYZ']));
		
		$this->assertEquals('123',$properties['unset-option']);
		try {
			unset($properties['unset-option']);
			$this->assertTrue(false,'Unset keys is not allowed.');
		}
		catch(\Xinc\Core\Exception\Mistake $e) {
		    $this->assertTrue(true,'Unset not possible');
		}
		try {
			$properties['grrh'] = true;
			$this->assertTrue(false,'Write access is not allowed.');
		}
		catch(\Xinc\Core\Exception\Mistake $e) {
			$this->assertTrue(true,'Write access is a mistake for properties');
		}
    }
    
    public function testSetArray()
    {
	    $test = array(
	       'test1' => 13, 'test2' => 12
	    );
	    $properties = new Properties();
	    $properties->set($test);
	    $this->assertEquals(13,$properties['test1']);	
	    $this->assertEquals(12,$properties['test2']);
	}
//! @}
}
