<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


if (! class_exists('PT_Fieldtype'))
{
	require PATH_THIRD.'pt_field_pack/pt_fieldtype.php';
}


/**
 * P&T Dropdown Class
 *
 * @package   P&T Field Pack
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2010 Pixel & Tonic, LLC
 */
class Pt_dropdown_ft extends PT_Multi_Fieldtype {

	var $info = array(
		'name'     => 'P&amp;T Dropdown',
		'version'  => PT_FIELD_PACK_VER
	);

	var $class = 'pt_dropdown';

	var $total_option_levels = 2;

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

		new FF2EE2(array('ff_select', 'pt_dropdown'));
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

		return form_dropdown($field_name, $this->settings['options'], $data);
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

		$label = $this->_find_label($data, $this->settings['options']);
		return $label ? $label : '';
	}

	/**
	 * Find Label
	 */
	private function _find_label($data, $options)
	{
		foreach($options as $name => $label)
		{
			if (is_array($label) && ($sublabel = $this->find_label($data, $label)) !== FALSE)
			{
				return $sublabel;
			}
			else if ((string) $data === (string) $name)
			{
				return $label;
			}
		}
		return FALSE;
	}

}
