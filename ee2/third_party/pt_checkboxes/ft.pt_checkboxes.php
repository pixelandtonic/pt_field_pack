<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


if (! class_exists('PT_Fieldtype'))
{
	require PATH_THIRD.'pt_field_pack/pt_fieldtype.php';
}


/**
 * P&T Checkboxes Class
 *
 * @package   P&T Field Pack
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2010 Pixel & Tonic, LLC
 */
class Pt_checkboxes_ft extends PT_Multi_Fieldtype {

	var $info = array(
		'name'     => 'P&amp;T Checkboxes',
		'version'  => PT_FIELD_PACK_VER
	);

	var $class = 'pt_checkboxes';

	// --------------------------------------------------------------------

	/**
	 * Install
	 */
	function install()
	{
		if (! class_exists('FF2EE2'))
		{
			require PATH_THIRD.'pt_field_pack/ff2ee2/ff2ee2.php';
		}

		new FF2EE2(array('ff_checkbox_group', 'pt_checkboxes'));
		new FF2EE2(array('ff_checkbox', 'pt_checkboxes'), array(&$this, '_convert_checkbox_settings'));
	}

	/**
	 * Convert Checkbox Settings
	 */
	function _convert_checkbox_settings($settings, $field)
	{
		return array('options' => array('y' => $settings['label']));
	}

	// --------------------------------------------------------------------

	/**
	 * Save Cell Settings
	 */
	function save_cell_settings($settings)
	{
		if (! $settings['options'])
		{
			return array('options' => array('y' => ''));
		}

		return parent::save_cell_settings($settings);
	}

	// --------------------------------------------------------------------

	/**
	 * Display Field
	 */
	function _display_field($data, $field_name)
	{
		$this->prep_field_data($data);
		$r = form_hidden($field_name, 'n');

		foreach($this->settings['options'] as $option_name => $option_label)
		{
			$selected = in_array($option_name, $data) ? 1 : 0;
			$r .= '<label>'
			    .   form_checkbox($field_name.'[]', $option_name, $selected)
			    .   NBS . $option_label
			    . '</label> ';
		}
		$r .= '<div style="clear:left"></div>';

		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Save
	 */
	function save($data)
	{
		$data = $data == 'n' ? '' : implode("\n", $data);
		return parent::save($data);
	}

	/**
	 * Save Cell
	 */
	function save_cell($data)
	{
		return $this->save($data);
	}

	// --------------------------------------------------------------------

	/**
	 * Replace Tag
	 */
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		if (! isset($this->settings['options']) || ! $this->settings['options'] || count($this->settings['options']) < 2)
		{
			return $data;
		}

		return parent::replace_tag($data, $params, $tagdata);
	}

}
