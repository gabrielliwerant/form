<?php if ( ! defined('DENY_ACCESS')) exit('403: No direct file access allowed');

/**
 * A Bright CMS
 * 
 * Core MVC/CMS framework used in TaskVolt and created for lightweight, custom
 * web applications.
 * 
 * @package A Bright CMS
 * @author Gabriel Liwerant
 */

/**
 * Form Class
 * 
 * Allows us to build, store, and return an HTML form with any form fields, 
 * inputs, labels, and formatting necessary.
 * 
 * @subpackage lib
 * @author Gabriel Liwerant
 */
class FormBuilder
{
	/**
	 * Stores the form action.
	 *
	 * @var string $_form_action
	 */
	private $_form_action;
	
	/**
	 * Stores the form method.
	 *
	 * @var string $_form_method
	 */
	private $_form_method;	
	
	/**
	 * Stores any labels for our form.
	 *
	 * @var array $_labels
	 */
	private $_labels = array();
	
	/**
	 * Stores any form input fields for our form.
	 *
	 * @var array $_fields
	 */
	private $_fields = array();
	
	/**
	 * Stores all the field meta data
	 *
	 * @var array $_field_meta
	 */
	private $_field_meta = array();
	
	/**
	 * Upon construction, we set the form action, method, and any form messages.
	 *
	 * @param string $form_action
	 * @param string $form_method
	 */
	public function __construct($form_action = null, $form_method = null)
	{
		$this->_setFormAction($form_action)->_setFormMethod($form_method);
	}
	
	/**
	 * Form action setter
	 *
	 * @param string $form_action
	 * 
	 * @return object FormBuilder
	 */
	private function _setFormAction($form_action)
	{
		$this->_form_action = $form_action;
		
		return $this;
	}
	
	/**
	 * Form method setter
	 *
	 * @param string $form_method
	 * 
	 * @return object FormBuilder
	 */
	private function _setFormMethod($form_method)
	{
		$this->_form_method = $form_method;
		
		return $this;
	}
	
	/**
	 * Builds form field attributes for HTML.
	 *
	 * @param string $name Name attribute
	 * @param string $id Field id
	 * @param string $class Class attribute
	 * @param string $maxlength Maxlength attribute
	 * @param string|void $type Type attribute
	 * @param string|void $value Value attribute
	 * @param string|void $size Size attribute
	 * 
	 * @return string
	 */
	private function _buildFieldAttributes(
		$name, 
		$id, 
		$class,
		$maxlength, 
		$type	= null, 
		$value	= null, 
		$size	= null
	)
	{
		$input	= array('name', 'id', 'class', 'maxlength', 'type', 'value', 'size');		
		$field	= null;

		foreach ($input as $val)
		{
			if ( ! empty($$val))
			{
				$attribute	= $val . '="' . $$val . '"';
				$field		.= $attribute . ' ';
			}
		}
		
		return $field;
	}

	/**
	 * Getter for required class name attribute.
	 *
	 * @param boolean $is_required
	 * 
	 * @return string 
	 */
	private function _getRequiredClassName($is_required)
	{
		if ($is_required)
		{
			$class = 'required';
		}
		else
		{
			$class = null;
		}
		
		return $class;
	}
	
	/**
	 * Build HTML label
	 *
	 * @param string $for For attribute for label
	 * @param string $text Text portion of label
	 * 
	 * @return string 
	 */
	public function buildLabel($for, $text)
	{
		return '<label for="' . $for . '">' . $text . '</label>';
	}	

	/**
	 * Form field setter
	 *
	 * @param string $key
	 * @param string $field
	 * 
	 * @return object FormBuilder
	 */
	public function setField($key, $field)
	{
		$this->_fields[$key] = $field;
		
		return $this;
	}	
	
	/**
	 * Allows us to populate a property with meta data for our fields.
	 *
	 * @param string $key Key name for field array
	 * @param array $meta_data Array with meta data for field
	 * 
	 * @return object FormBuilder
	 */
	public function setFieldMeta($key, $meta_data)
	{
		$this->_field_meta[$key] = $meta_data;
		
		return $this;
	}
	
	/**
	 * Get a specific field's meta data.
	 *
	 * @param string $key Field to return meta data for
	 * 
	 * @return array 
	 */
	public function getFieldMeta($key)
	{
		return $this->_field_meta[$key];
	}
	
	/**
	 * All field meta data getter
	 *
	 * @return array All stored field meta data
	 */
	public function getAllFieldMeta()
	{
		return $this->_field_meta;
	}
	
	/**
	 * Label setter
	 *
	 * @param string $key
	 * @param string $label
	 * 
	 * @return object FormBuilder
	 */
	public function setLabel($key, $label)
	{
		$this->_labels[$key] = $label;
		
		return $this;
	}
	
	/**
	 * Builds input field HTML.
	 *
	 * @param string $name Name attribute
	 * @param string $id Id Attribute
	 * @param string $maxlength Maxlength attribute
	 * @param boolean $is_required Tells us if field is required or not
	 * @param string|void $type Type attribute
	 * @param string|void $value Value attribute
	 * @param string|void $size Size attrivute
	 * 
	 * @return string HTML
	 */
	public function buildInput(
		$name, 
		$id, 
		$maxlength, 
		$is_required	= false,
		$type			= null, 
		$value			= null, 
		$size			= null
	)
	{
		$class	= $this->_getRequiredClassName($is_required);		
		$field	= $this->_buildFieldAttributes($name, $id, $class, $maxlength, $type, $value, $size);		

		return '<input ' . $field . '/>';
	}
	
	/**
	 * Builds a textarea field HTML.
	 *
	 * @param string $name Name attribute
	 * @param string $id Id Attribute
	 * @param boolean $is_required Tells us if field is required or not
	 * 
	 * @return string HTML
	 */
	public function buildTextArea($name, $id, $is_required = false)
	{
		$class	= $this->_getRequiredClassName($is_required);
		$field	= $this->_buildFieldAttributes($name, $id, $class, null, null, null, null);		

		return '<textarea ' . $field . '></textarea>';
	}
	
	/**
	 * Build select form field HTML.
	 *
	 * @param string $name Name attribute
	 * @param array $option_data Data to use for building the select options
	 * @param string $id Select id
	 * 
	 * @return string HTML
	 */
	public function buildSelect(
		$name, 
		$option_data, 
		$id, 
		$is_required = false
	)
	{
		$option = null;
		
		foreach ($option_data as $text => $value)
		{
			$option .= '<option value="' . $value . '">' . $text . '</option>';
		}

		$class	= $this->_getRequiredClassName($is_required);
		$field	= $this->_buildFieldAttributes($name, $id, $class, null);
		
		return '<select ' . $field . '>' . $option . '</select>';
	}
	
	/**
	 * Get stored field data based on array key.
	 *
	 * @param string $key
	 * 
	 * @return string 
	 */
	public function getField($key)
	{
		return $this->_fields[$key];
	}
	
	/**
	 * Get the array property with all the stored fields.
	 *
	 * @return array All the fields for the form
	 */
	public function getAllFields()
	{
		return $this->_fields;
	}

	/**
	 * Find the matching label to connect to the correct field and return it.
	 *
	 * @param string $field_key Key of field to find matching label for
	 * 
	 * @return string Matching label
	 */
	public function getLabelMatchingFieldKey($field_key)
	{
		foreach ($this->_labels as $for => $label)
		{
			if ($for === $field_key)
			{
				return $label;
			}
		}
	}
	
	/**
	 * Build the HTML form from all the appropriate properties with any desired
	 * id and individual fields and return the built form.
	 *
	 * @param string $fields HTML fields to enclose in form
	 * @param string|void $name Name attribute
	 * @param string}void $id Id attribute
	 * 
	 * @return string Built HTML form
	 */
	public function getForm($fields, $name = null, $id = null)
	{
		$action				= $this->_form_action;
		$method				= $this->_form_method;
		
		$form_attr_name		= array('action', 'method', 'name', 'id');
		$form_attributes	= null;
		
		foreach ($form_attr_name as $name)
		{
			if ( ! empty($$name))
			{
				$form_attributes .= $name . '="' . $$name . '" ';
			}
		}

		return '<form ' . $form_attributes . '>' . $fields . '</form>';
	}
	
	/**
	 * Searches through fields for an email field and then matches against user
	 * submitted data in a field with the same name.
	 *
	 * @param array $submitted_data Form data to look through
	 * 
	 * @return string/boolean Either the submitted email address or false
	 */
	public function findUserEnteredEmail($submitted_data)
	{
		foreach ($this->_field_meta as $name => $field_data)
		{
			if ( ! isset($field_data['is_email']))
			{
				continue;
			}

			$is_email = (boolean)$field_data['is_email'];

			if ($is_email AND isset($submitted_data[$name]))
			{
				return $submitted_data[$name];
			}
			elseif ($is_email AND ! isset($submitted_data[$name]) )
			{
				return false;
			}
		}
		
		return false;
	}
}
// End of Form Class

/* EOF lib/Form.php */