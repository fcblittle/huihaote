<?php
// +----------------------------------------------------------------------
// | IXCore
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://www.interidea.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: page7 <zhounan0120@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * 分页显示类
 +------------------------------------------------------------------------------
 * @category   Extend
 * @package  Extend
 * @subpackage  Util
 * @author    page7 <zhounan0120@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
class Page extends Base
{//类定义开始

    /**
     +----------------------------------------------------------
     * 分页起始行数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    protected $firstRow ;

    /**
     +----------------------------------------------------------
     * 列表每页显示行数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    protected $listRows ;

    /**
     +----------------------------------------------------------
     * 页数跳转时要带的参数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    protected $parameter  ;

    /**
     +----------------------------------------------------------
     * 分页总页面数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    protected $totalPages  ;

    /**
     +----------------------------------------------------------
     * 总行数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    protected $totalRows  ;

    /**
     +----------------------------------------------------------
     * 当前页数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    protected $nowPage    ;

    /**
     +----------------------------------------------------------
     * 分页栏每页显示的页数
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */
    protected $rollPage   ;

    /**
     +----------------------------------------------------------
     * 分页记录名称
     +----------------------------------------------------------
     * @var integer
     * @access protected
     +----------------------------------------------------------
     */

    // 分页显示定制
    protected $config   =   array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页');

    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $totalRows  总的记录数
     * @param array $firstRow  起始记录位置
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     +----------------------------------------------------------
     */
    public function __construct($totalRows, $listRows='', $parameter='')
    {
        $this->totalRows = $totalRows;
        $this->parameter = $parameter;
        $this->rollPage = C('PAGE_NUMBERS');
        $this->listRows = !empty($listRows)?$listRows:C('LIST_NUMBERS');
        $this->totalPages = ceil($this->totalRows/$this->listRows);
        $this->nowPage  = !empty($_GET[C('VAR_PAGE')])&&($_GET[C('VAR_PAGE')] >0)?$_GET[C('VAR_PAGE')]:1;

        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
     +----------------------------------------------------------
     * 分页显示
     * 用于在页面显示的分页栏的输出
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    public function show($isArray=false){

        if(0 == $this->totalRows) return;
        if(stristr($_SERVER['REQUEST_URI'], '&'.C('VAR_PAGE').'=') === false && stristr($_SERVER['REQUEST_URI'], '?'.C('VAR_PAGE').'=') === false) {
            $url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
        }else{
            $url = preg_replace("/([&]|[?])p=[0-9]+/", "", $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter);
        }

        $prev = floor(($this -> rollPage - 1)/2);
        $suff = floor(($this -> rollPage)/2);

        //< << >> >
        $prevPage = '';
        $nextPage = '';
        $theFirst = '';
        $theLast  = '';
        if($this -> nowPage + $suff < $this -> totalPages)
            $theLast = $this->totalPages;
        if($this -> nowPage - $prev > 1)
            $theFirst = 1;
        if($this -> nowPage != 1)
            $prevPage = $this -> nowPage - 1;
        if($this -> nowPage != $this -> totalPages)
            $nextPage = $this -> nowPage + 1;


        // 1 2 3 4 5
        if($this -> nowPage <= $prev)
        {
            $start = 1;
            $end = ($this -> rollPage > $this -> totalPages) ? $this -> totalPages : $this -> rollPage;
        }else if($this -> nowPage >= $this -> totalPages - $suff){
            $start = ($this -> totalPages - $this -> rollPage + 1 < 1) ? 1 : ($this -> totalPages - $this -> rollPage + 1);
            $end = $this -> totalPages;
        }else{
            $start = ($this -> nowPage - $prev < 1) ? 1 : ($this -> nowPage - $prev);
            $end = ($this -> nowPage + $suff > $this -> totalPages) ? $this -> totalPages : ($this -> nowPage + $suff);
        }

        if($isArray) {
            $linkPage = Array();
            for($page=$start; $page<=$end; $page++){
                if($page!=$this->nowPage){
                    if($page<=$this->totalPages){
                        $linkPage[$page]= '<a href="'.$url.'&'.C('VAR_PAGE').'='.$page.'">'.$page.'</a>';
                    }else{
                        break;
                    }
                }else{
                    if($this->totalPages != 1){
                        $linkPage[$page] = '<span>'.$page.'</span>';
                    }
                }
            }

            $pageArray['varpage']   =   $url.'&'.C('VAR_PAGE').'=';
            $pageArray['totalRows'] =   $this->totalRows;
            $pageArray['prevPage']  =   $prevPage;
            $pageArray['nextPage']  =   $nextPage;
            $pageArray['totalPages']=   $this->totalPages;
            $pageArray['firstPage'] =   $theFirst;
            $pageArray['lastPage']   =   $theLast;
            $pageArray['linkPages'] =   $linkPage;
            $pageArray['nowPage'] =   $this->nowPage;
            return $pageArray;
        }else{
            $linkPage = "";
            for($page=$start; $page<=$end; $page++){
                if($page!=$this->nowPage){
                    if($page<=$this->totalPages){
                        $linkPage .= '&nbsp;<a href="'.$url.'&'.C('VAR_PAGE').'='.$page.'">'.$page.'</a>';
                    }else{
                        break;
                    }
                }else{
                    if($this->totalPages != 1){
                        $linkPage .= " [".$page."]";
                    }
                }
            }

            if($prevPage) $prevPage = '<a href="'.$url.'&'.C('VAR_PAGE').'='.$prevPage.'">'.$this -> config['prev'].'</a>';
            if($nextPage) $nextPage = '<a href="'.$url.'&'.C('VAR_PAGE').'='.$nextPage.'">'.$this -> config['next'].'</a>';
            if($theFirst) $theFirst = '<a href="'.$url.'&'.C('VAR_PAGE').'='.$theFirst.'">'.$this -> config['first'].'</a>';
            if($theLast)   $theLast = '<a href="'.$url.'&'.C('VAR_PAGE').'='.$theLast.'">'.$this -> config['last'].'</a>';
            $pageStr = $theFirst.' '.$prevPage.' '.$linkPage.' '.$nextPage.' '.$theLast.' / '.$this->totalRows.$this->config['header'];
        	return $pageStr;
		}
    }

}//类定义结束
?>