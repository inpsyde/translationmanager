<?php

// Register autoloader.
require_once dirname( __FILE__ ) . '/includes/tm4mlp/class-loader.php';
spl_autoload_register( array( new \Tm4mlp\Loader(), 'load_class' ) );
