<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>萝卜网-管理后台</title>
    <link rel="stylesheet" href="/Public/Admin/lib/layui/css/layui.css">
    <link rel="stylesheet" href="/Public/Admin/css/common.css">
    
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
        
    <fieldset class="layui-elem-field layui-field-title mt20">
        <legend>权限组设置</legend>
    </fieldset>
    <form class="layui-form">
        <div class="layui-form-item">
            <label class="layui-form-label">权限组名称</label>
            <div class="layui-input-block">
                <input type="text" name="title" class="layui-input" value="<?php echo ($info['title']); ?>" lay-verify="required"
                       placeholder="请输入权限组名称">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">权限组备注</label>
            <div class="layui-input-block">
                <input type="text" name="remark" class="layui-input" value="<?php echo ($info['remark']); ?>" lay-verify="required"
                       placeholder="请输入权限组备注">
            </div>
        </div>
        <?php $rule_arr = explode(',',$info['rules']); ?>
        <div class="row">
            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$parent_row): $mod = ($i % 2 );++$i;?><label class="layui-form-label">权限设置</label>
                <fieldset class="layui-elem-field" style="position: relative">
                    <legend>
                        <i class="layui-icon<?php echo ($parent_row["icon"]); ?>" data-icon="<?php echo ($parent_row["icon"]); ?>"></i>
                        <?php echo ($parent_row["title"]); ?>
                    </legend>

                    <div class="layui-field-box">
                        <div class="layui-input-inline" style="margin-top: 10px">
                            <?php if(in_array($parent_row['id'],$rule_arr)): ?><input type="checkbox" name="rules[<?php echo ($parent_row["id"]); ?>]" value="<?php echo ($parent_row["id"]); ?>"
                                       title="<?php echo ($parent_row["title"]); ?>" checked>
                                <?php else: ?>
                                <input type="checkbox" name="rules[<?php echo ($parent_row["id"]); ?>]" value="<?php echo ($parent_row["id"]); ?>"
                                       title="<?php echo ($parent_row["title"]); ?>"><?php endif; ?>
                        </div>
                        <?php if(is_array($parent_row["son_data"])): $i = 0; $__LIST__ = $parent_row["son_data"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i; if(in_array($row['id'],$rule_arr)): ?><div class="layui-input-inline" style="margin-top: 10px">
                                    <input type="checkbox" name="rules[<?php echo ($row["id"]); ?>]" value="<?php echo ($row["id"]); ?>"
                                           title="<?php echo ($row["title"]); ?>" checked>
                                </div>
                                <?php else: ?>
                                <div class="layui-input-inline" style="margin-top: 10px">
                                    <input type="checkbox" name="rules[<?php echo ($row["id"]); ?>]" value="<?php echo ($row["id"]); ?>"
                                           title="<?php echo ($row["title"]); ?>">
                                </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                    </div>
                </fieldset><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-block">
                <input type="hidden" name="id" value="<?php echo ($id); ?>"/>
                <button class="layui-btn" lay-submit="" lay-filter="save-auth-group">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary" id="reset">重置</button>
            </div>
        </div>
    </form>

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


    <script>
        $(function () {
            var index = 0;
            //登录按钮事件
            form.on("submit(save-auth-group)", function (data) {
                var _btn = data.elem;
                _btn.disabled = true;
                var url = "<?php echo U('saveAuthGroup');?>";
                index = layer.msg('请求中，请稍候', {icon: 16, time: false, shade: 0.8});
                $.post(url, data.field, function (res) {
                    if (res.status == 1) {
                        layer.msg(res.info, {icon: 1}, function () {
                            window.location.href = "<?php echo U('authGroupList');?>"
                        })
                    } else {
                        _btn.disabled = false;
                        layer.msg(res.info, {icon: 2});
                        layer.close(index);
                    }
                });
                return false;
            });

        })
    </script>

</body>
</html>