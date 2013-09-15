<?php
/**
 * Fuel
 *
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package    Archelon
 * @version    1.0
 * @author     sharkpp
 * @license    MIT License
 * @copyright  20113 sharkpp
 * @link       http://www.sharkpp.net/
 */

class Observer_UserId extends Orm\Observer
{

	/**
	 * @var  string  default property to set the user id
	 */
	public static $property = 'user_id';

	/**
	 * @var  string  property to set the user id
	 */
	protected $_property;

	/**
	 * Set the properties for this observer instance, based on the parent model's
	 * configuration or the defined defaults.
	 *
	 * @param  string  Model class this observer is called on
	 */
	public function __construct($class)
	{
		$props = $class::observers(get_class($this));
		$this->_property = isset($props['property']) ? $props['property'] : static::$property;
	}

	/**
	 * Set the UserId property to the current user id.
	 *
	 * @param  Model  Model object subject of this observer method
	 */
	public function before_save(Orm\Model $obj)
	{
		$obj->{$this->_property} = \Auth::instance()->get_user_id()[1];
	}
}
