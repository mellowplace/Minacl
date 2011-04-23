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
 * This class can extract the changes for a particular revision
 * from CHANGELOG.md
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 * @subpackage util
 */
class phReleaseNotesReflector
{
	/**
	 * Holds the path to the changelog file
	 * @var string
	 */
	protected $_changelog = null;
	
	public function __construct($changelog)
	{
		$this->_changelog = $changelog;
		if(!file_exists($this->_changelog))
		{
			throw new Exception("The changelog file '{$changelog}' could not be found");
		}
	}
	
	/**
	 * Gets the changes made in a particular release
	 * @param string $release
	 * @return string the changes made
	 */
	public function getChangesForRelease($release)
	{
		$changelog = file_get_contents($this->_changelog);
		$rSearch = preg_quote($release);
		
		if(!preg_match('/(^|\n)###\s?' . $rSearch . '\s+.*?\n(.*?)(###|$)/s', $changelog, $matches))
		{
			// no changes for this version found
			throw new Exception("Cannot find changes in '{$this->_changelog}' for version '{$release}'");
		}
		
		return trim($matches[2]);
	}
	
	/**
	 * Gets the pear version and stability from our release string format
	 * 0.0.1alpha => version = 0.0.1a, stability = alpha 
	 * @param unknown_type $release
	 */
	public function getPearVersionAndStability($release)
	{
		$pattern = '/^(\d\.\d\.\d)(alpha|beta|RC\d)?$/s';
		if(!preg_match($pattern, $release, $matches))
		{
			throw new Exception("'{$release}' is not a valid release, it must match the regular expression '{$pattern}'");
		}
		
		$version = $matches[1];
		if(!isset($matches[2]))
		{
			$stability = 'stable';
		}
		else
		{
			$stability = $matches[2];
			if(substr($stability, 0, 2)=='RC')
			{
				$version = $version . $stability;
				$stability = 'beta';
			}
		}
		
		return array(
			'version' => $version,
			'stability' => $stability
		);
	}
}