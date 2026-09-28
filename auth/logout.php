<?php
session_start();
session_destroy();
header('Location: /Barz/index.php?page=home');
exit;
