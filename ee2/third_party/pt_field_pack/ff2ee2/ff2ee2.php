<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * FF2EE2
 * 
 * FieldFrame-to-EE2 Data Converter
 * 
 * This class was made to be imported by an EE2 fieldtype
 * within its install() function, to aid in converting its
 * prior FieldFrame-based data over to the way EE2 expects.
 * 
 * TODO: Provide a second callback for converting array-based entry data
 * which EE2's field API does not support
 * 
 * -------------------------------------------
 *  Usage
 * -------------------------------------------
 * 
 * If no changes will need to be made to your class name
 * or individual field settings, you can simply run and return:
 * 
 *     function install()
 *     {
 *         if (! class_exists('FF2EE2')) require 'ff2ee2.php';
 * 
 *         $converter = new FF2EE2('class');
 *         return $converter->global_settings;
 *     }
 * 
 * If your new class name differs from your old one,
 * pass an array in the first parameter:
 * 
 *         $converter = new FF2EE2(array('old_class', 'new_class'));
 * 
 * If you need to make changes to individual field settings,
 * pass a callback function in the second parameter:
 * 
 *         $converter = new FF2EE2('class', array(&$this, 'update_field_settings'));
 *
 * The callback function you pass should accept two parameters,
 * $field_settings and $field, and should return $field_settings
 * 
 *     function update_field_settings($field_id, $field)
 *     {
 *         if (isset($field_settings['weblogs']))
 *         {
 *             $field_settings['channels'] = $field_settings['weblogs'];
 *             unset($field_settings['weblogs']);
 *         }
 * 
 *         return $field_settings;
 *     }
 * 
 * 
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2011 Pixel & Tonic, Inc
 */

class FF2EE2 {

	/**
	 * Constructor
	 */
	function FF2EE2($class, $field_settings_callback = FALSE)
	{
		$this->fieldtype_id = NULL;
		$this->global_settings = array();

		// -------------------------------------------
		//  Get the EE2 instance
		// -------------------------------------------

		$this->EE =& get_instance();

		// -------------------------------------------
		//  Was FieldFrame even installed?
		// -------------------------------------------

		if (! $this->EE->db->table_exists('ff_fieldtypes'))
		{
			return;
		}

		// -------------------------------------------
		//  Get the old and new fieldtype classes
		// -------------------------------------------

		if (! is_array($class))
		{
			$old_class = $new_class = $class;
		}
		else
		{
			$old_class = $class[0];
			$new_class = $class[1];
		}

		// -------------------------------------------
		//  Get the FF fieldtype info
		// -------------------------------------------

		$ftype = $this->EE->db->select('fieldtype_id, version, settings')->where('class', $old_class)->get('ff_fieldtypes');

		if (! $ftype->num_rows())
		{
			return;
		}

		$this->fieldtype_id    = $ftype->row('fieldtype_id');
		$this->version         = $ftype->row('version');
		$this->global_settings = $this->_unserialize($ftype->row('settings'));

		// -------------------------------------------
		//  Convert each of this fieldtype's fields
		// -------------------------------------------

		$fields = $this->EE->db->where('field_type', 'ftype_id_'.$this->fieldtype_id)->get('channel_fields');

		foreach($fields->result_array() as $field)
		{
			$field_id       = $field['field_id'];
			$field_settings = $this->_unserialize($field['ff_settings']);

			// Does the fieldtype want to modify these?
			if ($field_settings_callback)
			{
				$field_settings = call_user_func($field_settings_callback, $field_settings, $field);
			}

			// serialize and encode with base64
			$field_settings = base64_encode(serialize($field_settings));

			// save them back to the DB
			$this->EE->db->where('field_id', $field_id)->update('channel_fields', array(
				'field_type' => $new_class,
				'field_settings' => $field_settings,
				'ff_settings' => ''
			));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Unserialize
	 */
	function _unserialize($vals)
	{
		if ($vals && (preg_match('/^(i|s|a|o|d):(.*);/si', $vals) !== FALSE) && ($tmp_vals = @unserialize($vals)) !== FALSE)
		{
			$vals = FF2EE2::_unserialize_cleanup($tmp_vals);
		}

     	return $vals;
	}

	/**
	 * Unserialize Cleanup
	 */
	function _unserialize_cleanup($vals)
	{
		if (is_array($vals))
		{
			foreach ($vals as &$val)
			{
				$val = FF2EE2::_unserialize_cleanup($val);
			}
		}
		else
		{	
			$vals = stripslashes($vals);

			if (get_instance()->config->item('auto_convert_high_ascii') == 'y')
			{
				get_instance()->load->helper('text');
				$vals = entities_to_ascii($vals);
			}
		}

		return $vals;
	}

	// --------------------------------------------------------------------

	/**
	 * Serialize
	 */
	function _serialize($vals)
	{
		if (get_instance()->config->item('auto_convert_high_ascii') == 'y')
		{
			$vals = FF2EE2::_array_ascii_to_entities($vals);
		}

     	return addslashes(serialize($vals));
	}

	/**
	 * ASCII to Entities
	 */
	function _array_ascii_to_entities($vals)
	{
		if (is_array($vals))
		{
			foreach ($vals as &$val)
			{
				$val = FF2EE2::_array_ascii_to_entities($val);
			}
		}
		else
		{
			get_instance()->load->helper('text');
			$vals = ascii_to_entities($vals);
		}

		return $vals;
	}

}
