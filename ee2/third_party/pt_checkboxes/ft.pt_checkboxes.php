<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


if (! class_exists('PT_Fieldtype'))
{
	require PATH_THIRD.'pt_field_pack/pt_fieldtype.php';
}


/**
 * PT Checkboxes Class
 *
 * @package   PT Field Pack
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2010 Pixel & Tonic, LLC
 */
class Pt_checkboxes_ft extends PT_Multi_Fieldtype {

	var $info = array(
		'name'     => 'PT Checkboxes',
		'version'  => '1.0'
	);

	var $class = 'pt_checkboxes';

	var $settings_label = 'checkbox_options_label';

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
	 * Display Field
	 */
	function _display_field($data, $field_name)
	{
		$this->prep_field_data($data);
		$r = form_hidden($field_name, 'n');

		foreach($this->settings['options'] as $option_name => $option_label)
		{
			$selected = in_array($option_name, $data) ? 1 : 0;
			$r .= '<label style="display:block; float:left; margin:3px 15px 7px 0; white-space:nowrap;">'
			    . form_checkbox($field_name.'[]', $option_name, $selected)
			    . NBS . $option_label
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
		return $data == 'n' ? '' : implode("\n", $data);
	}

	/**
	 * Save Cell
	 */
	function save_cell($data)
	{
		return $this->save($data);
	}

}
