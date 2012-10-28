<?php

class Model_User extends \Orm\Model
{
    const TABLE_NAME          = 'user';
    const ID_PROPERTY         = 'id';
    const USER_ID_PROPERTY    = 'user_id';
    const NAME_PROPERTY       = 'name';
    const EMAIL_PROPERTY      = 'email';
    const CREATED_AT_PROPERTY = 'created_at';
    const UPDATED_AT_PROPERTY = 'updated_at';

    protected static $_table_name = self::TABLE_NAME;

	protected static $_properties = array(
        self::ID_PROPERTY,
        self::USER_ID_PROPERTY => array(
            'validation' => array(
                'trim',
                'required',
                'max_length' => array(31),
                'match_pattern' => array('/^[0-9a-zA-Z_]*$/'),
                'unique' => array(array(
                    self::TABLE_NAME,
                    self::USER_ID_PROPERTY,
                )),
            ),
        ),
        self::NAME_PROPERTY => array(
            'validation' => array(
                'trim',
                'required',
                'max_length' => array(127),
            ),
        ),
        self::EMAIL_PROPERTY => array(
            'validation' => array(
                'required',
                'max_length' => array(127),
                'valid_email',
                'unique' => array(array(
                    self::TABLE_NAME,
                    self::EMAIL_PROPERTY,
                )),
            ),   
        ),
        self::CREATED_AT_PROPERTY,
        self::UPDATED_AT_PROPERTY,
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => true,
		),
        'Orm\Observer_Validation' => array('before_save'),
        //'MyOrm\Observer_Validation' => array('before_save'),
	);

}
