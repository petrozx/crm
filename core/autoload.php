<?php

spl_autoload_register(
    callback: function($class) {
        include_once($_SERVER['DOCUMENT_ROOT'].'/' . (
                str_replace('\\', '/', $class)
            ) . '.php'
        );
    }
);
