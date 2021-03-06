<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:96:"E:\phpStudy\PHPTutorial\WWW\tp5\public/../application/home/view/default/article\articleshow.html";i:1552959621;}*/ ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <title>Bootstrap 101 Template</title>

    <!-- Bootstrap -->
    <link href="__PUBLIC__/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="__PUBLIC__/css/style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style>
        .main{margin-bottom: 60px;}
        .indexLabel{padding: 10px 0; margin: 10px 0 0; color: #fff;}
    </style>
</head>
<body>
<div class="main">
    <!--导航部分-->
    <nav class="navbar navbar-default navbar-fixed-bottom">
        <div class="container-fluid text-center">
            <div class="col-xs-3">
                <p class="navbar-text"><a href="index.html" class="navbar-link">首页</a></p>
            </div>
            <div class="col-xs-3">
                <p class="navbar-text"><a href="#" class="navbar-link">服务</a></p>
            </div>
            <div class="col-xs-3">
                <p class="navbar-text"><a href="#" class="navbar-link">发现</a></p>
            </div>
            <div class="col-xs-3">
                <p class="navbar-text"><a href="#" class="navbar-link">我的</a></p>
            </div>
        </div>
    </nav>
    <!--导航结束-->

    <div class="container-fluid">
        <div class="blank"></div>
        <h3 class="noticeDetailTitle"><strong><?php echo $notice['title']; ?></strong></h3>
        <div class="noticeDetailInfo">发布者:小区物管</div>
        <div class="noticeDetailInfo">发布时间：<?php echo $notice['create_time']; ?></div>
        <div class="noticeDetailContent">
            <?php echo $notice['description']; ?>
        </div>
        <?php if($class == '\'小区活动\''): if($signStatic == 1): ?>
            <button class="btn btn-default sign">已报名</button>
            <?php endif; if($signStatic == 0): ?>
            <button class="btn btn-success sign" onclick=" sign(<?php echo $notice['id']; ?>)">我要报名</button>
            <?php endif; endif; ?>
    </div>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="__PUBLIC__/static/jquery-1.10.2.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="__PUBLIC__/bootstrap/js/bootstrap.min.js"></script>
<script>
    function sign(id){
        $.ajax({
            type:'post',
            url:"<?php echo url('Article/sign'); ?>",
            data:{
                noticeId:id,
                uid:"<?php echo session('user_auth.uid'); ?>",
            },
            dataType:'json',
            success:function(data){
                if(data.static == 0){
                    location.href="<?php echo url('Article/login'); ?>";
                }
                if(data.static == 1){
                    location.reload();
                }
                alert(data.notice);
            }
        });
    }
</script>
</body>
</html>