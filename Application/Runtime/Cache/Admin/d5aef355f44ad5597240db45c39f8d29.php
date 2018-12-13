<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>萝卜网-管理后台</title>
    <link rel="stylesheet" href="/Public/Admin/lib/layui/css/layui.css">
    <link rel="stylesheet" href="/Public/Admin/css/common.css">
    
    <style type="text/css">
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
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md3">
                <input type="text" name="username" value="<?php echo I('get.username');?>" placeholder="请输入管理员名称"
                       class="layui-input">
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md3">
                <button class="layui-btn search" type="button"><i class="layui-icon">
                    &#xe615;</i>查询
                </button>
                <a class="layui-btn layui-btn-danger" id="add-admin" href="javascript:;"><i class="layui-icon">
                    &#xe654;</i>添加管理员
                </a>
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

    <div id="admin-box" class="hide">
        <form class="layui-form mt20 w80">
            <div class="layui-form-item">
                <label class="layui-form-label">登录账号</label>
                <div class="layui-input-block">
                    <input type="text" name="username" class="layui-input" lay-verify="required" placeholder="请输入登录账号">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">真实姓名</label>
                <div class="layui-input-block">
                    <input type="text" name="realname" class="layui-input" lay-verify="required" placeholder="请输入真实姓名">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">登录密码</label>
                <div class="layui-input-block">
                    <input type="text" name="password" class="layui-input"
                           placeholder="请输入登录密码">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="id" value="0" id="admin-id"/>
                    <button class="layui-btn" lay-submit="" lay-filter="save-admin">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary" id="reset">重置</button>
                </div>
            </div>
        </form>
    </div>
    <div class="hide" id="auth-group-box">
        <form class="layui-form mt20">
            <div class="layui-form-item" id="group" style="margin: 20px">
            </div>
            <div class="layui-form-item" style="margin: 20px">
                <div class="layui-input-block">
                    <input type="hidden" name="uid" value="0" id="uid"/>
                    <button class="layui-btn" lay-submit="" lay-filter="save-auth">立即提交</button>
                </div>
            </div>
        </form>
    </div>


    <script type="text/html" id="table-edit">
        <a class="layui-btn layui-btn-xs update-admin" data-username="{{d.username}}"
           data-realname="{{d.realname}}" data-id="{{d.id}}">编辑</a>
        <a class="layui-btn layui-btn-xs layui-btn-danger del" data-url="<?php echo U('del');?>?id={{d.id}}">删除</a>
    </script>
    <script type="text/html" id="auth">
        <a class="layui-btn layui-btn-xs user-auth" data-id="{{d.id}}">立即授权</a>
    </script>
    <script type="text/html" id="status">
        <!-- 这里的 checked 的状态只是演示 -->
        <input type="checkbox" value="{{d.id}}" lay-skin="switch" lay-filter="status"
               lay-text="启用|禁用"
               {{ d.status== 1 ? 'checked' : '' }}>
    </script>
    <script>
        $(function () {
            var index;
            var auth_group_data = <?php echo ($auth_group_data); ?>;
            form.render();
            var url = "<?php echo U('index');?>";
            refresh();
            $('form.search').on('click', 'button.search', function () {
                refresh();
            });

            function refresh() {
                var param = $('form.search').serialize();
                var get_url = url + '?' + param;
                getData(get_url);
            }

            //操作
            $("body").on("click", ".user-auth", function () {  //编辑
                var uid = parseInt($(this).data('id'));
                if (uid > 0) {
                    $.get("/Admin/getUserAuthGroup", {uid: uid}, function (res) {
                        if (res.status == 1) {
                            $('#uid').val(uid);
                            var html = '';
                            if (res.info.data.length > 0) {
                                for (var i = 0; i < auth_group_data.length; i++) {
                                    if (res.info.data.indexOf(auth_group_data[i].id) != -1) {
                                        html += ' <div class="layui-input-inline" style="margin-top: 10px"><input type="checkbox" name="group_id[' + auth_group_data[i].id + ']" value="' + auth_group_data[i].id + ' "title="' + auth_group_data[i].title + '" checked> </div>';
                                    } else {
                                        html += ' <div class="layui-input-inline" style="margin-top: 10px"><input type="checkbox" name="group_id[' + auth_group_data[i].id + ']" value="' + auth_group_data[i].id + ' "title="' + auth_group_data[i].title + '"> </div>';
                                    }
                                }
                            } else {
                                for (var i = 0; i < auth_group_data.length; i++) {
                                    html += ' <div class="layui-input-inline" style="margin-top: 10px"><input type="checkbox" name="group_id[' + auth_group_data[i].id + ']" value="' + auth_group_data[i].id + ' "title="' + auth_group_data[i].title + '"> </div>';
                                }
                            }
                            $('#group').html(html);
                            form.render('checkbox');

                            index = layui.layer.open({
                                title: "管理员授权",
                                type: 1,
                                area: ['500px', '400px'],
                                content: $('#auth-group-box'),
                            })
                        } else {
                            layer.msg(res.info, {icon: 2});
                        }
                    })
                } else {
                    layer.msg('管理员编号异常，无法授权', {icon: 2})
                }
            })


            $('body').on('click', '.del', function () {
                var del_url = $(this).data('url');
                layer.confirm('你确定要删除该管理员信息吗？', function () {
                    $.get(del_url, {}, function (res) {
                        if (res.status == 1) {
                            layer.msg(res.info, {icon: 1}, function () {
                                var get_param = $('form.search').serialize();
                                var get_url = url + '?' + get_param;
                                getData(get_url);
                            })
                        } else {
                            layer.msg(res.info, {icon: 2});
                        }
                    })
                })
            })


            form.on("submit(save-auth)", function (data) {
                var _btn = data.elem;
                _btn.disabled = true;
                var url = "/Admin/userAuth";
                var wait_index = layer.msg('请求中，请稍候', {icon: 16, time: false, shade: 0.8});
                $.post(url, data.field, function (res) {
                    if (res.status == 1) {
                        layer.msg(res.info, {icon: 1}, function () {
                            window.location.reload();
                        });
                    } else {
                        _btn.disabled = false;
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
                table.render({
                    elem: '#table'
                    , url: url
                    , page: { //支持传入 laypage 组件的所有参数（某些参数除外，如：jump/elem） - 详见文档
                        layout: ['limit', 'count', 'prev', 'page', 'next', 'skip'] //自定义分页布局
                        , groups: 5 //只显示 1 个连续页码
                    }
                    , cellMinWidth: 100
                    , cols: [[
                        {field: 'id', title: '管理员ID', sort: true}
                        , {field: 'username', width: 160, title: '管理员账号'}
                        , {field: 'realname', width: 160, title: '真实姓名'}
                        , {field: 'add_time', width: 160, title: '添加时间'}
                        , {field: 'last_time', width: 160, title: '最后登录时间'}
                        , {field: 'last_ip', width: 160, title: '最后登录IP'}
                        , {field: 'status', title: '状态', templet: '#status'}
                        , {field: 'auth', title: '授权', templet: '#auth'}
                        , {fixed: 'right', title: '操作', toolbar: '#table-edit'}
                    ]],
                });
            }

            //添加管理员账号
            $('body').on('click', '#add-admin', function () {
                $('#reset').click();
                $('#admin-id').val(0);
                index = layer.open({
                    title: "添加管理员",
                    type: 1,
                    area: ['600px', '300px'],
                    content: $('#admin-box'),
                })
            })

            //编辑管理员账号
            $('body').on('click', '.update-admin', function () {
                $('#reset').click();
                $('#admin-box input[name=username]').val($(this).data('username'));
                $('#admin-box input[name=realname]').val($(this).data('realname'));
                $('#admin-id').val($(this).data('id'));
                index = layer.open({
                    title: "编辑管理员",
                    type: 1,
                    area: ['600px', '300px'],
                    content: $('#admin-box'),
                })
            })
            //监听性别操作
            form.on('switch(status)', function (obj) {
                $.post("<?php echo U('setStatus');?>", {id: obj.value}, function (res) {
                    if (res.status == 1) {
                        layer.msg(res.info, {icon: 1});
                    } else {
                        layer.msg(res.info, {icon: 2});
                    }

                })
            });

            //登录按钮事件
            form.on("submit(save-admin)", function (data) {
                var _btn = data.elem;
                _btn.disabled = true;
                var url = "/Admin/save"
                var wait_index = layer.msg('请求中，请稍候', {icon: 16, time: false, shade: 0.8});
                $.post(url, data.field, function (res) {
                    if (res.status == 1) {
                        layer.msg(res.info, {icon: 1}, function () {
                            layer.closeAll();
                            refresh();
                        })
                    } else {
                        _btn.disabled = false;
                        layer.msg(res.info, {icon: 2});
                        layer.close(wait_index);
                    }
                });
                return false;
            });
        })
    </script>

</body>
</html>