<?php

class Validation_Required
{
	// 同じタグ名の付いた項目どれかに値が存在すればOK
	public static function _validation_required_whichever($val, $group)
	{
		$this_ = \Validation::active();

	//	$group_label = is_array($group) ? \Arr::get($group, 1, \Arr::get($group, 0, '')) : $group;
	//	$group       = is_array($group) ? \Arr::get($group, 0, '') : $group;
		$group_field = array();

		//ぐるぐるして同じグループを持つフィールドを探す
		foreach ($this_->field() as $field)
		{
			foreach ($field->rules as $rule)
			{
				list($rule_name, $rule_options) = $rule;
				if ('required_whichever' == $rule_name &&
					\Arr::get($rule_options, 0) == $group)
				{
					if (!$this_->_empty($this_->input($field->name)))
					{
						return true;
					}
					$group_field[] = $field->label;
				}
			}
		}

		// 指定されたフィールドどれにも値が見つからなかった
		$message = 'The field ' . implode(', ', $group_field) . ' whichever must be required';
		$this_->set_message('required_whichever',
		                    \Lang::get('required_whichever', array(), $message));

		return false;
	}
}
