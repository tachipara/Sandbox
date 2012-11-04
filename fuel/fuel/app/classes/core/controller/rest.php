<?php

class Controller_Rest extends Fuel\Core\Controller_Rest
{

	/**
	 * @var  null|string  Set this in a controller to use a default format
	 */
	protected $rest_format = 'json';

	/**
	 * Router
	 *
	 * Requests are not made to methods directly The request will be for an "object".
	 * this simply maps the object and method to the correct Controller method.
	 *
	 * @param  string
	 * @param  array
	 */
	public function router($resource, array $arguments)
	{
		\Config::load('rest', true);

		// If no (or an invalid) format is given, auto detect the format
		if (is_null($this->format) or ! array_key_exists($this->format, $this->_supported_formats))
		{
			// auto-detect the format
			$this->format = array_key_exists(\Input::extension(), $this->_supported_formats) ? \Input::extension() : $this->_detect_format();
		}

		//Check method is authorized if required
		if (\Config::get('rest.auth') == 'basic')
		{
			$valid_login = $this->_prepare_basic_auth();
		}
		elseif (\Config::get('rest.auth') == 'digest')
		{
			$valid_login = $this->_prepare_digest_auth();
		}

		//If the request passes auth then execute as normal
		if(\Config::get('rest.auth') == '' or $valid_login)
		{
			// If they call user, go to $this->post_user();
			$controller_method = strtolower(\Input::method()) . '_' . $resource;

			// Fall back to action_ if no rest method is provided
			if ( ! method_exists($this, $controller_method))
			{
				$controller_method = 'action_'.$resource;
			}

			// If method is not available, set status code to 404
			if (method_exists($this, $controller_method))
			{
                $this->_prepare_params($controller_method);
				return call_user_func_array(array($this, $controller_method), $arguments);
			}
			else
			{
				$this->response->status = $this->no_method_status;
				return;
			}
		}
		else
		{
			$this->response(array('status'=> 0, 'error'=> 'Not Authorized'), 401);
		}
	}

    /**
     * @brief prepare parameters
     * @param String $controller_method
     */
    protected function _prepare_params($controller_method)
    {
        $method = $this->request->get_method();
        $params = $this->request->params();
        $params = array_merge($params, Input::get());
        $params = array_merge($params, Input::$method());

        $param_defines = '_param_defines_'.$controller_method;
        if (!empty(static::$$param_defines))
        {
            $val = Validation::forge();
            foreach (static::$$param_defines as $param => $define)
            {
                if (!empty($define['validation']))
                {
                    $field = $val->add($param, $param);
                    foreach ($define['validation'] as $rule => $cond)
                    {
                        if (is_numeric($rule) and is_string($cond))
                        {
                            $field->add_rule($cond);
                        }
                        else
                        {
                            $field->add_rule($rule, $cond[0]);
                        }
                    }
                }
            }

            if ($val->run($params))
            {
                $this->request->named_params = $val->validated();
            }
            else
            {
                foreach ($val->error() as $param => $error)
                {
                    throw new \Exception($error);
                }
            }
        }
    }

}
