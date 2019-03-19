<?php
namespace app\home\controller;
use think\Db;
use app\home\model\Document;
use app\common\controller\UcApi;
use think\Cookie;
/**
 * 文档模型控制器
 * 文档模型列表和详情
 */
class Article extends Home {

    /* 文档模型频道页 */
	public function index(){
		/* 分类信息 */
		$category = $this->category();

		//频道页只显示模板，默认不读取任何内容
		//内容可以通过模板标签自行定制

		/* 模板赋值并渲染模板 */
		$this->assign('category', $category);
		return $this->fetch($category['template_index']);
	}

	/* 文档模型列表页 */
	public function lists($p = 1){

		/* 分类信息 */
		$category = $this->category();
		/* 获取当前分类列表 */
		$Document = new Document();
		$list = $Document->lists($category['id']);
		if(false === $list){
			$this->error('获取列表数据失败！');
		}

		/* 模板赋值并渲染模板 */
		$this->assign('category', $category);
		$this->assign('list', $list);
		// echo $category['template_lists'];
		return $this->fetch($category['template_lists']);
	}

	/* 文档模型详情页 */
	public function detail($id = 0, $p = 1){
		/* 标识正确性检测 */
		if(!($id && is_numeric($id))){
			$this->error('文档ID错误！');
		}

		/* 页码检测 */
		$p = intval($p);
		$p = empty($p) ? 1 : $p;

		/* 获取详细信息 */
		$Document = new Document();
		$info = $Document->detail($id);
		if(!$info){
			$this->error($Document->getError());
		}
		/* 分类信息 */
		$category = $this->category($info['category_id']);
		/* 获取模板 */
		if(!empty($info['template'])){//已定制模板
			$tmpl = $info['template'];
		} elseif (!empty($category['template_detail'])){ //分类已定制模板
			$tmpl = $category['template_detail'];
		} else { //使用默认模板
			$tmpl = 'article/'. get_document_model($info['model_id'],'name') .'/detail';
		}
		/* 更新浏览数 */
		$map = array('id' => $id);
		$Document->where($map)->setInc('view');
		/* 模板赋值并渲染模板 */
		$this->assign('category', $category);
		$this->assign('info', $info);
		$this->assign('page', $p); //页码
		return $this->fetch($tmpl);
	}

	/* 文档分类检测 */
	private function category($id = 0){
		/* 标识正确性检测 */
		$id = $id ? $id : input('param.category',0);
		if(empty($id)){
			$this->error('没有指定文档分类！');
		}

		/* 获取分类信息 */
		$category = model('Category')->info($id);
		if($category && 1 == $category['status']){
			switch ($category['display']) {
				case 0:
					$this->error('该分类禁止显示！');
					break;
				//TODO: 更多分类显示状态判断
				default:
					return $category;
			}
		} else {
			$this->error('分类不存在或被禁用！');
		}
	}

	/*
	 * 文章列表
	 */
	public function articleList(){
	    $start = 0;//起始查询位置
	    $count = 3;//查询多少条
	    $time = time();
	    //获取分类
        $class = \request()->get('class');
        $sql = 'select document.id,document.title,document.description,document.create_time,document.view from document 
join category on document.category_id = category.id 
WHERE category.title = '.$class.' and document.`status` = 1 
and '.$time.' between document.create_time and deadline limit '.$start.','.$count;
	    //获取通知列表
        $lists = Db::query($sql);
        foreach($lists as &$list){
            $list['create_time'] = date('Y-m-d H:i:s',$list['create_time']);
        }
        $this->assign('lists',$lists);
        $this->assign('class',$class);
	    return $this->fetch();
    }

    /*
     * 文章详情
     */
    public function articleShow(){
        $class = \request()->get()['class'];
        //接收文章ID
        $id = request()->get('id');
        if($class == '\'小区活动\''){
            //查询报名状态
            $sign = Db::table('sign')
                ->where('uid',session('user_auth.uid'))
                ->where('notice_id',$id)
                ->select();
            $signStatic = $sign ? '1' : '0';
            $this->assign('signStatic',$signStatic);
        }

        //查询此id的文章
        $notice = \model('document')
            ->field('id,title,description,create_time,view')
            ->find($id);

        //修改浏览次数
        $notice->view += 1;
        $notice->save();
        $this->assign('notice',$notice);
        $this->assign('class',$class);
        return $this->fetch();
    }

    //ajax分页接口
    public function articlePage(){
        $class = \request()->get()['class'] | '';
        $start = \request()->get()['start'] | '';
        $end = \request()->get()['end'] | '';
        $time = time();
        $sql = 'select document.id,document.title,document.description,document.create_time,document.view 
from document join category on document.category_id = category.id 
WHERE category.title = \''.$class.'\' and document.`status` = 1 
and '.$time.' between document.create_time and deadline limit '.$start.','.$end;
        //获取通知列表
        $lists = Db::query($sql);
        foreach($lists as &$list){
            $list['create_time'] = date('Y-m-d H:i:s',$list['create_time']);
        }

        return json_encode($lists);
    }

    //登录
    public function login($username = '', $password = '', $verify = '',$type = 1){
        if($this->request->isPost()){ //登录验证
            /* 检测验证码 */
            if(!captcha_check($verify)){
                $this->error('验证码输入错误！');
            }

            /* 调用UC登录接口登录 */
            $user = new UcApi;
            $uid = $user->login($username, $password, $type);

            if(0 < $uid){ //UC登录成功
                /* 登录用户 */
                $Member = model('Member');
                if($Member->login($uid)){ //登录用户
                    //TODO:跳转到登录前页面
                    if(!$cookie_url = Cookie::get('__forward__')){
                        $cookie_url = url('Home/Index/index');
                    }
                    $this->success('登录成功！',$cookie_url);
                } else {
                    $this->error($Member->getError());
                }

            } else { //登录失败
                switch($uid) {
                    case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
                    case -2: $error = '密码错误！'; break;
                    default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
                }
                $this->error($error);
            }

        } else { //显示登录表单
            return $this->fetch('login/index');
        }
    }

    /*
     * 报名：
     * static:0未登录，-1已报名，1报名成功
     */
    public function sign(){
        if(!is_login()){
            return ['static'=>'0','notice'=>'请登录'];
        }
        $noticeId = request()->post()['noticeId'];
        $uid = request()->post()['uid'];
        $createTime = time();

        //判断是否已报名
        $sel = Db::table('sign')
            ->where('uid',$uid)
            ->where('notice_id',$noticeId)
            ->select();
        if($sel){
            return ['static'=>'-1','notice'=>'不能再次报名'];
        }
        $res = Db::table('sign')->insert([
            'uid' => $uid,
            'notice_id' => $noticeId,
            'create_time' => $createTime,
        ]);
        return $res ? ['static'=>'1','notice'=>'报名成功'] : ['static'=>'0','notice'=>'报名失败'];
    }



}
