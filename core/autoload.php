<?php

spl_autoload_register(
    callback: function($class) {
        include_once('../..' . '/' . strtolower(
                str_replace('\\', '/', $class)
            ) . '.php'
        );
    }
);
