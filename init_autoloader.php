<?php

//注册DDCLICK自动加载器
$ddclicklib = dirname(__FILE__).'/vendor/core';

include $ddclicklib.'/Autoloader.php';
Autoloader::register();
Autoloader::addDefaultPath($ddclicklib);

//注册其他类库的自动加载器放在以下
