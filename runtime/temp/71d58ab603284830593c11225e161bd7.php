<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:96:"E:\phpStudy\PHPTutorial\WWW\tp5\public/../application/home/view/default/article\articlelist.html";i:1552958494;}*/ ?>
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

    <div class="container-fluid article-list">

        <?php if(is_array($lists) || $lists instanceof \think\Collection || $lists instanceof \think\Paginator): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?>
            <div class="row noticeList">
                <a href="<?php echo url('Article/articleShow'); ?>?id=<?php echo $list['id']; ?>&class=<?php echo $class; ?>">
                <div class="col-xs-2">
                    <img class="noticeImg" src="__PUBLIC__/image/1.png" />
                </div>
                <div class="col-xs-10">
                    <p class="title"><?php echo $list['title']; ?></p>
                    <p class="intro"><?php echo $list['description']; ?></p>
                    <p class="info">浏览: <?php echo $list['view']; ?> <span class="pull-right"><?php echo $list['create_time']; ?></span> </p>

                </div>
                </a>
            </div>
        <?php endforeach; endif; else: echo "" ;endif; ?>

    </div>
    <br/>
    <button type="button" class="btn btn-primary onlineBtn" id="moreArticle">加载更多</button>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="__PUBLIC__/static/jquery-1.10.2.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="__PUBLIC__/bootstrap/js/bootstrap.min.js"></script>
<script>
    var start = $('.noticeList').length;
    var end = 3;
    var className = <?php echo $class; ?>;

    $('#moreArticle').on('click',function(){
        $.ajax({
            type:'get',
            url:"<?php echo url('Article/articlePage'); ?>?",
            data:{
                'start':start,
                'end':end,
                'class':className
            },
            dataType:'json',
            success:function(data){
                if(data == '[]'){
                    $('#moreArticle').hide();
                    $('.article-list').append('加载完成');
                }
                data = JSON.parse(data);
                str = '';
                for(i=0;i<data.length;i++){
                    str +='<div class="row noticeList">\n' +
                        '                <a href="<?php echo url('Article/articleShow'); ?>?id='+data[i]['id']+'&class=\''+<?php echo $class; ?>+'\'">\n' +
                        '                <div class="col-xs-2">\n' +
                        '                    <img class="noticeImg" src="__PUBLIC__/image/1.png" />\n' +
                        '                </div>\n' +
                        '                <div class="col-xs-10">\n' +
                        '                    <p class="title">'+data[i]['title']+'</p>\n' +
                        '                    <p class="intro">'+data[i]['description']+'</p>\n' +
                        '                    <p class="info">浏览: '+data[i]['view']+' <span class="pull-right">'+data[i]['create_time']+'</span> </p>\n' +
                        '                </div>\n' +
                        '                </a>\n' +
                        '            </div>'
                }
                $('.article-list').append(str);
            }
        })
        start += end;
    })


</script>
</body>
</html>