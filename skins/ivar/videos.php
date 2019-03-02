<?php

global $limbs;
echo $limbs->twig->render('videos.html', array('url' => $_SERVER['REQUEST_URI']));