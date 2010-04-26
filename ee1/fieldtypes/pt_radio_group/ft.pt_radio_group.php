<?php

if ( ! defined('EXT')) exit('Invalid file request');


/**
 * PT Radio Group Class
 *
 * @package   FieldFrame
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2009 Brandon Kelly
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */
class Pt_radio_group extends Fieldframe_Multi_Fieldtype {

	/**
	 * Fieldtype Info
	 * @var array
	 */
	var $info = array(
		'name'     => 'PT Radio Group',
		'version'  => PT_FIELD_PACK_VER,
		'docs_url' => 'http://pixelandtonic.com/fieldframe/docs/ff-radio-group',
		'no_lang'  => TRUE
	);

	var $settings_label = 'radio_options_label';

	/**
	 * Prep Field Data
	 *
	 * @param  mixed  &$field_data  The currently-saved $field_data
	 */
	function prep_field_data(&$field_data)
	{
		if (is_array($field_data))
		{
			$field_data = array_shift($field_data);
		}
	}

	/**
	 * Display Field
	 * 
	 * @param  string  $field_name      The field's name
	 * @param  mixed   $field_data      The field's current value
	 * @param  array   $field_settings  The field's settings
	 * @return string  The field's HTML
	 */
	function display_field($field_name, $field_data, $field_settings)
	{
		$this->prep_field_data($field_data);

		$SD = new Fieldframe_SettingsDisplay();
		return $SD->radio_group($field_name, $field_data, $field_settings['options']);
	}

	/**
	 * Display Cell
	 * 
	 * @param  string  $cell_name      The cell's name
	 * @param  mixed   $cell_data      The cell's current value
	 * @param  array   $cell_settings  The cell's settings
	 * @return string  The cell's HTML
	 */
	function display_cell($cell_name, $cell_data, $cell_settings)
	{
		return $this->display_field($cell_name, $cell_data, $cell_settings);
	}

	/**
	 * Display Tag
	 *
	 * @param  array   $params          Name/value pairs from the opening tag
	 * @param  string  $tagdata         Chunk of tagdata between field tag pairs
	 * @param  string  $field_data      Currently saved field value
	 * @param  array   $field_settings  The field's settings
	 * @return string  relationship references
	 */
	function display_tag($params, $tagdata, $field_data, $field_settings)
	{
		$this->prep_field_data($field_data);

		return $field_data;
	}

	/**
	 * Option Label
	 *
	 * @param  array   $params          Name/value pairs from the opening tag
	 * @param  string  $tagdata         Chunk of tagdata between field tag pairs
	 * @param  string  $field_data      Currently saved field value
	 * @param  array   $field_settings  The field's settings
	 * @return string  relationship references
	 */
	function label($params, $tagdata, $field_data, $field_settings)
	{
		$this->prep_field_data($field_data);

		return $field_settings['options'][$field_data];
	}

}
