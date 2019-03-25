<?php
/**
 * Created by PhpStorm.
 * User: zhaozhiyun.
 * Date: 2019/3/15 0015
 * Time: 10:13
 */

namespace app\admin\controller;


use app\admin\model\Rent;
use think\Db;
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
                $this->success('新增成功','index');
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

                $this->success('更新成功', 'index');
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

    //关于我们
    public function aboutIndex(){
        if(request()->isPost()){
           $res = Db::table('about_our')
               ->where('id',1)
               ->update([
                   'content' => request()->post()['content'],
                   'updated_time' => time()
                ]);

           return $res ? $this->success('更新成功','aboutIndex') : $this->error('更新失败','aboutIndex');
        }else{
            $res = Db::table('about_our')->select();
            $this->assign('content',$res[0]['content']);
            return $this->fetch();
        }
    }

    //小区租售列表
    public function rentIndex(){
        $lists = Db::table('rent')->select();
        foreach($lists as &$list){
            $list['create_time'] = date('Y-m-d',$list['create_time']);
        }
        $this->assign('lists',$lists);
        return $this->fetch();
    }

    //添加rent
    public function rentAdd(){
        if(request()->isPost()){
            $post_data = request()->post();
            $validate = new Validate([
                'title' => 'require',
                'static' => 'require',
                'price' => 'require|number',
                'phone' => 'require|number',
                'introduce' => 'require',
            ],[
                'title.require' => '标题不能为空',
                'static.require' => '状态不能为空',
                'price.require' => '价格不能为空',
                'price.number' => '价格只能为数字',
                'phone.require' => '联系电话不能为空',
                'phone.number' => '联系电话不能只能为数字',
                'introduce.require' => '简介不能为空',
            ]);
            $post_data['create_time'] = time();
            if(!$validate->check($post_data)){
                $this->error($validate->getError());
            }

            // 获取表单上传文件 例如上传了001.jpg
            $file = request()->file('img');

            // 移动到框架应用根目录/public/uploads/ 目录下
            if($file){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    // 成功上传后 获取上传信息
                    $post_data['img'] = $info->getSaveName();

                }else{
                    // 上传失败获取错误信息
                    echo $file->getError();
                }
            }

            $res = Db::table('rent')->insert($post_data);

            return $res ? $this->success('添加成功','rentIndex') : $this->error('添加失败','rentIndex');
        }else{
            return $this->fetch();
        }
    }

    public function rentEdit($id){
        if(!request()->isPost()){
            $list = Rent::get($id);
            $this->assign('list',$list);
            return $this->fetch();
        }else{
            $data = request()->post();

            $validate = new Validate([
                'title' => 'require',
                'static' => 'require',
                'price' => 'require|number',
                'phone' => 'require|number',
                'introduce' => 'require',
            ],[
                'title.require' => '标题不能为空',
                'static.require' => '状态不能为空',
                'price.require' => '价格不能为空',
                'price.number' => '价格只能为数字',
                'phone.require' => '联系电话不能为空',
                'phone.number' => '联系电话不能只能为数字',
                'introduce.require' => '简介不能为空',
            ]);

            if(!$validate->check($data)){
                $this->error($validate->getError());
            }

            if($file = request()->file('img')){
                $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info){
                    // 成功上传后 获取上传信息
                    $data['img'] = $info->getSaveName();

                }else{
                    // 上传失败获取错误信息
                    echo $file->getError();
                }
            }

            $id = $data['id'];
            unset($data['id']);
            $rent = new Rent;
            $res = $rent->save($data,['id'=>$id]);

            return $res ? $this->success('更新成功','rentIndex') : $this->error('更新失败','rentIndex');
        }

    }

    public function rentDel(){
        $id = array_unique((array)input('id/a',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(\think\Db::name('Rent')->where($map)->delete()){

            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    public function state($id){
        $list = Rent::get($id);
        if($list->state){
            $list->state = 0;
        }else{
            $list->state = 1;
        }

        $list->save();

        return $this->success('更新状态成功','rentIndex');
    }


}