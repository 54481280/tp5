<?php
// +----------------------------------------------------------------------
// | TwoThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.twothink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace app\home\controller;
use app\home\model\Document;
use app\home\model\Rent;
use OT\DataDictionary;
use think\Config;
use think\Db;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class Index extends Home{

	//系统首页
    public function index(){
        return $this->fetch();
    }

    //服务页面
    public function server(){
        return $this->fetch();
    }

    //关于我们
    public function aboutOur(){
        $res = Db::table('about_our')->find(1);
        $this->assign('content',$res['content']);
        return $this->fetch();
    }

    //小区租售列表
    public function rent(){
        $list = Rent::where('create_time','<',time())
            ->where('state','1')
            ->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    //租售详情
    public function rentShow($id){
        $rent = Rent::get($id);
        $this->assign('rent',$rent);
        return $this->fetch();
    }



}
