<?php

class Validation_Match
{
	// 指定したフィールドの値が指定した値と一致していればOK
	public static function _validation_match_field_value($val, $match_field, $match_value)
	{
		$this_ = \Validation::active();

		if ($match_value != $this_->input($match_field) ||
			$this_->_validation_required($val))
		{
			return true;
		}

		// 指定されたフィールドどれにも値が見つからなかった
		$message = 'The field '.$this_->field($match_field)->label.' must contain the value '.$match_value.'.';
		$this_->set_message('match_field_value',
		                    \Lang::get('match_field_value', array(), $message));

		return false;
	}
}
