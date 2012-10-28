<?php
/**
 * Fuel is a fast, lightweight, community driven PHP5 framework.
 *
 * @package		Fuel
 * @version		1.0
 * @author		Fuel Development Team
 * @license		MIT License
 * @copyright	2010 - 2012 Fuel Development Team
 * @link		http://fuelphp.com
 */

namespace MyOrm;

// Exception to throw when validation failed
class ValidationFailed extends \FuelException
{
	protected $validation;

	/**
	 * Overridden \FuelException construct to add a Validation instance into the exception
	 *
	 * @param string
	 * @param int
	 * @param Exception
	 * @param Validation
	 */
	public function __construct($message = null, $code = 0, \Exception $previous = null, \Validation $validation = null)
	{
		parent::__construct($message, $code, $previous);

        $this->validation = $validation;
	}

	/**
	 * Gets the Validation from this exception
	 *
	 * @return Validation
	 */
	public function get_validation()
	{
		return $this->validation;
	}
}

class Observer_Validation extends \Orm\Observer
{

	/**
	 * Set a Model's properties on a Validation, which will be created with the Model's
	 * classname if none is provided.
	 *
	 * @param   string
     * @param   array
	 * @param   Validation|null
	 * @return  Validation
	 */
	public static function set_rules($obj, $input = array(), $validation = null)
	{
		$class = is_object($obj) ? get_class($obj) : $obj;
		if (is_null($validation))
		{
			$validation = \Validation::instance($class);
			if ( ! $validation)
			{
				$validation = \Validation::forge($class);
			}
		}

		$primary_keys = is_object($obj) ? $obj->primary_key() : $class::primary_key();
		$properties = is_object($obj) ? $obj->properties() : $class::properties();
		foreach ($properties as $p => $settings)
		{
			if (! isset($input[$p]) or in_array($p, $primary_keys))
			{
				continue;
			}

			// create the field and add validation rules
            $field = $validation->add($p, $input[$p]);
			if ( ! empty($settings['validation']))
			{
				foreach ($settings['validation'] as $rule => $args)
				{
					if (is_int($rule) and is_string($args))
					{
						$args = array($args);
					}
					else
					{
						array_unshift($args, $rule);
					}
                    
                    call_user_func_array(array($field, 'add_rule'), $args);
				}
			}
		}

		return $validation;
	}

	/**
	 * Execute before saving the Model
	 *
	 * @param   \Orm\Model
	 * @throws  ValidationFailed
	 */
	public function before_save(\Orm\Model $obj)
	{
		return $this->validate($obj);
	}

	/**
	 * Validate the model
	 *
	 * @param   \Orm\Model
	 * @throws  ValidationFailed
	 */
	public function validate(\Orm\Model $obj)
	{
		// only allow partial validation on updates, specify the fields for updates to allow null
		$allow_partial = $obj->is_new() ? false : array();

		$input = array();
		foreach (array_keys($obj->properties()) as $p)
		{
			if ( ! in_array($p, $obj->primary_key()) and $obj->is_changed($p))
			{
				$input[$p] = $obj->{$p};
				is_array($allow_partial) and $allow_partial[] = $p;
			}
		}

		$val = static::set_rules($obj, $input);

		if ( ! empty($input) and $val->run($input, $allow_partial, array($obj)) === false)
		{
			throw new ValidationFailed($val->show_errors(), 0, null, $val);
		}
		else
		{
			foreach ($input as $k => $v)
			{
				$obj->{$k} = $val->validated($k);
			}
		}
	}
}
