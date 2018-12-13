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
                <input type="text" name="name" value="<?php echo I('get.name');?>" placeholder="请输入会员名称" class="layui-input">
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md3">
                <select name="status" class="layui-select">
                    <option value="">请选择会员身份</option>
                    <option value="1">无资料</option>
                    <option value="2">用户</option>
                    <option value="3">企业</option>
                    <option value="4">门店</option>
                </select>
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md3">
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

    <div id="box" class="hide">
        <form class="layui-form mt20">
            <div class="layui-form-item">
                <label class="layui-form-label">充值金额</label>
                <div class="layui-input-block">
                    <input type="number" step="1" placeholder="请输入充值金额" class="layui-input" name="amount">
                    <p style="color: red;font-size: 12px">当充值金额大于0时，直接填写充值金额即可；如：【10】、当需要扣除余额时填负数既可；如【-10】</p>
                </div>

            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">充值说明</label>
                <div class="layui-input-block">
                    <input type="text" placeholder="请输入充值说明" class="layui-input" name="represent">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="id"/>
                    <button class="layui-btn" lay-submit="" lay-filter="save-admin">立即提交</button>
                </div>
            </div>
        </form>
    </div>
    <div id="set-uid" class="hide">
        <form class="layui-form mt20">
            <div class="layui-form-item">
                <label class="layui-form-label">邀请人ID</label>
                <div class="layui-input-block">
                    <input type="number" placeholder="请输入邀请人ID" class="layui-input" name="invite_uid">
                </div>

            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="id"/>
                    <button class="layui-btn" lay-submit="" lay-filter="save-uid">立即提交</button>
                </div>
            </div>
        </form>
    </div>


    <script type="text/html" id="status">
        {{#  if(d.is_user_info == 1){ }}
        <a class="layui-btn layui-btn-xs layui-btn-normal">用户</a>
        {{#  } }}
        {{#  if(d.is_company_info == 1){ }}
        <a class="layui-btn layui-btn-xs">企业</a>
        {{#  } }}
        {{#  if(d.is_shop == 1){ }}
        <a class="layui-btn layui-btn-xs layui-btn-danger">门店</a>
        {{#  } }}
    </script>
    <script type="text/html" id="table-edit">
        <a class="layui-btn layui-btn-xs layui-btn-danger set-flow" data-url="<?php echo U('getUserFlow');?>?id={{d.id}}">交易记录</a>
        <a class="layui-btn layui-btn-xs set-amount" data-id="{{d.id}}">会员充值</a>
        <a class="layui-btn layui-btn-xs layui-btn-normal set-uid" data-id="{{d.id}}" data-uid="{{d.invite_uid}}">设置邀请人ID</a>
    </script>
    <script>
        $(function () {
            var tableObj;
            var index;
            form.render();
            var url = "<?php echo U('userList');?>";
            refresh();
            $('form.search').on('click', 'button.search', function () {
                refresh();
            });

            function refresh() {
                var param = $('form.search').serialize();
                var get_url = url + '?' + param;
                getData(get_url);
            }

            //添加管理员账号
            $('body').on('click', '.set-amount', function () {
                $('#box input[name=id]').val($(this).data('id'));
                $('#box input[name=amount]').val('');
                index = layer.open({
                    title: "会员充值",
                    type: 1,
                    area: ['800px', '260px'],
                    content: $('#box'),
                })
            })

            //添加管理员账号
            $('body').on('click', '.set-uid', function () {
                $('#set-uid input[name=id]').val($(this).data('id'));
                $('#set-uid input[name=invite_uid]').val($(this).data('uid'));
                index = layer.open({
                    title: "设置邀请人ID",
                    type: 1,
                    area: ['800px', '200px'],
                    content: $('#set-uid'),
                })
            })

            //添加管理员账号
            $('body').on('click', '.set-flow', function () {
                index = layer.open({
                    title: "交易记录",
                    type: 2,
                    area: ['90%', '90%'],
                    content: $(this).data('url'),
                })
            })

            form.on("submit(save-admin)", function (data) {
                var _btn = data.elem;
                _btn.disabled = true;
                var url = "<?php echo U('setUserAmount');?>";
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
            });

            form.on("submit(save-uid)", function (data) {
                var _btn = data.elem;
                _btn.disabled = true;
                var url = "<?php echo U('setUserInviteUid');?>";
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
                        {field: 'id', title: '会员ID', sort: true}
                        , {field: 'invite_uid', title: '邀请人ID'}
                        , {field: 'mobile', title: '手机号码'}
                        , {field: 'email', title: '邮箱'}
                        , {field: 'user_name', title: '用户姓名'}
                        , {field: 'company_name', title: '企业名称'}
                        , {field: 'amount', title: '账户余额'}
                        , {field: 'status', title: '会员身份', toolbar: '#status', width: 150}
                        , {fixed: 'right', title: '操作', toolbar: '#table-edit', width: '20%'}
                    ]],
                });
            }
        })
    </script>

</body>
</html>