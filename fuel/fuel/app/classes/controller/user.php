<?php

class Controller_User extends Controller_Rest
{

    /**
     * @brief post user
     * @return Response
     */
	public function post_user()
    {
        $params = $this->params(Model_User::USER_ID_PROPERTY);
        if (empty($params[Model_User::USER_ID_PROPERTY]))
        {
            throw new \Exception('user_id is empty.');
        }
        else
        {
            $user_id = $params[Model_User::USER_ID_PROPERTY];
        }

        try
        {
            $user = Model_User::forge(array(
                Model_User::USER_ID_PROPERTY => $user_id,
                Model_User::NAME_PROPERTY => Input::post(Model_User::NAME_PROPERTY),
                Model_User::EMAIL_PROPERTY => Input::post(Model_User::EMAIL_PROPERTY),
            ));
            $user->save();
        } catch(Orm\ValidationFailed $ve)
        {
            $code = 400;
            $error = $ve->getMessage();
            //$error = 'validation error.';
        } catch (Exception $e)
        {
            $code = 500;
            $error = 'post user failed.';
        }

        $result = array();
        if (empty($error))
        {
            $code = 200;
            $result['Result'] = array(
                'status' => 'success',
            );
        }
        else
        {
            $result['Error'] = array(
                'message' => $error,
            );
        }
        $this->response($result, $code);
	}

    /**
     * @brief get user
     * @return Response
     */
	public function get_user()
    {
        $params = $this->params(Model_User::USER_ID_PROPERTY);
        if (empty($params[Model_User::USER_ID_PROPERTY]))
        {
            throw new \Exception('user_id is empty.');
        }
        else
        {
            $user_id = $params[Model_User::USER_ID_PROPERTY];
        }

        try
        {
            $user = Model_User::find('first', array(
                'select' => array(
                    Model_User::USER_ID_PROPERTY,
                    Model_User::NAME_PROPERTY,
                    Model_User::EMAIL_PROPERTY,
                ),
                'where' => array(
                    array(Model_User::USER_ID_PROPERTY, $user_id),
                ),
                'limit' => array(1),
            ));
        } catch (Exception $e)
        {
            $code = 500;
            $error = 'get user failed.';
        }

        if (!empty($user))
        {
            $user = $user->to_array();
            unset($user[Model_User::ID_PROPERTY]);
        }
        else
        {
            $code = 404;
            $error = 'user not found.';
        }

        $result = array();
        if (empty($error))
        {
            $code = 200;
            $result['Result'] = $user;
        }
        else
        {
            $result['Error'] = array(
                'message' => $error,
            );
        }
        $this->response($result, $code);
	}

    /**
     * @brief put user
     * @return Response
     */
	public function put_user()
    {
        $params = $this->params(Model_User::USER_ID_PROPERTY);
        if (empty($params[Model_User::USER_ID_PROPERTY]))
        {
            throw new \Exception('user_id is empty.');
        }
        else
        {
            $user_id = $params[Model_User::USER_ID_PROPERTY];
        }

        try
        {
            $user = Model_User::find('first', array(
                'where' => array(
                    array(Model_User::USER_ID_PROPERTY, $user_id),
                ),
                'limit' => array(1),
            ));
            $user->set(Model_User::NAME_PROPERTY, Input::put(Model_User::NAME_PROPERTY));
            $user->save();
        } catch(Orm\ValidationFailed $ve)
        {
            $code = 400;
            $error = $ve->getMessage();
            //$error = 'validation error.';
        } catch (Exception $e)
        {
            $code = 500;
            $error = $e->getMessage();
            //$error = 'put user failed.';
        }

        $result = array();
        if (empty($error))
        {
            $code = 200;
            $result['Result'] = array(
                'status' => 'success',
            );
        }
        else
        {
            $result['Error'] = array(
                'message' => $error,
            );
        }
        $this->response($result, $code);
	}

    /**
     * @brief delete user
     * @return Response
     */
	public function delete_user()
    {
        $params = $this->params(Model_User::USER_ID_PROPERTY);
        if (empty($params[Model_User::USER_ID_PROPERTY]))
        {
            throw new \Exception('user_id is empty.');
        }
        else
        {
            $user_id = $params[Model_User::USER_ID_PROPERTY];
        }

        try
        {
            $user = Model_User::find('first', array(
                'select' => array(
                    Model_User::ID_PROPERTY,
                ),
                'where' => array(
                    array(Model_User::USER_ID_PROPERTY, $user_id),
                ),
                'limit' => array(1),
            ));
            $user->delete();
        } catch (Exception $e)
        {
            $code = 500;
            $error = 'delete user failed.';
        }

        $result = array();
        if (empty($error))
        {
            $code = 200;
            $result['Result'] = array(
                'status' => 'success',
            );
        }
        else
        {
            $result['Error'] = array(
                'message' => $error,
            );
        }
        $this->response($result, $code);
	}

}
