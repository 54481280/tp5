<?php
/**
 * Created by PhpStorm.
 * User: zhaozhiyun.
 * Date: 2019/3/15 0015
 * Time: 10:13
 */

namespace app\admin\controller;


use think\Validate;

class Property extends Admin
{
    /*
     * 在线报修列表
     */
    public function index(){
        $keyword = request()->param()['title'];//接收参数

        if($keyword){
            $list = \think\Db::name("Property")
                    ->where('title','like',"%$keyword%")
                    ->field(true)
                    ->order('id asc');
        }else{
            $list       =   \think\Db::name("Property")->field(true)->order('id asc');
        }
        $list  = $list->paginate(10,false,['query'=>['title' => $keyword]]);//参数分页

        $this->assign('list',$list);
        $this->assign('meta_title','用户信息');
        return $this->fetch();
    }

    /*
     * 新增
     */
    public function add(){
        if(request()->isPost()) {
            $Property = model('Property');//获取模型实例
            $post_data = request()->post();//获取post数据
//            print_r($post_data);exit;
//            var_dump($post_data);exit;
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
                $this->success('新增成功',Cookie('__forward__'));
            }else{
                $this->error($Property->getError());
            }
        }
        else{

            return $this->fetch('edit');
        }
    }
    
    /*
     * 编辑
     */
    public function edit($id = 0){
        if(request()->isPost()){
            $Property = model('Property');
            $post_data=$this->request->post();

            $data = $Property->where('id',$id)->update($post_data);
            if($data){

                $this->success('更新成功', Cookie('__forward__'));
            } else {
                $this->error($Property->getError());
            }
        } else {

            /* 获取数据 */
            $info = \think\Db::name('Property')->field(true)->find($id);
            $this->assign('info', $info);

            return $this->fetch();
        }
    }


    public function del(){
        $id = array_unique((array)input('id/a',0));
//        return 123;

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(\think\Db::name('Property')->where($map)->delete()){

            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }


}