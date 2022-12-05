<?php

spl_autoload_register(
    callback: function($class) {
        include_once($_SERVER['DOCUMENT_ROOT'] . '/' . strtolower(
                str_replace('\\', '/', $class)
            ) . '.php'
        );
    }
);

\core\classes\Env::link(
    '../../'
);
