<?php

spl_autoload_register(
    callback: function($class) {
        include_once(__DIR__ . '/' . strtolower(
                str_replace('\\', '/', $class)
            ) . '.php'
        );
    }
);
