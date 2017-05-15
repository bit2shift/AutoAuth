<?php
spl_autoload_register();

// Replace 'SMFUser' with the appropriate class from 'hooks\'
return new hooks\SMFUser('..');
