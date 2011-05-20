<?php if (! defined('APP_VER')) exit('No direct script access allowed');


if (! defined('PT_FIELD_PACK_VER'))
{
	// get the version from config.php
	require PATH_THIRD.'pt_field_pack/config.php';
	define('PT_FIELD_PACK_VER', $config['version']);
}


/**
 * P&T Field Pack Extension Class for ExpressionEngine 2
 *
 * @package   P&T Field Pack
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2011 Pixel & Tonic, Inc
 */
class Pt_field_pack_ext {

	var $name = 'P&T Field Pack';
	var $version = PT_FIELD_PACK_VER;
	var $settings_exist = 'n';
	var $docs_url = 'http://pixelandtonic.com/divebar';

	/**
	 * Extension Constructor
	 */
	function Pt_field_pack_ext()
	{
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------

	/**
	 * Activate Extension
	 */
	function activate_extension(){}

	/**
	 * Update Extension
	 */
	function update_extension($current = FALSE)
	{
		$this->disable_extension();
	}

	/**
	 * Disable Extension
	 */
	function disable_extension()
	{
		$this->EE->db->where('class', 'Pt_field_pack_ext')
		             ->delete('extensions');
	}

	// --------------------------------------------------------------------

	/**
	 * channel_entries_tagdata hook
	 */
	function channel_entries_tagdata($tagdata, $row, $Channel)
	{
		// has this hook already been called?
		if ($this->EE->extensions->last_call)
		{
			$tagdata = $this->EE->extensions->last_call;
		}

		return $tagdata;
	}
}
