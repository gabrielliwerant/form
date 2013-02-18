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
 * FormValidator Class
 * 
 * @subpackage form
 * @author Gabriel Liwerant
 */
class FormValidator
{
	/**
	 * Store form validation messages.
	 *
	 * @var array $_form_validator_message
	 */
	private $_form_validator_message = array();
	
	/**
	 * Nothing to see here...
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Form validation messages setter
	 *
	 * @param string $key
	 * @param string $message 
	 * 
	 * @return object FormValidator
	 */
	public function setFormValidatorMessage($key, $message)
	{
		$this->_form_validator_message[$key] = $message;
		
		return $this;
	}
	
	/**
	 * Form validation messages getter
	 *
	 * @param string $key
	 * 
	 * @return string 
	 */
	public function getFormValidatorMessage($key)
	{
		return $this->_form_validator_message[$key];
	}
	
	/**
	 * Overwrite required field form validation message with an introduction and
	 * the specific field left empty.
	 *
	 * @param string $field_name The field that was left empty
	 * @param string $old_message_key Key to get old message for overwriting
	 * 
	 * @return object FormValidator
	 */
	public function appendRequiredFieldNameToFormMessage($field_name, $old_message_key)
	{
		$old_message = $this->getFormValidatorMessage($old_message_key);
		
		$new_message = 
			'The '
			. ucfirst($field_name)
			. ' field is empty. ' 
			. $old_message;
		
		$this->setFormValidatorMessage($old_message_key, $new_message);
		
		return $this;
	}
	
	/**
	 * Run data through a honeypot check.
	 * 
	 * A honeypot is an input field in a form that is hidden with css. Because
	 * a normal user should not see it, it should remain empty when the form is
	 * submitted. If it is filled, we may assume it was filled by a bot, and we
	 * can handle the form as such.
	 *
	 * @param string $value Value to check
	 * 
	 * @return boolean Result of check
	 */
	public function isValidAgainstHoneypot($value)
	{
		if ( ! empty($value))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/**
	 * Validates against a honeypot form check.
	 * 
	 * We use all the field meta data to look for our honeypot. Then we make
	 * a form validator object to perform our check. If we're in logging mode
	 * and we fail the check, log it. Return the result of the check.
	 *
	 * @param object $field_meta_data Our field data to search
	 * @param string $meta_key Key to match to find honeypot field
	 * @param array $submitted_data User-submitted form data to check against
	 * 
	 * @return mixed Result of the check, true on success, result on failure
	 */
	public function validateHoneypotAgainstSubmittedData($field_meta_data, $meta_key, $submitted_data)
	{
		foreach ($field_meta_data as $field_name => $field_data)
		{
			if ( ! isset($field_data[$meta_key]))
			{
				continue;
			}
			
			$is_honeypot = (boolean)$field_data[$meta_key];

			if ($is_honeypot)
			{
				if (isset($submitted_data[$field_name]))
				{
					$is_valid = $this->isValidAgainstHoneypot($submitted_data[$field_name]);
				}
				else
				{
					$is_valid = false;
				}

				if ( ! $is_valid)
				{				
					return $submitted_data[$field_name];
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Run data through a spam check.
	 * 
	 * A spam check asks the user a question on a form that is intended to
	 * disqualify spam bots. We run the user-submitted data through our list of
	 * possible answers.
	 *
	 * @param array $spam_check_answer_data List of acceptable spam answers
	 * @param string $submitted_answer User-submitted answer
	 * 
	 * @return boolean 
	 */
	public function isValidAgainstSpamCheck($spam_check_answer_data, $submitted_answer)
	{
		foreach ($spam_check_answer_data as $value)
		{
			if (strtolower($submitted_answer) === strtolower($value))
			{
				return true;
			}
		}

		return false;
	}
	
	/**
	 * Validate data against any spam checking.
	 * 
	 * If we find a spam check field, we loop through the correct values to find
	 * a match. If we do, we have passed the validation. If the loop doesn't 
	 * find any true answers, we have failed validation. If there are no spam 
	 * check fields, we have passed the validation.
	 *
	 * @param array $field_meta_data Stored field meta data
	 * @param array $meta_key Key to look for in finding meta spam check field
	 * @param string $answer_key Key to look for in user-submitted data
	 * @param array $submitted_data User submitted data
	 * 
	 * @return mixed Result of the check, true on success, result on failure
	 */
	public function validateSpamCheckAgainstSubmittedData($field_meta_data, $meta_key, $answer_key, $submitted_data)
	{
		$is_valid = false;
		
		foreach ($field_meta_data as $field_name => $field_data)
		{
			if ( ! isset($field_data[$meta_key]) OR ! isset($field_data[$answer_key]) )
			{
				continue;
			}
			
			$is_spam_check_field = (boolean)$field_data[$meta_key];

			if ($is_spam_check_field)
			{
				if (isset($submitted_data[$field_name]))
				{
					return $this->isValidAgainstSpamCheck($field_data[$answer_key], $submitted_data[$field_name]);
				}
				else
				{
					return $submitted_data;
				}
			}
		}
		
		return $is_valid;
	}
	
	/**
	 * Run data through a required field check.
	 * 
	 * A required field is one which must have data submitted to it in order to
	 * pass the check.
	 *
	 * @param string $submitted_data To check for any data
	 * 
	 * @return boolean 
	 */
	public function isValidAgainstRequiredField($submitted_data)
	{
		if (empty($submitted_data))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/**
	 * Validate data against required fields.
	 * 
	 * We use a loop to search for values in required fields and exit as soon as
	 * we find an empty field with a required value. Otherwise, we have passed
	 * verification.
	 *
	 * @param array $field_meta_data Form field meta data to loop through
	 * @param string $meta_key Key to search field meta against
	 * @param array $submitted_data User-submitted form data to check against
	 * @param string|void $old_message_key Key for message appending
	 * @param boolean $does_append_err_msg If we should append our error message
	 * 
	 * @return boolean Result of the check
	 */
	public function validateRequiredFieldsAgainstSubmittedData(
		$field_meta_data, 
		$meta_key, 
		$submitted_data,
		$old_message_key = null,
		$does_append_err_msg = true		
	)
	{
		foreach ($field_meta_data as $field_name => $field_data)
		{			
			if ( ! isset($field_data[$meta_key]))
			{
				continue;
			}
			
			$is_required = (boolean)$field_data[$meta_key];

			if ($is_required)
			{
				if (isset($submitted_data[$field_name]))
				{
					$is_valid = $this->isValidAgainstRequiredField($submitted_data[$field_name]);
				}
				else
				{
					$is_valid = false;
				}

				if ( ! $is_valid)
				{
					// Append the name of the field to the message for display
					if ($does_append_err_msg)
					{
						$this->appendRequiredFieldNameToFormMessage($field_name, $old_message_key);
					}

					return false;
				}
			}
		}
		
		return true;
	}
}
// End of FormValidator Class

/* EOF form/FormValidator.php */