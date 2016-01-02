<?php
/**
 * Created by PhpStorm.
 * User: http://unun.in
 * Date: 15-4-24
 * Time: 下午09:31
 */
class XunSearch
{

    private $_xindex;
    private $_xsearch;
    private $_project;

    public function __construct($project)
    {
        //这个路径可以根据自己的情况改成你自己的
        require_once SITE_ROOT.'/tool/xunsearch/lib/XS.php';
        $xs = new XS($project);
        $this->_project = $project;
        $this->_xindex = $xs->index;
        $this->_xsearch = $xs->search;
        $this->_xsearch->setCharset('UTF-8');
    }

    /**
     * todo 待改进，还有问题
     * @param $keyWord
     * @param int $row
     * @param int $jumpNum
     * @return array
     */
    public function searchIndex($keyWord,$row,$jumpNum)
    {
        $xs = new XS($this->_project);
        //开启模糊搜索
        $xs->search->setFuzzy();
        //开启同义词搜索
        $xs->search->setAutoSynonyms();
        $xs->search->setQuery($keyWord);
        //设置搜索结果的数量和偏移用于搜索结果分页, 每次调用search后会还原这2个变量到初始值
        $xs->search->setLimit($row, $jumpNum);
        //执行搜索，并将搜索结果文档保存到$data中
        $docs = $xs->search->search();
        //获取搜索结果总数的估算值
        $count = $xs->search->count();
        /*if($count){
            $data = array();
            foreach ($docs as $key=>$doc){
                $data[$key]['id'] = $doc->id;
                $data[$key]['name'] = $xs->search->highlight(htmlspecialchars($doc->name));
            }
            return array('data'=>$data,'count'=>$count);
        }*/
        return array('data'=>$docs,'count'=>$count,'obj'=>$xs);
    }

    /**
     * 添加/更新索引到索引队列中，默认是更新
     * @param $data
     * @param bool $update
     */
    public function addIndex($data,$update = true )
    {
        $document = new XSDocument;
        $document->setFields($data);
        if($update){
            $this->_xindex->update($document);
        }else{
            $this->_xindex->add($document);
        }
    }

    /**
     * 强制刷新服务端的当前库的索引缓存
     * @return bool
     */
    public function flushIndex()
    {
        return $this->_xindex->flushIndex();
    }

    /**
     * 完全清空索引数据
     */
    public function clearIndex()
    {
        $this->_xindex->clean();
    }

    /**
     * 删除索引
     * @param array|int $id
     * array('123', '789', '456') 删除主键为 123, 789, 456 的记录
     * 123 删除主键为 123 的记录
     */
    public function delete($id)
    {
        $this->_xindex->del($id);
    }

    /**
     * 获取热门搜索词列表
     * @param $num 需要返回的热门搜索数量上限, 最大值为50
     * @param string $type 排序类型, 默认为 total(搜索总量), 可选值还有 lastnum(上周), currnum(本周)
     * @return mixed
     */
    public function hotWord($num,$type='currnum')
    {
        return $this->_xsearch->getHotQuery($num,$type);
    }

    /**
     * 读取展开的搜索词，主要用于做搜索建议和搜索纠错
     * @param $keyWord
     * @param int $num 需要返回的搜索词最大数量 默认为10, 最大值为20
     * @return array 返回展开的搜索词组成的数组
     */
    public function expanded($keyWord,$num=10)
    {
        return $this->_xsearch->getExpandedQuery($keyWord,$num);
    }

    /**
     * 最近一次搜索的结果匹配总数估算值
     * @return int
     */
    public function lastCount()
    {
        return $this->_xsearch->getLastCount();
    }

    /**
     * 添加同义词
     * @param $word
     * @param $Synonym
     */
    public function addSynonym($word,$Synonym)
    {
        $this->_xindex->addSynonym($word,$Synonym);
    }

    /**
     * 强制刷新服务端当前项目的搜索日志
     * @return bool
     */
    public function flushLog()
    {
        return $this->_xindex->flushLogging();
    }
}
