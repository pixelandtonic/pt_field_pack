<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


if (! defined('PT_FIELD_PACK_VER'))
{
	// get the version from config.php
	require PATH_THIRD.'pt_field_pack/config.php';
	define('PT_FIELD_PACK_VER', $config['version']);
}


/**
 * P&T Fieldtype Base Class
 *
 * @package   P&T Field Pack
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2010 Pixel & Tonic, LLC
 */
class PT_Fieldtype extends EE_Fieldtype {

	/**
	 * PT_Fieldtype Constructor
	 */
	function PT_Fieldtype()
	{
		parent::EE_Fieldtype();
	}

	// --------------------------------------------------------------------

	/**
	 * Options Setting
	 */
	function options_setting($options=array(), $indent = '')
	{
		$r = '';

		foreach($options as $name => $label)
		{
			if ($r !== '') $r .= "\n";

			// is this just a blank option?
			if (! $name && ! $label) $name = $label = ' ';

			$r .= $indent . htmlentities($name);

			// is this an optgroup?
			if (is_array($label)) $r .= "\n".$this->options_setting($label, $indent.'    ');
			else if ($name != $label) $r .= ' : '.$label;
		}

		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Save Options Setting
	 */
	function save_options_setting($options = '', $total_levels = 1)
	{
		// prepare options
		$options = preg_split('/[\r\n]+/', $options);
		foreach($options as &$option)
		{
			$option_parts = preg_split('/\s:\s/', $option, 2);
			$option = array();
			$option['indent'] = preg_match('/^\s+/', $option_parts[0], $matches) ? strlen(str_replace("\t", '    ', $matches[0])) : 0;
			$option['name']   = trim($option_parts[0]);
			$option['value']  = isset($option_parts[1]) ? trim($option_parts[1]) : $option['name'];
		}

		return $this->_structure_options($options, $total_levels);
	}

	/**
	 * Structure Options
	 */
	private function _structure_options(&$options, $total_levels, $level = 1, $indent = -1)
	{
		$r = array();

		while ($options)
		{
			if ($indent == -1 || $options[0]['indent'] > $indent)
			{
				$option = array_shift($options);
				$children = (! $total_levels OR $level < $total_levels)
				              ?  $this->_structure_options($options, $total_levels, $level+1, $option['indent']+1)
				              :  FALSE;
				$r[(string)$option['name']] = $children ? $children : (string)$option['value'];
			}
			else if ($options[0]['indent'] <= $indent)
			{
				break;
			}
		}

		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Prep Iterators
	 */
	function prep_iterators(&$tagdata)
	{
		// find {switch} tags
		$this->_switches = array();
		$tagdata = preg_replace_callback('/'.LD.'switch\s*=\s*([\'\"])([^\1]+)\1'.RD.'/sU', array(&$this, '_get_switch_options'), $tagdata);

		$this->_count_tag = 'count';
		$this->_iterator_count = 0;
	}

	/**
	 * Get Switch Options
	 */
	function _get_switch_options($match)
	{
		global $FNS;

		$marker = LD.'SWITCH['.$FNS->random('alpha', 8).']SWITCH'.RD;
		$this->_switches[] = array('marker' => $marker, 'options' => explode('|', $match[2]));
		return $marker;
	}

	/**
	 * Parse Iterators
	 */
	function parse_iterators(&$tagdata)
	{
		// {switch} tags
		foreach($this->_switches as $i => $switch)
		{
			$option = $this->_iterator_count % count($switch['options']);
			$tagdata = str_replace($switch['marker'], $switch['options'][$option], $tagdata);
		}

		// update the count
		$this->_iterator_count++;

		// {count} tags
		$tagdata = $this->EE->TMPL->swap_var_single($this->_count_tag, $this->_iterator_count, $tagdata);
	}

}


// ====================================================================


/**
 * P&T Multi Fieldtype Base Class
 * 
 * @package   P&T Field Pack
 * @author    Brandon Kelly <brandon@pixelandtonic.com>
 * @copyright Copyright (c) 2010 Pixel & Tonic, LLC
 */
class PT_Multi_Fieldtype extends PT_Fieldtype {

	var $default_field_settings = array(
		'options' => array(
			'Option 1' => 'Option 1',
			'Option 2' => 'Option 2',
			'Option 3' => 'Option 3'
		)
	);

	var $default_cell_settings = array(
		'options' => array(
			'Opt 1' => 'Opt 1',
			'Opt 2' => 'Opt 2'
		)
	);

	var $default_tag_params = array(
		'sort'      => '',
		'backspace' => '0'
	);

	var $total_option_levels = 1;

	// --------------------------------------------------------------------

	/**
	 * Display Field Settings
	 */
	function display_settings($data)
	{
		// load the language file
		$this->EE->lang->loadfile($this->class);

		$options = isset($data['options']) ? $data['options'] : array();
		$input_name = $this->class.'_options';

		$this->EE->table->add_row(
			lang($this->class.'_options', $input_name) . '<br />'
			. lang('field_list_instructions') . '<br /><br />'
			. lang('option_setting_examples'),

			'<textarea id="'.$input_name.'" name="'.$input_name.'" rows="6">'.$this->options_setting($options).'</textarea>'
		);
	}

	/**
	 * Display Cell Settings
	 */
	function display_cell_settings($data)
	{
		// load the language file
		$this->EE->lang->loadfile($this->class);

		$options = isset($data['options']) ? $data['options'] : array();

		return array(
			array(
				lang($this->class.'_options'),
				'<textarea class="matrix-textarea" name="options" rows="4">'.$this->options_setting($options).'</textarea>'
			)
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Save Field Settings
	 */
	function save_settings($data)
	{
		$post = $this->EE->input->post($this->class.'_options');

		// replace quotes
		$post = str_replace('"', '&quot;', $post);

		return array(
			'options' => $this->save_options_setting($post, $this->total_option_levels)
		);
	}

	/**
	 * Save Cell Settings
	 */
	function save_cell_settings($settings)
	{
		// replace quotes
		$settings['options'] = str_replace('"', '&quot;', $settings['options']);

		$settings['options'] = $this->save_options_setting($settings['options'], $this->total_option_levels);
		return $settings;
	}

	// --------------------------------------------------------------------

	/**
	 * Prep Field Data
	 *
	 * Ensures $field_data is an array.
	 */
	function prep_field_data(&$data)
	{
		if (! is_array($data))
		{
			$data = array_filter(preg_split("/[\r\n]+/", $data));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Display Field
	 */
	function display_field($data)
	{
		if (is_string($data)) $data = html_entity_decode($data);

		return $this->_display_field($data, $this->field_name);
	}

	/**
	 * Display Cell
	 */
	function display_cell($data)
	{
		return $this->_display_field($data, $this->cell_name);
	}

	// --------------------------------------------------------------------

	/**
	 * Save
	 */
	function save($data)
	{
		// replace quotes
		return str_replace('"', '&quot;', $data);
	}

	/**
	 * Save Cell
	 */
	function save_cell($data)
	{
		// replace quotes
		return $this->save($data);
	}

	// --------------------------------------------------------------------

	/**
	 * Find Options
	 */
	private function _find_option($needle, $haystack)
	{
		foreach ($haystack as $key => $value)
		{
			$r = $value;
			if ($needle == $key OR (is_array($value) AND (($r = $this->_find_option($needle, $value)) !== FALSE)))
			{
				return $r;
			}
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Replace Tag
	 */
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		if (! isset($this->settings['options']) || ! $this->settings['options'])
		{
			return $data;
		}

		if (! $tagdata)
		{
			return $this->replace_ul($data, $params);
		}

		$this->prep_field_data($data);
		$r = '';

		if ($this->settings['options'] && $data)
		{
			// optional sorting
			if (isset($params['sort']) && $params['sort'])
			{
				$sort = strtolower($params['sort']);

				if ($sort == 'asc')
				{
					sort($data);
				}
				else if ($sort == 'desc')
				{
					rsort($data);
				}
			}

			// offset and limit
			if (isset($params['offset']) || isset($params['limit']))
			{
				$offset = isset($params['offset']) ? $params['offset'] : 0;
				$limit = isset($params['limit']) ? $params['limit'] : count($data);
				$data = array_splice($data, $offset, $limit);
			}

			// prepare for {switch} and {count} tags
			$this->prep_iterators($tagdata);

			foreach($data as $option_name)
			{
				if (($option = $this->_find_option($option_name, $this->settings['options'])) !== FALSE)
				{
					// copy $tagdata
					$option_tagdata = $tagdata;

					// simple var swaps
					$option_tagdata = $this->EE->TMPL->swap_var_single('option', $option, $option_tagdata);
					$option_tagdata = $this->EE->TMPL->swap_var_single('option_name', $option_name, $option_tagdata);

					// parse {switch} and {count} tags
					$this->parse_iterators($option_tagdata);

					$r .= $option_tagdata;
				}
			}

			if (isset($params['backspace']) && $params['backspace'])
			{
				$r = substr($r, 0, -$params['backspace']);
			}
		}

		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Unordered List
	 */
	function replace_ul($data, $params = array())
	{
		return "<ul>\n"
		     .   $this->replace_tag($data, $params, "  <li>{option}</li>\n")
		     . '</ul>';
	}

	/**
	 * Ordered List
	 */
	function replace_ol($data, $params = array())
	{
		return "<ol>\n"
		     .   $this->replace_tag($data, $params, "  <li>{option}</li>\n")
		     . '</ol>';
	}

	// --------------------------------------------------------------------

	/**
	 * All Options
	 */
	function replace_all_options($data, $params = array(), $tagdata = FALSE, $options = FALSE, $iterator_count = 0)
	{
		if (! $tagdata)
		{
			return "<ul>\n"
			     .   $this->replace_all_options($data, $params, "  <li>{option}</li>\n")
			     . "</ul>";
		}

		PT_Multi_Fieldtype::prep_field_data($data);

		$r = '';

		if ($options === FALSE)
		{
			$options = $this->settings['options'];
		}

		if ($options)
		{
			// optional sorting
			if (isset($params['sort']) && $params['sort'])
			{
				$sort = strtolower($params['sort']);

				if ($sort == 'asc')
				{
					asort($options);
				}
				else if ($sort == 'desc')
				{
					arsort($options);
				}
			}

			// prepare for {switch} and {count} tags
			$this->prep_iterators($tagdata);
			$this->_iterator_count += $iterator_count;

			foreach($options as $option_name => $option)
			{
				if (is_array($option))
				{
					$sub_params = array_merge($params, array('backspace' => '0'));
					$r .= $this->replace_all_options($data, $sub_params, $tagdata, $option, $this->_iterator_count);
				}
				else
				{
					// copy $tagdata
					$option_tagdata = $tagdata;

					// simple var swaps
					$option_tagdata = $this->EE->TMPL->swap_var_single('option', $option, $option_tagdata);
					$option_tagdata = $this->EE->TMPL->swap_var_single('option_name', $option_name, $option_tagdata);
					$option_tagdata = $this->EE->TMPL->swap_var_single('selected', (in_array($option_name, $data) ? 1 : 0), $option_tagdata);

					// parse {switch} and {count} tags
					$this->parse_iterators($option_tagdata);

					$r .= $option_tagdata;
				}
			}

			if (isset($params['backspace']) && $params['backspace'])
			{
				$r = substr($r, 0, -$params['backspace']);
			}
		}

		return $r;
	}

	// --------------------------------------------------------------------

	/**
	 * Is Selected?
	 */
	function replace_selected($data, $params = array())
	{
		$this->prep_field_data($data);

		return (isset($params['option']) AND in_array($params['option'], $data)) ? 1 : 0;
	}

	/**
	 * Total Selections
	 */
	function replace_total_selections($data, $params = array())
	{
		$this->prep_field_data($data);

		return $field_data ? (string) count($data) : '0';
	}

}
