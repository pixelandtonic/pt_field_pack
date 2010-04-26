<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


if (! class_exists('PT_Fieldtype'))
{
	require PATH_THIRD.'pt__fieldtype/pt_fieldtype.php';
}


/**
 * PT Multi-select Class
 *
 * @package   PT Field Pack
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2010 Pixel & Tonic, LLC
 */
class Pt_multiselect_ft extends PT_Multi_Fieldtype {

	var $info = array(
		'name'     => 'PT Multiselect',
		'version'  => '1.0'
	);

	var $class = 'pt_multiselect';

	var $total_option_levels = 2;

	// --------------------------------------------------------------------

	/**
	 * Install
	 */
	function install()
	{
		if (! class_exists('FF2EE2'))
		{
			require PATH_THIRD.'pt__fieldtype/ff2ee2/ff2ee2.php';
		}

		new FF2EE2(array('ff_multiselect', 'pt_multiselect'));
	}

	// --------------------------------------------------------------------

	/**
	 * Display Field
	 */
	function _display_field($data, $field_name)
	{
		global $DSP;

		$this->prep_field_data($data);

		$r = form_hidden($field_name, 'n')
		   . form_multiselect($field_name.'[]', $this->settings['options'], $data);

		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Save Field
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
