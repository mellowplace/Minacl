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
		$this->_view->years = new ArrayObject();
		
		for($x = $this->_startYear; $x <= $this->_endYear; $x++)
		{
			$this->_view->years[] = $x;
		}
		
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
	}
	
	/**
	 * Gets the currently selected date time
	 * 
	 * @return int the current datetime as a unix timestamp
	 */
	public function getDateTime()
	{
		$year = $this->year->getValue();
		$month = $this->month->getValue();
		$day = $this->day->getValue();
		$hour = $this->hour->getValue();
		$minute = $this->minute->getValue();
		$second = $this->second->getValue();
		
		return strtotime("{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}");
	}
	
	/**
	 * Set the current selected date time
	 * @param string|int $dateTime a string or timestamp
	 * @todo select the correct options
	 */
	public function setDateTime($dateTime)
	{
		if(!is_int($dateTime))
		{
			$dateTime = strtotime($dateTime);
		}
		
		$this->year->bind(date('Y', $dateTime));
		$this->month->bind(date('m', $dateTime));
		$this->day->bind(date('d', $dateTime));
		$this->hour->bind(date('H', $dateTime));
		$this->minute->bind(date('i', $dateTime));
		$this->second->bind(date('s', $dateTime));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/form/phForm::getValue()
	 */
	public function getValue()
	{
		return $this->getDateTime();
	}
}