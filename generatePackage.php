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
 * Generates the package.xml file for PEAR
 *
 * @author Rob Graham <htmlforms@mellowplace.com>
 * @package phform
 */
require_once('PEAR/PackageFileManager2.php');
require_once(dirname(___FILE___) . '/lib/util/phReleaseNotesReflector.php');

/*
 * check arguments
 */
if($argc < 2)
{
	trigger_error('Usage: ' . $argv[0] . ' version [make]', E_USER_ERROR);
}

$version = $argv[1];

PEAR::setErrorHandling(PEAR_ERROR_DIE);

$release = new phReleaseNotesReflector(dirname(__FILE__) . '/CHANGELOG.md');

$options = array(
'filelistgenerator' => 'file', // this copy of our source code is a CVS checkout
'simpleoutput' => true,
'baseinstalldir' => '/Minacl',
'packagedirectory' => dirname(___FILE___) . '/lib/form', // just package the library
'clearcontents' => false,
'dir_roles' => array('.' => 'php'),
'exceptions' => array(
	'README' => 'doc',
	'LICENSE' => 'doc'
)
);

$packagexml = new PEAR_PackageFileManager2;
$packagexml->setOptions($options);
$packagexml->setPackageType('php');

// Package name, summary and longer description

$packagexml->setPackage('Minacl');
$packagexml->setSummary('Minacl - Agile, reusable forms for PHP');
$packagexml->setDescription('Minacl is a form library that parses valid XHTML and gives you an API to validate and access the data your form produces.');

$packagexml->setNotes($release->getChangesForRelease($version));
// The channel where this package is hosted. Since we’re installing from a local
// downloaded file rather than a channel we’ll pretend it’s from PEAR.

$packagexml->setChannel('pear.minacl.org');

// Add any known dependencies such as PHP version, extensions, PEAR installer

$packagexml->setPhpDep('5.2');
$packagexml->setPearinstallerDep('1.8.0');
$packagexml->addPackageDepWithChannel('required', 'PEAR', 'pear.php.net', '1.4.0');

$packagexml->addRelease();
// Other info, like the Lead Developers. license, version details and stability type

$pearVersion = $release->getPearVersionAndStability($version);
$packagexml->addMaintainer('lead', 'Rob', 'Rob Graham', 'minacl@mellowplace.com');
$packagexml->setLicense('LGPL', 'http://www.gnu.org/licenses/');
$packagexml->setAPIVersion($pearVersion['version']);
$packagexml->setReleaseVersion($pearVersion['version']);
$packagexml->setReleaseStability($pearVersion['stability']);
$packagexml->setAPIStability($pearVersion['stability']);

// Add this as a release, and generate XML content
$packagexml->generateContents();

// Pass a “make” flag from the command line or browser address to actually write
// package.xml to disk, otherwise just debug it for any errors

if (isset($argv[2]) && $argv[2] == 'make')
{
	$packagexml->writePackageFile();
} 
else 
{
	$packagexml->debugPackageFile();
}