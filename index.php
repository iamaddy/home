<?php
/**
 * 统一入口文件
 */

//统一当前目录
chdir(dirname(__FILE__));
define('_ROOT_', str_replace('\\', '/', realpath(dirname(__FILE__))));
//注册自动加载器
include 'init_autoloader.php';

//应用执行
Application::init(include('config/application.config.php'))->run();
