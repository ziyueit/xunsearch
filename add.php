<?php
/**
 * Created by PhpStorm.
 * User: http://unun.in
 * Date: 15-4-24
 * Time: 下午09:35
 */
include './PDO.php';
include './XunSearch.php';
$xun = new XunSearch('test');
if($_POST['info']){
    $info = $_POST['info'];
    $m = new myPdo();
    if(empty($info['title']) || empty($info['text'])){
        exit('输入的信息不完整！<br/><a href="add.html">返回添加数据</a>');
    }
    if($m->add($info,'test')){
        $info['cid'] = $m->getLastInsertId();
        $xun = new XunSearch('test');
        $xun->addIndex($info);
        $xun->flushIndex();
    }else{
        echo '失败';
    }
}else{
    echo '失败';
}
echo '<br/><a href="add.html">返回添加数据</a>';

