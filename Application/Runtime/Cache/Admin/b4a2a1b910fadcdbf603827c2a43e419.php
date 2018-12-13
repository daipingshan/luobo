<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>萝卜网-管理后台</title>
    <link rel="stylesheet" href="/Public/Admin/lib/layui/css/layui.css">
    <link rel="stylesheet" href="/Public/Admin/css/common.css">
    
    <style type="text/css">
        .search .layui-form-select .layui-edge {
            right: 25%;
        }

        .search .layui-form-select dl {
            min-width: 80%;
        }
    </style>

</head>
<body class="layui-layout-body">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="layui-logo">萝卜网-管理后台</div>
        <!-- 头部区域（可配合layui已有的水平导航） -->
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item">
                <a href="javascript:;">
                    <img src="http://t.cn/RCzsdCq" class="layui-nav-img">
                    <?php echo session('admin_user_info')['username'];?>
                </a>
                <dl class="layui-nav-child">
                    <dd><a href="javascript:;" onclick="updatePass()">修改密码</a></dd>
                </dl>
            </li>
            <li class="layui-nav-item"><a href="<?php echo U('Login/logout');?>">退出</a></li>
        </ul>
    </div>

    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree" id="nav">
                <?php if(is_array($menu_data)): $i = 0; $__LIST__ = $menu_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i; if(empty($row["href"])): ?><li class="layui-nav-item">
                            <a href="javascript:;"><i class="layui-icon <?php echo ($row["icon"]); ?>" style="font-size: 20px"></i>
                                <?php echo ($row["title"]); ?></a>
                            <dl class="layui-nav-child">
                                <?php if(is_array($row["children"])): $i = 0; $__LIST__ = $row["children"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><dd style="padding-left: 30px"><a href="<?php echo ($val["href"]); ?>" data-type="2"><?php echo ($val["title"]); ?></a>
                                    </dd><?php endforeach; endif; else: echo "" ;endif; ?>
                            </dl>
                        </li>
                        <?php else: ?>
                        <li class="layui-nav-item"><a href="<?php echo ($row["href"]); ?>" data-type="1"><i
                                class="layui-icon <?php echo ($row["icon"]); ?>" style="font-size: 20px"></i> <?php echo ($row["title"]); ?></a></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>

    <div class="layui-body" style="padding: 20px">
        <!-- 内容主体区域 -->
        
    <form class="layui-form search" action="" name="search">
        <div class="layui-row">
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md2">
                <input type="text" name="mobile" value="<?php echo I('get.mobile');?>" placeholder="请输入手机号码" class="layui-input">
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md2">
                <input type="text" name="email" value="<?php echo I('get.email');?>" placeholder="请输入邮箱" class="layui-input">
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md2">
                <input type="text" name="name" value="<?php echo I('get.name');?>" placeholder="请输入公司名称" class="layui-input">
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md3">
                <select name="is_check" class="layui-select">
                    <option value="">请选择企业状态</option>
                    <option value="0">未认证</option>
                    <option value="1">已认证通过</option>
                    <option value="2">认证失败</option>
                    <option value="4">二次未认证</option>
                </select>
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md3">
                <input type="checkbox" name="status" value="1" title="禁用">
                <button class="layui-btn search" type="button"><i class="layui-icon">
                    &#xe615;</i>查询
                </button>
            </div>
        </div>
    </form>
    <table class="layui-hide" id="table" lay-filter="table"></table>

    </div>

    <div class="layui-footer">
        <!-- 底部固定区域 -->
        © Copyright ©2017-2018 v1.0 All Rights Reserved.系统由<a href="www.10isp.com" target="_blank"> 中光电信</a> 提供技术支持
    </div>
</div>
<div class="hide" style="padding: 20px" id="update-pass">
    <form class="layui-form" action="" name="update-pass">
        <div class="layui-form-item">
            <label class="layui-form-label">登录密码</label>
            <div class="layui-input-block">
                <input type="text" placeholder="请输入登录密码" name="password" class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">确认密码</label>
            <div class="layui-input-block">
                <input type="text" placeholder="请输入确认密码" name="pass" class="layui-input"/>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label"></label>
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="update-pass">修改</button>
            </div>
        </div>
    </form>
</div>

<script src="/Public/Admin/lib/layui/layui.all.js"></script>
<script>
    var BASE_BAR = [['FullScreen', 'simpleupload', 'bold', 'underline', 'fontborder', 'fontfamily', 'fontsize', 'strikethrough', '|', 'selectall', 'cleardoc', '|',
        'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
        'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify']];
    var layer = layui.layer,
        element = layui.element,
        form = layui.form,
        laydate = layui.laydate,
        upload = layui.upload,
        carousel = layui.carousel,
        flow = layui.flow,
        util = layui.util,
        table = layui.table,
        laypage = layui.laypage,
        laytpl = layui.laytpl,
        layedit = layui.layedit,
        $ = layui.jquery;
    table.limit = 30;
    layedit.set({
        uploadImage: {
            url: "<?php echo U('Common/uploadEditImg');?>" //接口url
            , type: 'post' //默认post
        }
    });

    function updatePass() {
        $('#update-pass input').val('');
        layer.open({
            type: 1,
            title: '修改密码',
            area: ['500px', '260px'],
            content: $('#update-pass')
        })
    }

    //监听提交
    form.on('submit(update-pass)', function (data) {
        if (!data.field.password) {
            layer.msg('请输入登录密码！', {icon: 2});
            return false;
        }
        if (!data.field.pass) {
            layer.msg('请输入确认密码！', {icon: 2});
            return false;
        }
        if (data.field.password != data.field.pass) {
            layer.msg('两次密码不一致！', {icon: 2});
            return false;
        }
        $.post("<?php echo U('Index/updatePass');?>", data.field, function (res) {
            if (res.status == 1) {
                layer.msg(res.info, {icon: 1}, function () {
                    parent.layer.closeAll();
                });
            } else {
                layer.msg(res.info, {icon: 2});
            }
        });
        return false;
    });

    var location_url = window.location.href;
    $('#nav a').each(function () {
        if ($(this).attr('href') != 'javascript:;') {
            var url_arr = $(this).attr('href').split('.');
            if (location_url.indexOf(url_arr[0]) != -1) {
                if ($(this).data('type') == 2) {
                    $(this).parents('li').addClass('layui-nav-itemed');
                }
                $(this).parent().addClass('layui-this');
            }
        }
    });

</script>

    <div id="meal" class="hide">
        <form class="layui-form mt20">
            <div class="layui-form-item">
                <label class="layui-form-label">设置套餐</label>
                <?php $meal = C('COMPANY');unset($meal['base'],$meal[0]); ?>
                <?php $val = 1; ?>
                <?php if(is_array($meal)): $i = 0; $__LIST__ = $meal;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><div class="layui-input-inline" style="width: 120px;">
                        <input type="radio" name="meal" value="<?php echo ($val); ?>"
                               title="<?php echo ($row["name"]); ?>">
                    </div>
                    <?php $val++; endforeach; endif; else: echo "" ;endif; ?>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="id"/>
                    <button class="layui-btn" lay-submit="" lay-filter="save-meal">立即提交</button>
                </div>
            </div>
        </form>
    </div>
    <div id="credit" class="hide">
        <form class="layui-form mt20">
            <div class="layui-form-item">
                <label class="layui-form-label">企业评分</label>
                <div class="layui-input-inline" style="width: 100px;">
                    <input type="radio" name="credit" value="1"
                           title="1分">
                </div>
                <div class="layui-input-inline" style="width: 100px;">
                    <input type="radio" name="credit" value="2"
                           title="2分">
                </div>
                <div class="layui-input-inline" style="width: 100px;">
                    <input type="radio" name="credit" value="3"
                           title="3分">
                </div>
                <div class="layui-input-inline" style="width: 100px;">
                    <input type="radio" name="credit" value="4"
                           title="4分">
                </div>
                <div class="layui-input-inline" style="width: 100px;">
                    <input type="radio" name="credit" value="5"
                           title="5分">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="id"/>
                    <button class="layui-btn" lay-submit="" lay-filter="save-credit">立即提交</button>
                </div>
            </div>
        </form>
    </div>


    <script type="text/html" id="company-status">
        {{#  if(d.is_check == 1){ }}
        <a class="layui-btn layui-btn-xs">已认证通过</a>
        {{#  } else if(d.is_check ==2) { }}
        <a class="layui-btn layui-btn-xs layui-btn-danger">认证失败</a>
        {{#  } else if(d.is_check ==4) { }}
        <a class="layui-btn layui-btn-xs layui-btn-danger">二次认证，等待审核</a>
        {{#  } else { }}
        <a class="layui-btn layui-btn-xs layui-btn-danger">未认证</a>
        {{#  } }}
    </script>
    <script type="text/html" id="company-meal">
        {{#  if(d.company_meal == 1){ }}
        <a class="layui-btn layui-btn-xs">黄金会员</a>
        {{#  } else if(d.company_meal ==2) { }}
        <a class="layui-btn layui-btn-xs layui-btn-warm">铂金会员</a>
        {{#  } else if(d.company_meal ==3) { }}
        <a class="layui-btn layui-btn-xs layui-btn-danger">钻石会员</a>
        {{#  } else { }}
        <a class="layui-btn layui-btn-xs layui-btn-normal">普通会员</a>
        {{#  } }}
    </script>
    <script type="text/html" id="table-edit">
        <a class="layui-btn layui-btn-xs set-meal" data-id="{{d.id}}">设置套餐</a>
        <a class="layui-btn layui-btn-xs set-credit" data-id="{{d.id}}">设置评分</a>
        <a class="layui-btn layui-btn-xs" href="<?php echo U('partnerInfo');?>?user_id={{d.user_id}}">查看信息</a>
        {{#  if(d. company_meal== 3){ }}
        {{#  if(d.is_recommend == 1){ }}
        <a class="layui-btn layui-btn-xs layui-btn-danger set-recommend"
           data-url="<?php echo U('setPartnerRecommend');?>?id={{d.id}}&is_recommend=0">取消推荐</a>
        {{#  } else { }}
        <a class="layui-btn layui-btn-xs set-recommend"
           data-url="<?php echo U('setPartnerRecommend');?>?id={{d.id}}&is_recommend=1">商铺推荐</a>
        {{#  } }}
        {{#  } }}
        {{#  if(d.status == 0){ }}
        <a class="layui-btn layui-btn-xs layui-btn-danger del"
           data-url="<?php echo U('setPartnerStatus');?>?id={{d.id}}&status=1">禁用</a>
        {{#  } else { }}
        <a class="layui-btn layui-btn-xs layui-btn-warm del"
           data-url="<?php echo U('setPartnerStatus');?>?id={{d.id}}&status=0">启用</a>
        {{#  } }}
        <!-- <a class="layui-btn layui-btn-xs layui-btn-danger del-company" data-url="<?php echo U('delCompany');?>?id={{d.id}}">删除</a>-->


    </script>
    <script>
        $(function () {
            var index;
            var url = "<?php echo U('index');?>";
            var tableObj;
            form.render();
            //日期选择器
            laydate.render({
                elem: '#expire-time'
            });
            refresh();
            $('form.search').on('click', 'button.search', function () {
                refresh();
            });

            function refresh() {
                var param = $('form.search').serialize();
                var get_url = url + '?' + param;
                getData(get_url);
            }

            $('body').on('click', '.del', function () {
                var del_url = $(this).data('url');
                var text = $(this).text();
                layer.confirm('你确定要' + text + '此商铺吗？', function () {
                    $.get(del_url, {}, function (res) {
                        if (res.status == 1) {
                            layer.msg(res.info, {icon: 1}, function () {
                                tableObj.reload();
                            })
                        } else {
                            layer.msg(res.info, {icon: 2});
                        }
                    })
                })
            })

            $('body').on('click', '.set-recommend', function () {
                var del_url = $(this).data('url');
                var text = $(this).text();
                layer.confirm('你确定要将此' + text + '吗？', function () {
                    $.get(del_url, {}, function (res) {
                        if (res.status == 1) {
                            layer.msg(res.info, {icon: 1}, function () {
                                    tableObj.reload();
                                }
                            )
                        } else {
                            layer.msg(res.info, {icon: 1});
                        }
                    })
                })
            })


            //添加管理员账号
            $('body').on('click', '.set-meal', function () {
                $('#meal input[name=id]').val($(this).data('id'));
                form.render();
                index = layer.open({
                    title: "设置企业套餐",
                    type: 1,
                    area: ['800px', '200px'],
                    content: $('#meal'),
                })
            })


            //添加管理员账号
            $('body').on('click', '.set-credit', function () {
                $('#credit input[name=id]').val($(this).data('id'));
                form.render();
                index = layer.open({
                    title: "设置企业评分",
                    type: 1,
                    area: ['800px', '200px'],
                    content: $('#credit'),
                })
            })


            $('body').on('click', '.del-company', function () {
                var del_url = $(this).data('url');
                layer.confirm('你确定要删除该企业信息吗？', function () {
                    $.get(del_url, {}, function (res) {
                        if (res.status == 1) {
                            layer.msg(res.info, {icon: 1}, function () {
                                tableObj.reload();
                            })
                        } else {
                            layer.msg(res.info, {icon: 2});
                        }
                    })
                })
            })


            form.on("submit(save-meal)", function (data) {
                var _btn = data.elem;
                _btn.disabled = true;
                var post_url = "<?php echo U('setPartnerMeal');?>";
                var wait_index = layer.msg('请求中，请稍候', {icon: 16, time: false, shade: 0.8});
                $.post(post_url, data.field, function (res) {
                    _btn.disabled = false;
                    if (res.status == 1) {
                        layer.msg(res.info, {icon: 1}, function () {
                            layer.closeAll();
                            tableObj.reload();
                        })
                    } else {
                        layer.msg(res.info, {icon: 2});
                        layer.close(wait_index);
                    }
                });
                return false;
            });
            form.on("submit(save-credit)", function (data) {
                var _btn = data.elem;
                _btn.disabled = true;
                var url = "<?php echo U('setPartnerCredit');?>";
                var wait_index = layer.msg('请求中，请稍候', {icon: 16, time: false, shade: 0.8});
                $.post(url, data.field, function (res) {
                    _btn.disabled = false;
                    if (res.status == 1) {
                        layer.msg(res.info, {icon: 1}, function () {
                            layer.closeAll();
                            tableObj.reload();
                        })
                    } else {
                        layer.msg(res.info, {icon: 2});
                        layer.close(wait_index);
                    }
                });
                return false;
            });


            /**
             * 获取数据
             * @param url
             */
            function getData(url) {
                tableObj = table.render({
                    elem: '#table'
                    , url: url
                    , page: { //支持传入 laypage 组件的所有参数（某些参数除外，如：jump/elem） - 详见文档
                        layout: ['limit', 'count', 'prev', 'page', 'next', 'skip'] //自定义分页布局
                        , groups: 5 //只显示 1 个连续页码
                    }
                    , cellMinWidth: 100
                    , cols: [[
                        {field: 'user_id', title: '用户ID', sort: true}
                        , {field: 'company_name', width: 200, title: '公司名称'}
                        , {field: 'mobile', title: '手机号码'}
                        , {field: 'email', title: '邮箱'}
                        , {field: 'status', title: '企业状态', templet: '#company-status'}
                        , {field: 'company_address', title: '公司地址'}
                        , {field: 'company_meal', title: '会员级别', templet: '#company-meal'}
                        , {field: 'admin_name', title: '审核人', width: 100}
                        , {fixed: 'right', title: '操作', toolbar: '#table-edit', width: '35%'}
                    ]],
                });
            }
        })
    </script>

</body>
</html>