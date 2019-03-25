<?php

namespace app\home\controller;
use think\Controller;
use think\Validate;


class Property extends Controller
{
    /*
     * 在线报修表单
     */
    public function index(){
        return $this->fetch();
    }

    /*
     * 提交报修功能
     */
    public function add(){
        $Property = model('Property');//获取模型实例
        $post_data = request()->post();//获取post数据
        $validate = new Validate([
            'title' => 'require',
            'name' => 'require',
            'phone' => 'require',
            'address' => 'require',
            'parse' => 'require',
        ],[
            'title.require' => '标题不能为空',
            'name.require' => '名字不能为空',
            'phone.require' => '联系电话不能为空',
            'address.require' => '地址不能为空',
            'parse.require' => '内容不能为空',
        ]);

        if(!$validate->check($post_data)){
            $this->error($validate->getError());
        }

        //获取添加时间
        $post_data['add_time'] = time();

        $data = $Property->create($post_data);//新增数据

        if($data){
            $this->success('报修成功','Index/index');
        }else{
            $this->error($Property->getError());
        }
    }


}
