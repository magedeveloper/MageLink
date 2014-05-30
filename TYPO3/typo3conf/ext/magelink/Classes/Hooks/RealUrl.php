<?php
namespace MageDeveloper\Magelink\Hooks;

	/***************************************************************
	 *  Copyright notice
	 *
	 *  (c) 2013 Bastian Zagar <zagar@aixdesign.net>, aixdesign.net
	 *
	 *  All rights reserved
	 *
	 *  This script is part of the TYPO3 project. The TYPO3 project is
	 *  free software; you can redistribute it and/or modify
	 *  it under the terms of the GNU General Public License as published by
	 *  the Free Software Foundation; either version 3 of the License, or
	 *  (at your option) any later version.
	 *
	 *  The GNU General Public License can be found at
	 *  http://www.gnu.org/copyleft/gpl.html.
	 *
	 *  This script is distributed in the hope that it will be useful,
	 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
	 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 *  GNU General Public License for more details.
	 *
	 *  This copyright notice MUST APPEAR in all copies of the script!
	 ***************************************************************/

/**
 * RealUrl Configuration
 *
 * @package magelink
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class RealUrl
{

	/**
	 * Main hook function.  Generates an entire RealUrl configuration.
	 *
	 * @param		array		Main parameters.  Typically, 'config' is the
	 *							existing RealURL configuration thas has been
	 *							generated to this point and 'extKey' is unique
	 *							that this hook used when it was registered.
	 * @param mixed $parentObj
	 */
	function addRealUrlConfig(&$params, $parentObj)
	{
		$postVarSetRoot = '_DEFAULT';
		$config         = &$params['config'];
		$extKey         = &$params['extKey'];

		if (!isset($config['encodeSpURL_postProc'])) {
			$config['encodeSpURL_postProc'] = array();
		}

		if (!isset($config['decodeSpURL_preProc'])) {
			$config['decodeSpURL_preProc'] = array();
		}

		$config['encodeSpURL_postProc'][] = 'EXT:magelink/Classes/Hooks/RealUrl.php:&RealUrl->encodeSpURL_postProc';
		$config['decodeSpURL_preProc'][]  = 'EXT:magelink/Classes/Hooks/RealUrl.php:&RealUrl->decodeSpURL_preProc';

		if(!is_array($config['postVarSets'][$postVarSetRoot])) {
			$config['postVarSets'][$postVarSetRoot] = array();
		}
		$config['postVarSets'][$postVarSetRoot] = array_merge($config['postVarSets'][$postVarSetRoot], $this->addPostVarSets());

		// die(print_r($config));
		return $config;
	}

	/**
	 * Hook for realurl.
	 * Encondes everything that realurl left over
	 *
	 * @param array $params
	 * @param tx_realurl $ref
	 */
	function encodeSpURL_postProc(&$params, &$ref)
	{
		die(print_r($params));
	}

	/**
	 * Decode everything and pass the rest back to realurl
	 *
	 * @param array $params
	 * @param tx_realurl $ref
	 */
	function decodeSpURL_preProc(&$params, &$ref)
	{
		die(print_r($params));
	}

	/**
	 * Adds the postVarSets (not specific to a page) to the RealURL config.
	 *
	 * @return		array		RealURL configuration element.
	 */
	function addPostVarSets()
	{
		$postVarSets = array();

		$postVarSets['category'] = array(
			$this->addValueMap('tx_magelink_categorydisplay[controller]', array(
				//'category' => 'Category'
			)),
			$this->addValueMap('tx_magelink_categorydisplay[action]', array(
				'display' => 'sub'
			)),
			$this->addSimple('tx_magelink_categorydisplay[category]'),
		);

		$postVarSets['product'] = array(
			$this->addValueMap('tx_magelink_productdisplay[controller]', array(
				//'product' => 'Product'
			)),
			$this->addValueMap('tx_magelink_productdisplay[action]', array(
				'display' => 'index'
			)),
			$this->addSimple('tx_magelink_productdisplay[product]'),
		);

		return $postVarSets;
	}

	/*************************************************************************
	 *
	 * Helper functions for generating common RealURL config elements.
	 *
	 ************************************************************************/

	/**
	 * Adds a RealURL config element for simple GET variables.
	 *
	 *	array( 'GETvar' => 'tx_fmemcfeaturematrix_pi1[f1]' ),
	 *
	 * @param		string		The GET variable.
	 * @return		array		RealURL config element.
	 */
	function addSimple($key)
	{
		return array( 'GETvar' => $key );
	}


	/**
	 * Adds RealURL config element for table lookups.
	 *
	 *	array(
	 *		'GETvar'      => 'tx_ttnews[tt_news]',
	 *		'lookUpTable' => array(
	 *			'table'               => 'tt_news',
	 *			'id_field'            => 'uid',
	 *			'alias_field'         => 'title',
	 *			'addWhereClause'      => ' AND NOT deleted',
	 *			'useUniqueCache'      => 1,
	 *			'useUniqueCache_conf' => array(
	 *				'strtolower'     => 1,
	 *				'spaceCharacter' => '_',
	 *			)
	 *		)
	 *	)
	 *
	 * @param		string		The GET variable.
	 * @param		string		The name of the table.
	 * @param		string		The field in the table to be used in the URL.
	 * @param		string		Previous GET variable that must be present for
	 *							this rule to be evaluated.
	 * @return		array		RealURL config element.
	 */
	function addTable($key, $table, $aliasField='title', $condForPrevious=false, $where=' AND NOT deleted')
	{
		$configArray = array();

		if($condForPrevious) {
			$configArray['cond'] = array ('prevValueInList' => $condForPrevious);
		}

		$configArray['GETvar'] = $key;
		$configArray['lookUpTable'] = array(
			'table' => $table,
			'id_field' => 'uid',
			'alias_field' => $aliasField,
			'addWhereClause' => $where,
			'useUniqueCache' => 1,
			'userUniqueCache_conf' => array(
				'strtolower' => 1,
				'spaceCharacter' => '_',
			),
		);

		return $configArray;
	}

	/**
	 * Adds RealURL config element for value map.
	 *	array(
	 *		'GETvar' => 'sub',
	 *		'valueMap' => array(
	 *			'subscribe' => '1',
	 *			'unsubscribe' => '2',
	 *		),
	 *		'noMatch' => 'bypass',
	 *	)
	 *
	 * @param		string		The GET variable.
	 * @param		array		Associative array with label and value.
	 * @param		string		noMatch behavior.
	 * @return		array		RealURL config element.
	 */
	function addValueMap($key, $valueMapArray, $noMatch='bypass')
	{
		$configArray = array();
		$configArray['GETvar'] = $key;

		if(is_array($valueMapArray)) {
			foreach($valueMapArray as $key => $value) {
				$configArray['valueMap'][$key] = $value;
			}
		}

		$configArray['noMatch'] = $noMatch;
		return $configArray;
	}

	/**
	 * Adds RealURL config element for single type.
	 *
	 *	array(
	 *		'type' => 'single',
	 *		'keyValues' => array(
	 *			'tx_newloginbox_pi1[forgot]' => 1,
	 *		)
	 *	)
	 *
	 * @param		array		Associative array of GET variables and values.
	 *							All values must be matched.
	 * @return		array		RealURL config element.
	 */
	function addSingle($keyValueArray)
	{
		$configArray = array();
		$configArray['type'] = 'single';

		if(is_array($keyValueArray)) {
			foreach($keyValueArray as $key => $value) {
				$configArray['keyValues'][$key] = $value;
			}
		}

		return $configArray;
	}
}

