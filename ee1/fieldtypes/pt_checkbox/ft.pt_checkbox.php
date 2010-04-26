<?php

if ( ! defined('EXT')) exit('Invalid file request');


/**
 * PT Checkbox Class
 *
 * @package   FieldFrame
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2009 Brandon Kelly
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */
class Pt_checkbox extends Fieldframe_Fieldtype {

	/**
	 * Fieldtype Info
	 * @var array
	 */
	var $info = array(
		'name'     => 'PT Checkbox',
		'version'  => '1.0',
		'docs_url' => 'http://pixelandtonic.com/fieldframe/docs/ff-checkbox',
		'no_lang'  => TRUE
	);

	var $default_field_settings = array(
		'label' => ''
	);

	var $default_cell_settings = array(
		'label' => ''
	);

	const CHECKED_VALUE = 'y';

	/**
	 * Display Field Settings
	 * 
	 * @param  array  $field_settings  The field's settings
	 * @return array  Settings HTML (cell1, cell2, rows)
	 */
	function display_field_settings($field_settings)
	{
		global $DSP, $LANG;

		$cell2 = $DSP->qdiv('defaultBold', $LANG->line('checkbox_label_label'))
		       . $DSP->input_text('label', $field_settings['label'], '', '', 'input', '260px');

		return array('cell2' => $cell2);
	}

	/**
	 * Display Field Settings
	 * 
	 * @param  array  $cell_settings  The cell's settings
	 * @return string  Settings HTML
	 */
	function display_cell_settings($cell_settings)
	{
		global $DSP, $LANG;

		$r = '<label class="itemWrapper">'
		   . $DSP->qdiv('defaultBold', $LANG->line('checkbox_label_label'))
		   . $DSP->input_text('label', $cell_settings['label'], '', '', 'input', '140px')
		   . '</label>';

		return $r;
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
		global $DSP;

		$checked = $field_data == Pt_checkbox::CHECKED_VALUE ? 1 : 0;
		$r = $DSP->input_hidden($field_name, 'n')
		   . '<label style="display:block; margin:3px 0 7px;">'
		   . $DSP->input_checkbox($field_name, Pt_checkbox::CHECKED_VALUE, $checked)
		   . NBS.$field_settings['label']
		   . '</label> ';

		return $r;
	}

	/**
	 * Save Field
	 *
	 * @param  string  $field_data      The field's data
	 * @param  array   $field_settings  The field's settings
	 * @return string  Modified $field_data
	 */
	function save_field($field_data, $field_settings)
	{
		return $field_data == Pt_checkbox::CHECKED_VALUE ? Pt_checkbox::CHECKED_VALUE : '';
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
	 * Save Cell
	 *
	 * @param  string  $cell_data      The cell's data
	 * @param  array   $cell_settings  The cell's settings
	 * @return string  Modified $cell_data
	 */
	function save_cell($cell_data, $cell_settings)
	{
		return $this->save_field($cell_data, $cell_settings);
	}

	/**
	 * Label
	 *
	 * @param  array   $params          Name/value pairs from the opening tag
	 * @param  string  $tagdata         Chunk of tagdata between field tag pairs
	 * @param  string  $field_data      Currently saved field value
	 * @param  array   $field_settings  The field's settings
	 * @return string  relationship references
	 */
	function label($params, $tagdata, $field_data, $field_settings)
	{
		return $field_settings['label'];
	}

}
