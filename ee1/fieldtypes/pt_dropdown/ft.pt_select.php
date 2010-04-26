<?php

if ( ! defined('EXT')) exit('Invalid file request');


/**
 * PT Select Class
 *
 * @package   FieldFrame
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2009 Brandon Kelly
 * @license   http://creativecommons.org/licenses/by-sa/3.0/ Attribution-Share Alike 3.0 Unported
 */
class Pt_select extends Fieldframe_Multi_Fieldtype {

	/**
	 * Fieldtype Info
	 * @var array
	 */
	var $info = array(
		'name'     => 'PT Select',
		'version'  => '1.0',
		'desc'     => 'A better drop-down list',
		'docs_url' => 'http://pixelandtonic.com/fieldframe/docs/ff-select',
		'no_lang'  => TRUE
	);

	var $settings_label = 'select_options_label';
	var $total_option_levels = 2;

	/**
	 * Display Site Settings
	 */
	function display_site_settings()
	{
		global $DB;

		$query = $DB->query('SELECT extension_id FROM exp_extensions WHERE class = "Sarge" AND enabled = "y" LIMIT 1');
		if ($query->num_rows)
		{
			$SD = new Fieldframe_SettingsDisplay();
			return $SD->block()
			     . $SD->row(array(
			                  $SD->label('convert_sarge_label'),
			                  $SD->select('convert', 'n', array('n' => 'no', 'y' => 'yes'))
			                ))
			     . $SD->block_c();
		}

		return FALSE;
	}

	/**
	 * Save Site Settings
	 *
	 * @param  array  $site_settings  The site settings post data
	 * @return array  The modified $site_settings
	 */
	function save_site_settings($site_settings)
	{
		global $DB, $FF;

		if (isset($site_settings['convert']) AND $site_settings['convert'] == 'y')
		{
			// convert Sarge-based drop-down lists
			$query = $DB->query('SELECT field_id, field_list_items FROM exp_weblog_fields WHERE field_type = "select"');
			if ($query->num_rows)
			{
				foreach ($query->result as $field)
				{
					$options = preg_split('/[\r\n]+/', $field['field_list_items']);
					$ff_settings = array('options' => array());
					$optgroup = FALSE;
					$convert = FALSE;

					foreach ($options as $option)
					{
						$values = $values = preg_split("/\s*=\s*/", trim($option));
						if ( ! isset($values[1])) $values[1] = $values[0];
						else $convert = TRUE;

						if ($optgroup)
						{
							if ($values[0] == '[/optgroup]') $optgroup = FALSE;
							else $ff_settings['options'][$optgroup][$values[1]] = $values[0];
						}
						else
						{
							if ($values[0] == '[optgroup]')
							{
								$optgroup = $values[1];
								$ff_settings['options'][$values[1]] = array();
							}
							else $ff_settings['options'][$values[1]] = $values[0];
						}
					}

					if ($convert)
					{
						$data = array(
							'field_type' => 'ftype_id_'.$this->_fieldtype_id,
							'ff_settings' => $FF->_serialize($ff_settings),
							'field_list_items' => ''
						);
						$DB->query($DB->update_string('exp_weblog_fields', $data, 'field_id = '.$field['field_id']));
					}
				}
			}

			// disable Sarge
			$DB->query($DB->update_string('exp_extensions', array('enabled' => 'n'), 'class = "Sarge"'));
		}
	}

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
		global $DSP;

		$this->prep_field_data($field_data);

		$SD = new Fieldframe_SettingsDisplay();
		return $SD->select($field_name, $field_data, $field_settings['options']);
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

		$label = $this->find_label($field_data, $field_settings['options']);
		return $label ? $label : '';
	}

	private function find_label($field_data, $options)
	{
		foreach($options as $name => $label)
		{
			if (is_array($label) && ($sublabel = $this->find_label($field_data, $label)) !== FALSE)
			{
				return $sublabel;
			}
			else if ($field_data === $name)
			{
				return $label;
			}
		}
		return FALSE;
	}

}
