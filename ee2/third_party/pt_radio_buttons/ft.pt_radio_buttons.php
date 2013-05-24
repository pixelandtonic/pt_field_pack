<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


if (! class_exists('PT_Fieldtype'))
{
	require PATH_THIRD.'pt_field_pack/pt_fieldtype.php';
}


/**
 * P&T Radio Buttons Class
 *
 * @package   P&T Field Pack
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2011 Pixel & Tonic, Inc
 */
class Pt_radio_buttons_ft extends PT_Multi_Fieldtype {

	var $info = array(
		'name'     => 'P&amp;T Radio Buttons',
		'version'  => PT_FIELD_PACK_VER
	);

	var $class = 'pt_radio_buttons';

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

		return array();
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

		$field_id = str_replace(array('[', ']'), array('_', '_'), $field_name);

		foreach($this->settings['options'] as $option_name => $option)
		{
			$field_data = array(
				'name'        => $field_name,
				'id'          => $field_id.'_'.$option_name,
				'value'       => $option_name,
				'checked'     => ((string) $option_name === (string) $data)
			);

			$r .= '<label for="' . $field_data['id'] . '">'
			    .   form_radio($field_data)
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
	function replace_label($data)
	{
		$this->prep_field_data($data);

		return $this->settings['options'][$data];
	}

}
