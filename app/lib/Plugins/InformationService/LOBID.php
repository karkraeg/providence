<?php
/** ---------------------------------------------------------------------
 * app/lib/Plugins/InformationService/LOBID.php :
 * ----------------------------------------------------------------------
 * LOBID InformationService by Karl Becker 2018
 *
 * Erweiterung für die CollectiveAccess Instanz der ICS
 *
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2015 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This source code is free and modifiable under the terms of
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * @package CollectiveAccess
 * @subpackage InformationService
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 *
 * ----------------------------------------------------------------------
 */

  /**
    *
    */


require_once(__CA_LIB_DIR__."/Plugins/IWLPlugInformationService.php");
require_once(__CA_LIB_DIR__."/Plugins/InformationService/BaseInformationServicePlugin.php");

global $g_information_service_settings_LOBID;

$g_information_service_settings_LOBID = [
    'searchType' => array(
        'formatType' => FT_TEXT,
        'displayType' => DT_SELECT,
        'default' => "person",
        'options' => array(
            _t('Persons') => 'persons',
            _t('Regions') => 'nmo:Region',
        ),
        'width' => 10,
        'height' => 1,
        'label' => _t('Search Type'),
        'validForRootOnly' => 1,
        'description' => _t('Enter an integer value for my setting.'),
    ),

];
class WLPlugInformationServiceLOBID Extends BaseInformationServicePlugin Implements IWLPlugInformationService {
	# ------------------------------------------------
	static $s_settings;
	# ------------------------------------------------
	/**
	 *
	 */
	public function __construct() {
		global $g_information_service_settings_LOBID;

		WLPlugInformationServiceLOBID::$s_settings = $g_information_service_settings_LOBID;
		parent::__construct();
		$this->info['NAME'] = 'LOBID';

		$this->description = _t('Provides access to LOBID service');
	}
	# ------------------------------------------------
	/**
	 * Get all settings settings defined by this plugin as an array
	 *
	 * @return array
	 */
	public function getAvailableSettings() {
		return WLPlugInformationServiceLOBID::$s_settings;
	}
	# ------------------------------------------------
	# Data
	# ------------------------------------------------
	/**
	 * Perform lookup on LOBID-based data service
	 *
	 * @param array $pa_settings Plugin settings values
	 * @param string $ps_search The expression with which to query the remote data service
	 * @param array $pa_options Lookup options (none defined yet)
	 * @return array
	 */
	public function lookup($pa_settings, $ps_search, $pa_options=null) {

		$vs_content = caQueryExternalWebservice(
            $vs_url = 'https://lobid.org/gnd/search?q='.urlencode($ps_search).'&size=10&format=json&type=Person'
		);

		$va_content = @json_decode($vs_content, true);
		if(!isset($va_content['member']) || !is_array($va_content['member'])) { return array(); }

		// the top two levels are 'aggregation' and 'member'
		$va_results = $va_content['member'];
		$va_return = array();

		foreach($va_results as $va_result) {
            $systematik = $va_result['gndSubjectCategory'][0]['label'];
			$va_return['results'][] = array(
                'label' => $va_result['preferredName'] . ' (GND ID: ' . $va_result['gndIdentifier'] . ')' . (!empty($systematik) ? ' (' . $systematik . ')' : ''),
				'url' => $va_result['id'],
				'idno' => $va_result['id'],
			);
		}

		return $va_return;
	}
	# ------------------------------------------------
	/**
	 * Fetch details about a specific item from a LOBID-based data service for "more info" panel
	 *
	 * @param array $pa_settings Plugin settings values
	 * @param string $ps_url The URL originally returned by the data service uniquely identifying the item
	 * @return array An array of data from the data server defining the item.
	 */
	public function getExtendedInformation($pa_settings, $ps_url) {
		$vs_display = "<p><a href='$ps_url' target='_blank'>$ps_url</a></p>";

		return array('display' => $vs_display);
	}
	# ------------------------------------------------
}
