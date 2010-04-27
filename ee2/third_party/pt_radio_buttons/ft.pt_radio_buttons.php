<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


if (! class_exists('PT_Fieldtype'))
{
	require PATH_THIRD.'pt_field_pack/pt_fieldtype.php';
}


/**
 * PT Radio Buttons Class
 *
 * @package   PT Field Pack
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2010 Pixel & Tonic, LLC
 */
class Pt_radio_buttons_ft extends PT_Multi_Fieldtype {

	var $info = array(
		'name'     => 'PT Radio Buttons',
		'version'  => PT_FIELD_PACK_VER
	);

	var $class = 'pt_radio_buttons';

	var $settings_label = 'radio_options_label';

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

		new FF2EE2(array('ff_radio_group', 'pt_radio_buttons'));
	}

	// --------------------------------------------------------------------

	/**
	 * Prep Field Data
	 */
	function prep_field_data(&$data)
	{
		if (is_array($data))
		{
			$data = array_shift($data);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Display Field
	 */
	function _display_field($data, $field_name)
	{
		$this->prep_field_data($data);

		$r = '';

		foreach($this->settings['options'] as $option)
		{
			$selected = ($option == $data);
			$r .= '<label>'
			    .   form_radio($field_name, $option, $selected)
			    .   NBS . $option
			    . '</label>';
		}

		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Replace Tag
	 */
	function replace_tag($data)
	{
		$this->prep_field_data($data);

		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * Option Label
	 */
	function label($data)
	{
		$this->prep_field_data($field_data);

		return $this->settings['options'][$data];
	}

}
