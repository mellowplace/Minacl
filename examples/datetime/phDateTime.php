<?php
/*
 * Minacl Project: An HTML forms library for PHP
 *          https://github.com/mellowplace/PHP-HTML-Driven-Forms/
 * Copyright (c) 2010, 2011 Rob Graham
 *
 * This file is part of Minacl.
 *
 * Minacl is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * Minacl is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with Minacl.  If not, see
 * <http://www.gnu.org/licenses/>.
 */

/**
 * DateTime component from the Minacl examples
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage examples.datetime
 */
class phDateTime extends phForm
{
	protected $_startYear, $_endYear;
	
	public function __construct($name, $template, $startYear, $endYear)
	{
		parent::__construct($name, $template);
		$this->_startYear = $startYear;
		$this->_endYear = $endYear;
	}
	
	public function preInitialize()
	{
		$this->_view->months = array(
			1	=> 'January',
			2	=> 'February',
			3	=> 'March',
			4	=> 'April',
			5	=> 'May',
			6	=> 'June',
			7	=> 'July',
			8	=> 'August',
			9	=> 'September',
			10	=> 'October',
			11	=> 'November',
			12	=> 'December'
		);
		
		$this->_view->years = new ArrayObject();
		
		for($x = $this->_startYear; $x <= $this->_endYear; $x++)
		{
			$this->_view->years[] = $x;
		}
	}
	
	/**
	 * Set the current selected date time
	 * @param string|int $dateTime a string or timestamp
	 * @todo select the correct options
	 */
	public function setCurrentDateTime($dateTime)
	{
		if(!is_int($dateTime))
		{
			$dateTime = strtotime($dateTime);
		}
		
		$this->element()->year->setRawValue(date('Y', $dateTime));
		$this->element()->month->setRawValue(date('m', $dateTime));
		$this->element()->day->setRawValue(date('d', $dateTime));
		$this->element()->hour->setRawValue(date('H', $dateTime));
		$this->element()->minute->setRawValue(date('i', $dateTime));
		$this->element()->second->setRawValue(date('s', $dateTime));
	}
	
	/**
	 * Gets the currently selected date time
	 * 
	 * @return int the current datetime as a unix timestamp
	 * @todo calc the current datetime from the selected options
	 */
	public function getCurrentDateTime()
	{
		$year = $this->element()->year->getRawValue();
		$month = $this->element()->month->getRawValue();
		$day = $this->element()->day->getRawValue();
		$hour = $this->element()->hour->getRawValue();
		$minute = $this->element()->minute->getRawValue();
		$second = $this->element()->second->getRawValue();
		
		return strtotime("{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}");
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phForm::getValue()
	 */
	public function getValue()
	{
		return $this->getCurrentDateTime();
	}
}