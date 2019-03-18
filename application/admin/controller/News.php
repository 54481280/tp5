<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 艺品网络 
// +----------------------------------------------------------------------

namespace app\admin\controller;


class News extends Admin {

    public function index(){
        return $this->fetch();
    }

    public function add(){
        if(request()->isPost()){

        }else{
            return $this->fetch('edit');
        }

    }


}
