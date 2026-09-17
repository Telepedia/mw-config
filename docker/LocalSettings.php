<?php
// Maintenance scripts spawned by through shellbox etc get a cleared environment and therefore cannot pick up 
// the location of our LocalSettings.php file, so we copy this to the root as a helper which just loads the relevant 
// file
require_once '/srv/config/LocalSettings.php';
