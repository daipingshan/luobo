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
        
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
        <legend>权限设置</legend>
    </fieldset>
    <blockquote class="layui-elem-quote news_search">
        <div class="layui-inline">
            <a class="layui-btn layui-btn-danger" id="add-auth" href="javascript:;"><i class="layui-icon">
                &#xe654;</i>添加权限
            </a>
        </div>
    </blockquote>
    <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$parent_row): $mod = ($i % 2 );++$i;?><fieldset class="layui-elem-field" style="position: relative">
            <legend>
                <i class="layui-icon <?php echo ($parent_row["icon"]); ?>" data-icon="<?php echo ($parent_row["icon"]); ?>"></i>
                <?php echo ($parent_row["title"]); ?>
            </legend>
            <div class="layui-field-box">
                <div class="layui-form links_list">
                    <table class="layui-table">
                        <colgroup>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col width="20%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th style="text-align:center;">权限值</th>
                            <th style="text-align:center;">权限名称</th>
                            <th style="text-align:center;">权限图标</th>
                            <th style="text-align:center;">菜单状态</th>
                            <th style="text-align:center;">权限状态</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td align="center"><?php echo ($parent_row["name"]); ?></td>
                            <td align="center"><?php echo ($parent_row["title"]); ?></td>
                            <td align="center">
                                <i class="layui-icon <?php echo ($parent_row["icon"]); ?>" data-icon="<?php echo ($parent_row["icon"]); ?>"></i>
                            </td>
                            <?php if(($parent_row["display"]) == "1"): ?><td style="text-align:center;color:#5FB878">显示</td>
                                <?php else: ?>
                                <td style="text-align:center;color:#FF5722">屏蔽</td><?php endif; ?>
                            <?php if(($parent_row["status"]) == "1"): ?><td style="text-align:center;color:#5FB878">启用</td>
                                <?php else: ?>
                                <td style="text-align:center;color:#FF5722">禁用</td><?php endif; ?>
                            <td align="center">
                                <a class="layui-btn layui-btn-xs auth-edit" data-id="<?php echo ($parent_row["id"]); ?>"
                                   data-title="<?php echo ($parent_row["title"]); ?>" data-sort="<?php echo ($parent_row["sort"]); ?>"
                                   data-name="<?php echo ($parent_row["name"]); ?>"
                                   data-display="<?php echo ($parent_row["display"]); ?>" data-icon="<?php echo ($parent_row["icon"]); ?>"><i
                                        class="iconfont icon-edit"></i> 编辑</a>
                                <?php if(($parent_row["status"]) == "1"): ?><a class="layui-btn layui-btn-danger layui-btn-xs auth-status"
                                       data-id="<?php echo ($parent_row["id"]); ?>"><i class="layui-icon">&#x1007;</i> 禁用</a>
                                    <?php else: ?>
                                    <a class="layui-btn layui-btn-danger layui-btn-xs auth-status"
                                       data-id="<?php echo ($parent_row["id"]); ?>"><i class="layui-icon">&#x1005;</i> 启用</a><?php endif; ?>
                            </td>
                        </tr>
                        <?php if(is_array($parent_row["son_data"])): $i = 0; $__LIST__ = $parent_row["son_data"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><tr>
                                <td align="center"><?php echo ($row["name"]); ?></td>
                                <td align="center"><?php echo ($row["title"]); ?></td>
                                <td align="center">
                                    <i class="layui-icon <?php echo ($row["icon"]); ?>" data-icon="<?php echo ($row["icon"]); ?>"></i>
                                </td>
                                <?php if(($row["display"]) == "1"): ?><td style="text-align:center;color:#5FB878">显示</td>
                                    <?php else: ?>
                                    <td style="text-align:center;color:#FF5722">屏蔽</td><?php endif; ?>
                                <?php if(($row["status"]) == "1"): ?><td style="text-align:center;color:#5FB878">启用</td>
                                    <?php else: ?>
                                    <td style="text-align:center;color:#FF5722">禁用</td><?php endif; ?>
                                <td align="center">
                                    <a class="layui-btn layui-btn-xs auth-edit" data-id="<?php echo ($row["id"]); ?>"
                                       data-title="<?php echo ($row["title"]); ?>" data-sort="<?php echo ($row["sort"]); ?>" data-name="<?php echo ($row["name"]); ?>"
                                       data-display="<?php echo ($row["display"]); ?>" data-icon="<?php echo ($row["icon"]); ?>"><i
                                            class="iconfont icon-edit"></i> 编辑</a>
                                    <?php if(($row["status"]) == "1"): ?><a class="layui-btn layui-btn-danger layui-btn-xs auth-status"
                                           data-id="<?php echo ($row["id"]); ?>"><i class="layui-icon">&#x1007;</i> 禁用</a>
                                        <?php else: ?>
                                        <a class="layui-btn layui-btn-danger layui-btn-xs auth-status"
                                           data-id="<?php echo ($row["id"]); ?>"><i class="layui-icon">&#x1005;</i> 启用</a><?php endif; ?>
                                </td>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </fieldset><?php endforeach; endif; else: echo "" ;endif; ?>

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

    <div id="auth-box" class="hide">
        <form class="layui-form w80 mt20">
            <div class="layui-form-item" id="partner-id">
                <label class="layui-form-label">上级权限</label>
                <div class="layui-input-block">
                    <select name="parent_id" class="layui-select">
                        <option value="0">顶级权限</option>
                        <?php if(is_array($parent_arr)): foreach($parent_arr as $k=>$row): ?><option value="<?php echo ($k); ?>"><?php echo ($row); ?></option><?php endforeach; endif; ?>
                    </select>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">权限名称</label>
                <div class="layui-input-block">
                    <input type="text" name="title" class="layui-input" lay-verify="required" placeholder="请输入权限名称">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">权限地址</label>
                <div class="layui-input-block">
                    <input type="text" name="name" class="layui-input" placeholder="请输入权限地址">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">权限排序</label>
                <div class="layui-input-block">
                    <input type="number" name="sort" class="layui-input"
                           placeholder="请输入权限排序">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">权限图标</label>
                <div class="layui-input-block">
                    <input type="text" name="icon" class="layui-input"
                           placeholder="请输入权限图标">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">菜单显示</label>
                <div class="layui-input-block">
                    <input type="radio" name="display" value="1" title="显示" checked>
                    <input type="radio" name="display" value="0" title="屏蔽">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="id" value="0" id="auth-id"/>
                    <button class="layui-btn" lay-submit="" lay-filter="save-auth">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary" id="reset">重置</button>
                </div>
            </div>
        </form>
    </div>



    <script>
        $(function () {
            var index = 0;
            //添加广告
            $("body").on("click", "#add-auth", function () {
                $('#auth-box #partner-id').show();
                $('#auth-id').val(0);
                $('#reset').click();
                form.render();
                layui.layer.open({
                    title: "添加权限",
                    type: 1,
                    area: ['600px', '450px'],
                    content: $('#auth-box'),
                })
            })

            //添加广告
            $("body").on("click", ".auth-edit", function () {  //编辑
                $('#auth-box #partner-id').hide();
                $('#auth-id').val($(this).data('id'));
                $('#auth-box input[name=name]').val($(this).data('name'));
                $('#auth-box input[name=title]').val($(this).data('title'));
                $('#auth-box input[name=icon]').val($(this).data('icon'));
                $('#auth-box input[name=sort]').val($(this).data('sort'));
                if ($(this).data('display') == 1) {
                    $('#auth-box input[name=display]').eq(0).attr('checked', true);
                } else {
                    $('#auth-box input[name=display]').eq(1).attr('checked', true);
                }
                form.render();
                layui.layer.open({
                    title: "修改权限",
                    type: 1,
                    area: ['600px', '450px'],
                    content: $('#auth-box'),
                })
            })

            $("body").on("click", ".auth-status", function () {  //删除
                var _this = $(this);
                layer.confirm('确定要更新当前栏目状态吗？', {icon: 3, title: '提示信息'}, function () {
                    index = layer.msg('更新中，请稍候', {icon: 16, time: false, shade: 0.8});
                    $.post("<?php echo U('setAuthStatus');?>", {id: _this.data('id')}, function (res) {
                        if (res.status == 1) {
                            layer.msg(res.info, {icon: 1}, function () {
                                window.location.reload(true);
                            });
                        } else {
                            layer.msg(res.info, {icon: 2});
                            layer.close(index);
                        }
                    });
                })
            });

            //登录按钮事件
            form.on("submit(save-auth)", function (data) {
                var _btn = data.elem;
                _btn.disabled = true;
                var url = "<?php echo U('saveAuth');?>";
                index = layer.msg('请求中，请稍候', {icon: 16, time: false, shade: 0.8});
                $.post(url, data.field, function (res) {
                    if (res.status == 1) {
                        layer.msg(res.info, {icon: 1}, function () {
                                window.location.reload();
                            }
                        )
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