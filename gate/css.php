<?php
header('Content-type: text/css');
// prevent caching generated css file
echo @file_get_contents('data/generated/types.css');