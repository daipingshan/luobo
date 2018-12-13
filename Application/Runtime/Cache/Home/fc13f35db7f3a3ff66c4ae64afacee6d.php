<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,
                                     initial-scale=1.0,
                                     maximum-scale=1.0,
                                     user-scalable=no">
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" href="../../../../favicon.ico">

<title><?php echo ($title); ?> <?php echo C('base')['title'];?></title>

<!-- Bootstrap core CSS -->
<link rel="stylesheet" href="/Public/Home/css/bootstrap.min.css">
<link rel="stylesheet" href="/Public/Home/css/base.css">
<link rel="stylesheet" href="/Public/Home/css/mobile.css">
<meta name="keywords" content="<?php echo C('base')['keyword'];?>">
<meta name="description" content="<?php echo C('base')['desc'];?>">

<link rel="stylesheet" href="/Public/Home/css/animate.min.css">
<link rel="stylesheet" type="text/css" href="/Public/Home/fonts/iconfont.css">
<script src="/Public/Home/js/jquery-3.3.1.min.js"></script>


<body>
<!-- 顶部导航-->

    <div id="topbar" class="hidden-xs">
        <div class="container">

            <div class="col float-left right-bar">
							<?php if(empty($_SESSION['user']['id'])): ?><span>全国站</span>
								<?php else: ?>

                <?php if($_SESSION['identity']== 2): ?><a href="<?php echo U('Company/index');?>">公司主页</a>

                    <?php if($info['company_meal'] != 0 && $count <= 30): ?><a href="<?php echo U('Recruit/sendRecruitmentInfo');?>">发布岗位 </a><?php endif; ?>
												<a href="<?php echo U('Company/luobovip');?>">萝卜会员 </a>
                        <a href="<?php echo U('Company/manage');?>">岗位管理 </a>
                        <a href="<?php echo U('Recruit/workLifePaper', array('status' => '1'));?>">简历库</a>
                <?php else: ?>

                            <a href="<?php echo U('user/info_display');?>">个人资料</a>
                        <a href="<?php echo U('User/index');?>">投递记录
                          <?php if(($invitations != 0)): ?><span class="badge visible-lg-*"><?php echo ($invitations); ?></span><?php endif; ?>
                        </a>
                          <a href="<?php echo U('user/vitae');?>">我的简历</a><?php endif; endif; ?>
              </div>
            <div class="col-md-6 float-right text-right navbar-right">
                <?php if(empty($_SESSION['user']['id'])): ?><a href="<?php echo U('Auth/login');?>">登录</a> |
                    <a href="<?php echo U('Auth/register');?>">注册</a>

                    <?php else: ?>


                        <div class="btn-group">
                          <a id="dLabel"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php if($_SESSION['identity']== 2): ?>企业<?php else: ?>求职者<?php endif; ?>
                            <!--<?php echo ($_SESSION['user']['mobile']); ?>-->
                            <span class="caret"></span>
                          </a>

                        </a>
                          <ul class="dropdown-menu" aria-labelledby="dLabel">
                               <li><a href="<?php echo U('Auth/account');?>">账户信息</a></li>
                               <li><a href="<?php echo U('Auth/switch_identity');?>">切换身份</a></li>
                               <li><a href="<?php echo U('Auth/password');?>">修改密码</a></li>
                               <li role="separator" class="divider"></li>
                               <li><a href="<?php echo U('Auth/logout');?>">退出</a></li>
                          </ul>

                        </div><?php endif; ?>
            </div>
        </div>
    </div>


<div id="wrapper">
    <div class="overlay"></div>
<!-- LOGO 与导航 -->
<div id="header">
    <div class="container">
        <div class="row">
            <div id="logo" class="col-md-4 col-xs-12">
                <a href="/"><img src="/Public/Home/images/logo.png" class="logo"></a>
            </div>
            <div id="nav" class="col-md-8 hidden-xs">

                <ul class="nav navbar-nav row">
                     <li class="nav-item ">
                         <a <?php if(($menu) == "index"): ?>class="nav-link active"
                             <?php else: ?>
                                  class="nav-link"<?php endif; ?>
                                 href="<?php echo U('Index/index');?>">主页</a>
                     </li>
                     <li class="nav-item  ">
                        <a
                        <?php if(($menu) == "company"): ?>class="nav-link active"
                                                                        <?php else: ?>
                                                                        class="nav-link"<?php endif; ?>
                        href="<?php echo U('Index/company');?>">企业商铺</a>
                     </li>
                    <li class="nav-item ">
                        <a
                        <?php if(($menu) == "project"): ?>class="nav-link active"
                            <?php else: ?>
                            class="nav-link"<?php endif; ?>
                        href="<?php echo U('Index/project');?>">萝卜项目
                        </a>
                    </li>
                     <li class="nav-item  ">
                        <a
                        <?php if(($menu) == "school"): ?>class="nav-link active"
                            <?php else: ?>
                            class="nav-link"<?php endif; ?>
                        href="<?php echo U('Index/school');?>">萝卜校招</a>
                     </li>

                     <li class="nav-item ">
                        <a
                          <?php if(($menu) == "Shop"): ?>class="nav-link active"
                              <?php else: ?>
                              class="nav-link"<?php endif; ?>
                          href="<?php echo U('Shop/index');?>">萝卜门店
                        </a>

                      </li>
                </ul>

            </div>

            <div class="btn-group animated fadeInLeft right_list navbar-right visible-xs">
                <a id="dLabel"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="iconfont icon-qita "></span>
                </a>

                <ul class="dropdown-menu" aria-labelledby="dLabel">
                    <li><a href="<?php echo U('User/vitae');?>">我的简历</a></li>
                    <li><a href="<?php echo U('Auth/account');?>">账户信息</a></li>
                    <li><a href="<?php echo U('Auth/password');?>">修改密码</a></li>
                    <li><a href="<?php echo U('Auth/logout');?>">退出登录</a></li>
                </ul>


            </div>
        </div>
    </div>
</div>

<div id="search"
	<?php if(isset($search_hide)): ?>class="hide"<?php endif; ?>
>
    <div class="container">
        <div class="row">
						<?php if($search_mode == 'company'): ?><form action="<?php echo U('search/company');?>" method="get" >
                <?php $placeholder = "输入企业名称"; ?>
            <?php elseif($search_mode == 'vitae'): ?>
								<form action="<?php echo U('search/vitae');?>" method="get">
								 <?php $placeholder = "搜索人才"; ?>
            <?php else: ?>
                <form action="<?php echo U('search/index');?>" method="get">
                <?php $placeholder = "搜索岗位"; endif; ?>

                <div class="col-md-2 col-xs-1"></div>
                <div class="col-md-5 col-xs-7">
                    <input class="form-control search-input" name="keyword" type="text" placeholder="<?php echo ($placeholder); ?>"
                    <?php if(isset($inventory['keyword'])): ?>value="<?php echo ($inventory['keyword']); ?>"<?php endif; ?>
                    >

                    <?php $hot_jobs = C('BASE')[search]; $jobs_items = explode(",", $hot_jobs); ?>
                    <p class="search-keyword hidden-xs">
                        热门搜索：
                        <?php if(is_array($jobs_items)): foreach($jobs_items as $key=>$vo): ?><a href="/search/index/keyword/<?php echo ($vo); ?>"><?php echo ($vo); ?></a><?php endforeach; endif; ?>

                    </p>
                </div>
                <div class="col-md-3 col-xs-4">
                    <button type="submit" class="btn btn-primary  luobo_btn search-btn">搜索</button>
                </div>
                <div class="col-md-2 col-xs-0"></div>
            </form>
        </div>
    </div>
</div>



<div id="topmod" class="container">
    <div class="">
        <div class="col-md-3 hidden-xs">
            <link rel="stylesheet" href="/Public/Home/css/home_nav.css">
            

    <div class="index_nav ">
        <div class="nav_left  col-md-11 ">
            <ul id="jobs_list" class="list-group">
                <?php if(is_array($type)): $i = 0; $__LIST__ = $type;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><li data-id="<?php echo ($key); ?>" class="list-group-item">
                        <img src="/Public/Home/images/icon/home/<?php echo ($row["icon"]); ?>.png" class="icon_size">
                        <span><?php echo ($row["name"]); ?></span>
                        <i class="iconfont icon-xxx icon-gengduo icon-more"></i>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
            <div id="alljobs">
                <img src="/Public/Home/images/icon/home/icon-qita1.png" class="icon_size">
                查看全部
            </div>
        </div>
        <div class="nav_right ">
            <?php if(is_array($type)): $i = 0; $__LIST__ = $type;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><div class="sub hide " data-id="<?php echo ($key); ?>">
                    <?php if(is_array($row["son_data"])): $i = 0; $__LIST__ = $row["son_data"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><dl class="dl-horizontal">
                            <dt><a><?php echo ($val["name"]); ?> </a></dt>
                            <dd>
                                <?php if(is_array($val["son_data"])): $i = 0; $__LIST__ = $val["son_data"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><a href="<?php echo U('Search/index',array('category_id'=>$v['id']));?>"><?php echo ($v["name"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
                            </dd>
                        </dl><?php endforeach; endif; else: echo "" ;endif; ?>
                </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>

        </div>
        <div class="col-md-9 col-xs-12">
            
    <div id="myCarousel" class="carousel slide">
        <!-- 轮播（Carousel）指标 -->
        <ol class="carousel-indicators">
            <?php if(is_array($advert)): $i = 0; $__LIST__ = $advert;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><li data-target="#myCarousel" data-slide-to="<?php echo ($key); ?>"
                <?php if(($key) == "0"): ?>class="active"<?php endif; ?>
                ></li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ol>
        <!-- 轮播（Carousel）项目 -->
        <div class="carousel-inner">
            <?php if(is_array($advert)): $i = 0; $__LIST__ = $advert;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><div
                <?php if(($key) == "0"): ?>class="item active"
                    <?php else: ?>
                    class="item"<?php endif; ?>
                >
                <img src="<?php echo ($row["img_url"]); ?>" alt="<?php echo ($row["title"]); ?>">
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    <!-- 轮播（Carousel）导航 -->
    <!-- Controls -->
    <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
    </div>

        </div>
    </div>

</div>
<main class="container">
    
    <div class="jobs ">
        <div class="jobs-head  ">
            <div class="jobs-tab col-md-8 col-xs-8">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation"  class="active">
                        <a href="#self" data-id="self" id="self-tab" aria-controls="self" role="tab" data-toggle="tab"
                           class="position-type">萝卜项目</a>
                    </li>
                    <li role="presentation">
                        <a href="#new" data-id="new" id="new-tab" aria-controls="new" role="tab" data-toggle="tab"
                           class="position-type">最新职位</a>
                    </li>

                    <li role="presentation">
                        <a href="#hot" data-id="hot" id="hot-tab" aria-controls="hot" role="tab" data-toggle="tab"
                           class="position-type active">推荐职位</a>
                    </li>
                </ul>
            </div>
            <span class="jobsChange col-md-4 col-xs-4  position-change">
                <img class="change_img" src="/Public/Home/images/icon/home/change.png">换一批
            </span>
        </div>
        <div class="tab-content ">
            <div id="self" role="tabpanel" class="tab-pane jobs-con row active">
            </div>
            <div id="new" role="tabpanel" class="tab-pane jobs-con row">
            </div>
            <div id="hot" role="tabpanel" class="tab-pane jobs-con row ">
            </div>
            <div class="clear"></div>
        </div>
        <div class="text-center">
            <div class="jobs-more col-md-2 col-md-push-5 col-xs-12 col-xs-push-0">
                <a href="<?php echo U('Search/index',array('type'=>'hot'));?>" target="_blank" class="" id="location_href">查看更多</a>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="recommended_enterprise">
        <div class="jobs-head ">
            <span class="jobs-title col-md-10 col-xs-8">热门公司</span>
            <span class="jobsChange col-md-2  col-xs-4 company-change">
                <img class="change_img" src="/Public/Home/images/icon/home/change.png">换一批
            </span>
            <div class="clear"></div>
        </div>
        <div class="jobs-con " id="company">
        </div>
        <div class="text-center">
            <div class="jobs-more col-md-2 col-md-push-5 col-xs-12 col-xs-push-0">
                <a href="<?php echo U('Search/company',array('type'=>'hot'));?>" target="_blank" class="">查看更多</a>
            </div>
        </div>

    </div>

    <div id="links" class="row hidden-xs">
        <div class="jobs-head col-md-12 col-xs-12">
            <span class="jobs-title">友情链接</span>
            <div class="clear"></div>
        </div>
        <div class="links-con col-md-12">

            <ul class="links">
                <?php if(is_array($link)): $i = 0; $__LIST__ = $link;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$link_row): $mod = ($i % 2 );++$i;?><li><a href="<?php echo ($link_row["url"]); ?>" target="_blank"><?php echo ($link_row["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>

        </div>
    </div>
</main>

<footer id="footer-con">
    <div class="container">
        <div class="foorer_con row">
            <div class="col-md-3 hidden-xs">
                <h4>公司问答</h4>
                <ul class="footermenu">
                    <li><a href="/article/detail/id/4">关于我们</a></li>
                    <li><a href="/article/detail/id/3">用户注册协议</a></li>
                </ul>
            </div>
            <div class="col-md-3 hidden-xs">
                <h4>企业服务</h4>
                <ul class="footermenu">
                    <li><a href="">萝卜问答</a></li>
                    <li><a href="">会员中心</a></li>
                </ul>
            </div>
            <div class="col-md-4  hidden-xs">
                <h4>联系方式</h4>
                <ul class="footermenu">
                    <li>服务热线：029—81142480</li>

                    <li>受理电话：9：00 - 20：00</li>

                    <li>Email：luobo@luobowang.com</li>
                    <li>地址：西安市高新区锦业一路研祥城市广场B座309</li>
                </ul>
            </div>
            <div class="col-md-2 text-center  hidden-xs">
                <img src="/Public/Home/images/qrcode.jpg" class="qrcode ">
                <p >微信公众号</p>

            </div>
        </div>
    </div>
    <div id="footerbar" class="hidden-xs">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>©2018 萝卜网 陕ICP备18011042号
                    </p>
                </div>
                <div class="col-md-6 ">
                    <ul class="footerbar-ul pull-right">

                        <li class="nav-item  ">
                            <a
                            <?php if(($menu) == "company"): ?>class="nav-link active"
                                <?php else: ?>
                                class="nav-link"<?php endif; ?>
                            href="<?php echo U('Index/company');?>">企业商铺</a>
                        </li>
                        <li class="nav-item  ">
                            <a
                            <?php if(($menu) == "school"): ?>class="nav-link active"
                                <?php else: ?>
                                class="nav-link"<?php endif; ?>
                            href="<?php echo U('Index/school');?>">萝卜校招</a>
                        </li>
                        <li class="nav-item ">
                            <a
                            <?php if(($menu) == "project"): ?>class="nav-link active"
                                <?php else: ?>
                                class="nav-link"<?php endif; ?>
                            href="<?php echo U('Index/project');?>">萝卜项目
                            </a>
                        </li>
                        <li class="nav-item ">
                            <a
                            <?php if(($menu) == "Shop"): ?>class="nav-link active"
                                <?php else: ?>
                                class="nav-link"<?php endif; ?>
                            href="<?php echo U('Shop/index');?>">萝卜门店
                            </a>

                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="main_nav_bottom visible-xs-* hidden-md hidden-lg">
        <nav class="navbar navbar-default navbar-fixed-bottom">
            <div class="container" align="center">
                <ul class="nav nav-tabs nav-tabs-justified">
                    <div class="row" align="center">
                        <div class="col-xs-3" align="center">
                            <li
                            <?php if((CONTROLLER_NAME == 'Index') AND (ACTION_NAME == 'index')): ?>class="active"<?php endif; ?>>
                                <a href="/">
                                    <span class="glyphicon glyphicon-home col-xs-12" aria-hidden="true"></span>
                                    主页
                                </a>
                            </li>
                        </div>
                        <div class="col-xs-3" align="center">
                            <li
                            <?php if((CONTROLLER_NAME == 'Search') or (CONTROLLER_NAME == 'Jobs') ): ?>class="active"<?php endif; ?>>
                                <a href="/search/index">
                                    <span class="glyphicon glyphicon-lock col-xs-12" aria-hidden="true"></span>
                                    好工作
                                </a>
                            </li>
                        </div>
                        <div class="col-xs-3" align="center">
                            <li
                            <?php if(((CONTROLLER_NAME == 'Index') AND (ACTION_NAME == 'project')) or (CONTROLLER_NAME == 'project')): ?>class="active"<?php endif; ?>>
                                <a href="/Index/project">
                                    <span class="glyphicon glyphicon-fire col-xs-12" aria-hidden="true"></span>
                                    好项目
                                </a>
                            </li>
                        </div>
                        <div class="col-xs-3" align="center">
                            <li <?php if((CONTROLLER_NAME) == "User"): ?>class="active"<?php endif; ?>>
                                <a href="/User/mobile">
                                    <span class="glyphicon glyphicon-user col-xs-12" aria-hidden="true"></span>
                                    我的
                                </a>
                            </li>
                        </div>
                    </div>
                </ul>
            </div>
        </nav>
    </div>
</footer>
<div class="fixed_bar hidden-xs"  ><img id="toTop" src="/Public/Home/images/top.png"> </div>
<script src="/Public/Home/js/toastr.min.js"></script>
<script src="/Public/Home/js/lib.js"></script>
<link rel="stylesheet" href="/Public/Home/css/toastr.min.css">

<script src="/Public/Home/js/bootstrap.min.js"></script>
<script src="/Public/Home/js/popper.min.js"></script>
<?php if(isset($alert)): ?><script>
		$(function(){
				toastr.info('<?php echo ($alert); ?>');
		});
	</script><?php endif; ?>

<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?4db8ac3254b61b48a23ca5fab464355c";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
</div>
<script>
        $(document).ready(function() {

            $('.index_nav').on('mouseenter', function() {

                $(".nav_right").removeClass('hide');
            }).on('mouseleave', function() {

                $(".nav_right").addClass('hide');
                $(".sub").addClass('hide');
            }).on('mouseenter', 'li', function(e) {

                var li_data = $(this).attr('data-id');
                var x = $(this).position().top;
								if(li_data >= 4 && li_data < 8){
								    var range = x - 170;
								}else if(li_data >= 8){
										range = x - 290;
								}else{
								    range = 0;
								}


                $(".nav_right").css('top',range);
                $(".sub").addClass('hide');
                $('.sub[data-id="' + li_data + '"]').removeClass('hide')
                //console.log(li_data);

            })
            var alljobs  = $('#alljobs');
            var jobs_list = $('#jobs_list');

            var html1 = '<img src="/Public/Home/images/icon/home/icon-qita1.png" class="icon_size">&nbsp;&nbsp;查看全部';
            var html2 = '<img src="/Public/Home/images/icon/home/icon-qita1.png" class="icon_size" >&nbsp;&nbsp;收起';
            alljobs.on('click',function(){
                 if($(this).hasClass('menu-open')){
                      jobs_list.animate({height:'295px'})
                      alljobs.html(html1).removeClass('menu-open');
                      return;
                 }
                 jobs_list.animate({height:'510px'})
                 $(this).html(html2).addClass('menu-open');

            });



        });

    </script>

    <script>
        $(document).ready(function () {
            var type = 'self';
            $('.position-type').click(function () {
                type = $(this).data('id');
            })
            $('.position-change').click(function () {
                getData(type);
            })
            $('.company-change').click(function () {
                getCompany();
            })
            getData('self');
            getData('hot');
            getData('new');
            getCompany();

            $('#hot-tab').click(function () {
                $('#location_href').attr('href', "<?php echo U('Search/index',array('type'=>'hot'));?>");
            });
            $('#new-tab').click(function () {
                $('#location_href').attr('href', "<?php echo U('Search/index',array('type'=>'new'));?>");
            });
            $('#self-tab').click(function () {
                $('#location_href').attr('href', "<?php echo U('Index/project');?>");
            });

            /**
             * 获取招聘信息
             */
            function getData(type) {
                $.get("<?php echo U('getRecruit');?>", {type: type}, function (res) {
                    var data = res.data;
                    var html = '';
                    $.each(data, function (i, item) {
                            html += '<div class="jobs-box col-md-4 col-xs-12">';
                            html += '<div class="job-detail">';
                            html += '<div class="row">';
                            if (type == 'self') {
                                html += '<div class="col-md-4 col-xs-4">';
                                html += ' <a href="<?php echo U('project/detail');?>?id=' + item.id + '" ><img src="' + item.image + '" alt="" class="project-logo img-rounded"></a>';
                                html += '</div>';
                                html += '<div class="col-md-8 col-xs-8">';
                                html += '<p><a href="<?php echo U('project/detail');?>?id=' + item.id + '" class="job-title">' + item.name + '</a></p>';
                                html += '<p class="job-meta"><a href="<?php echo U('project/detail');?>?id=' + item.id + '"><span class="url pull-right">平台补助￥' + item.rebate + '元</span></a></p>';
                                html += '<p class="job-push-time">[' + item.create_time + ' 发布]</p>';
                                html += '</div>';
                            } else {
                                html += '<div class="col-md-3 col-xs-4"><a href="<?php echo U('Jobs/detail');?>?id=' + item.id + '" class="job-title">';
                                if (item.avatar) {
                                    html += ' <img src="' + item.avatar + '" alt="' + item.company_name + '" class="job-logo img-rounded">';
                                } else {
                                    html += ' <img src="/Public/Home/images/cp_def.png" alt="' + item.company_name + '" class="job-logo img-rounded">';
                                }
                                html += '</a></div>';
                                html += '<div class="col-md-6 col-xs-8">';
                                html += '<p><a href="<?php echo U('Jobs/detail');?>?id=' + item.id + '" class="job-title">' + item.name + '</a></p>';
                                html += '<p class="job-meta">' + item.work_name + ' / ' + item.educate_name + '</p>';
                                html += '<p class="job-push-time">[' + item.create_time + ' 发布]</p>';
                                html += '</div>';
                                html += '<div class="col-md-3 col-xs-12">';
                                if (item.salary_min == 0 && item.salary_max == 0) {
                                    html += '<p class="job-pay pull-right">面议</p>';
                                } else {
                                    html += '<p class="job-pay pull-right">' + item.salary_min + 'K~' + item.salary_max + 'K</p>';
                                }
                                html += '</div>';
                            }

                            html += '</div>';
                            html += '<div class="job-be row cp_foote">';
                            if (type == 'self') {
                                html += '<div class="col-md-12 col-xs-12">';
                                html += '<span class="job-cp-name"><strong>' + item.position_name + '</strong></span>';
                                if (item.salary_min == 0 && item.salary_max == 0) {
                                    html += '<p class="job-pay pull-right">薪资：面议</p>';
                                } else {
                                    html += '<p class="job-pay pull-right">薪资：' + item.salary_min + 'K~' + item.salary_max + 'K</p>';
                                }
                                html += '</div>';
                            } else {
                                html += '<div class="col-md-8 col-xs-8">';
                                html += '<p><a href="<?php echo U('Jobs/company');?>?id=' + item.company_id + '" class="job-cp-name">' + item.company_name + '</a></p>';
                                html += '<p class="job-cp-tag">' + item.job_excellence + ' </p>';
                                html += '</div>';
                                html += '<div class="col-md-4 col-xs-4">';
                                html += '<a href="<?php echo U('Jobs/detail');?>?id=' + item.id + '" class="pull-right job-more">了解详情</a>';
                                html += '</div>';
                            }
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                        }
                    );
                    $('#' + type).html(html);
                }, 'json');
            }

            /**
             * 获取公司信息
             */
            function getCompany() {
                $.get("<?php echo U('getCompany');?>", {}, function (res) {
                    var data = res.data;
                    var html = '';
                    $.each(data, function (i, item) {
                        html += '<div class="jobs-box col-md-4 col-xs-12">';
                        html += '<div class="job-detail">';
                        html += '<div class="row">';
                        html += '<div class="col-md-4 text-center col-xs-4">';
                        html += '<a href="<?php echo U('Jobs/company');?>?id=' + item.id + '">';
                        if (item.avatar) {
                            html += ' <img src="' + item.avatar + '" alt="' + item.company_name + '" class="img-rounded">';
                        } else {
                            html += ' <img src="/Public/Home/images/cp_def.png" alt="' + item.company_name + '" class="img-rounded">';
                        }
                        html += '</a></div>';
                        html += '<div class="col-md-8 col-xs-8">';
                        if (item.short_name != '') {
                            html += '<p><a href="<?php echo U('Jobs/company');?>?id=' + item.id + '" class="job-title">' + item.short_name + '</a></p>';
                        } else {
                            html += '<p><a href="<?php echo U('Jobs/company');?>?id=' + item.id + '" class="job-title">' + item.company_name + '</a></p>';
                        }
                        html += '<p class="job-meta">' + item.industry_field + '/' + item.scale_name + '/' + item.company_size + '人</p>';
                        html += '</div>';
                        html += '</div>';
                        html += '<div class="row cp_mark_box">';
                        html += item.label_html;
                        html += '</div>';
                        html += '<div class="row cp_mark_foot cp_foote">';
                        html += '<div class="col-md-6 col-xs-6">';
                        html += ' <span>' + item.num + '</span>热招职位';
                        html += '</div>';
                        html += '<div class="col-md-6 col-xs-6">';
                        html += ' <span>资格</span>' + item.meal_name;
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                    });
                    $('#company').html(html);
                }, 'json');
            }
        });

    </script>