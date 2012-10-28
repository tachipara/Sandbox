<?php

Autoloader::add_classes(array(
    'MyOrm\\Observer_Validation' => __DIR__.'/classes/observer/validation.php',

    // Exceptions
    'MyOrm\\ValidationFailed' => __DIR__.'/classes/observer/validation.php',
));
