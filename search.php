<?php
/**
 * Created by PhpStorm.
 * User: http://unun.in
 * Date: 15-4-24
 * Time: 下午10:08
 */
include './XunSearch.php';
include './PDO.php';

$m = new myPdo();
$word = $_GET['w'];
if(empty($word)){
    exit('你没有输入任何东西');
}
$xun = new XunSearch('test');
$res = $xun->search($word);
print_r($res);
