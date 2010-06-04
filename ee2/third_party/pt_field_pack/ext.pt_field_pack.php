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
 * @copyright Copyright (c) 2010 Pixel & Tonic, LLC
 */
class Pt_field_pack_ext {

	var $name = 'P&T Field Pack';
	var $version = PT_FIELD_PACK_VER;
	var $settings_exist = 'n';
	var $docs_url = 'http://pixelandtonic.com/fieldpack';

	/**
	 * Extension Constructor
	 */
	function Pt_field_pack_ext()
	{
		$this->EE =& get_instance();

		// -------------------------------------------
		//  Prepare Cache
		// -------------------------------------------

		if (! isset($this->EE->session->cache['pt_field_pack']))
		{
			$this->EE->session->cache['pt_field_pack'] = array();
		}
		$this->cache =& $this->EE->session->cache['pt_field_pack'];
	}

	// --------------------------------------------------------------------

	/**
	 * Activate Extension
	 */
	function activate_extension()
	{
		$this->EE->db->insert('extensions', array(
			'class'    => 'Pt_field_pack_ext',
			'hook'     => 'channel_entries_tagdata',
			'method'   => 'channel_entries_tagdata',
			'settings' => '',
			'priority' => 10,
			'version'  => $this->version,
			'enabled'  => 'y'
		));
	}

	/**
	 * Update Extension
	 */
	function update_extension($current = FALSE)
	{
		if (! $current || $current == $this->version)
		{
			return FALSE;
		}

		$this->EE->db->where('class', 'Pt_field_pack_ext');
		$this->EE->db->update('extensions', array('version' => $this->version));
	}

	/**
	 * Disable Extension
	 */
	function disable_extension()
	{
		$this->EE->db->query('DELETE FROM exp_extensions WHERE class = "Pt_field_pack_ext"');
	}

	// --------------------------------------------------------------------

	/**
	 * Get Fields
	 */
	private function _get_fields()
	{
		if (! isset($this->cache['fields']))
		{
			$this->EE->db->select('field_id, field_name, field_type, field_settings');
			$this->EE->db->where_in('field_type', array('pt_checkboxes', 'pt_dropdown', 'pt_multiselect', 'pt_radio_buttons'));
			$query = $this->EE->db->get('channel_fields');

			$fields = $query->result_array();

			foreach ($fields as &$field)
			{
				$field['class'] = ucfirst($field['field_type']).'_ft';
				$field['field_settings'] = unserialize(base64_decode($field['field_settings']));
			}

			$this->cache['fields'] = $fields;
		}

		return $this->cache['fields'];
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

		// iterate through each field
		foreach($this->_get_fields() as $field)
		{
			// is the tag even being used here?
			if (strpos($tagdata, LD.$field['field_name']) !== FALSE)
			{
				$data = $row['field_id_'.$field['field_id']];
				$offset = 0;

				while (preg_match('/'.LD.$field['field_name'].'(:(\w+))?(\s+.*)?'.RD.'/sU', $tagdata, $matches, PREG_OFFSET_CAPTURE, $offset))
				{
					$tag_pos = $matches[0][1];
					$tag_len = strlen($matches[0][0]);
					$tagdata_pos = $tag_pos + $tag_len;
					$endtag = LD.'/'.$field['field_name'].(isset($matches[1][0]) ? $matches[1][0] : '').RD;
					$endtag_len = strlen($endtag);
					$endtag_pos = strpos($tagdata, $endtag, $tagdata_pos);
					$tag_func = (isset($matches[2][0]) && $matches[2][0]) ? 'replace_'.$matches[2][0] : '';

					if (! $tag_func || ! method_exists($field['class'], $tag_func)) $tag_func = 'replace_tag';

					// get the params
					$params = array();
					if (isset($matches[3][0]) && $matches[3][0] && preg_match_all('/\s+([\w-:]+)\s*=\s*([\'\"])([^\2]*)\2/sU', $matches[3][0], $param_matches))
					{
						for ($j = 0; $j < count($param_matches[0]); $j++)
						{
							$params[$param_matches[1][$j]] = $param_matches[3][$j];
						}
					}

					// is this a tag pair?
					$field_tagdata = ($endtag_pos !== FALSE)
					  ?  substr($tagdata, $tagdata_pos, $endtag_pos - $tagdata_pos)
					  :  '';

					// -------------------------------------------
					//  Call the tag's method
					// -------------------------------------------

					if (! class_exists($field['class']))
					{
						include_once PATH_THIRD.$field['field_type'].'/ft.'.$field['field_type'].EXT;
					}

					$fieldtype = new $field['class'];
					$fieldtype->settings = array_merge($row, $field['field_settings']);

					$new_tagdata = $fieldtype->$tag_func($data, $params, $field_tagdata);

					// update tagdata
					$tagdata = substr($tagdata, 0, $tag_pos)
					         . $new_tagdata
					         . substr($tagdata, ($endtag_pos !== FALSE ? $endtag_pos+$endtag_len : $tagdata_pos));

					// update offset
					$offset = $tag_pos;
				}
			}
		}


		return $tagdata;
	}
}
