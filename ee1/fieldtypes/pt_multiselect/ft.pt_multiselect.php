<?php

if ( ! defined('EXT')) exit('Invalid file request');


/**
 * PT Multi-select Class
 *
 * @package   FieldFrame
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2009 Brandon Kelly
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */
class Pt_multiselect extends Fieldframe_Multi_Fieldtype {

	/**
	 * Fieldtype Info
	 * @var array
	 */
	var $info = array(
		'name'     => 'PT Multi-select',
		'version'  => '1.0',
		'docs_url' => 'http://pixelandtonic.com/fieldframe/docs/ff-multi-select',
		'no_lang'  => TRUE
	);

	var $total_option_levels = 2;

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
		global $DSP;

		$this->prep_field_data($field_data);

		$SD = new Fieldframe_SettingsDisplay();

		$r = $DSP->input_hidden($field_name, 'n')
		   . $SD->multiselect($field_name.'[]', $field_data, $field_settings['options'], array('width' => ';'));

		return $r;
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
	 * Save Field
	 */
	function save_field($field_data, $field_settings)
	{
		return $field_data == 'n' ? '' : implode("\n", $field_data);
	}

	/**
	 * Save Cell
	 */
	function save_cell($cell_data, $cell_settings)
	{
		return $this->save_field($cell_data, $cell_settings);
	}

}
