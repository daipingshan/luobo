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
        
    <fieldset class="layui-elem-field w80">
        <legend>文章信息</legend>
        <div class="layui-field-box">
            <div class="layui-row mt20 w80">
                <form class="layui-form" action="">
                    <?php if(empty($info)): ?><div class="layui-form-item" id="article_type">
                            <label class="layui-form-label">文章分类</label>
                            <div class="layui-input-inline">
                                <select name="parent_type_id" lay-filter="type">
                                    <option value="">请选择文章一级分类</option>
                                    <?php if(is_array($type_data)): foreach($type_data as $key=>$val): ?><option value="<?php echo ($key); ?>"><?php echo ($val); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                            <div class="layui-input-inline">
                                <select name="type_id" id="type-id">
                                    <option value="">请选择文章二级分类</option>
                                </select>
                            </div>
                        </div><?php endif; ?>
                    <div class="layui-form-item">
                        <label class="layui-form-label">文章标题</label>
                        <div class="layui-input-block">
                            <input type="text" name="title" value="<?php echo ($info["title"]); ?>" placeholder="文章标题"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">文章内容</label>
                        <div class="layui-input-block">
                            <script id="content" type="text/plain"><?php echo ($info["content"]); ?></script>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">文章配图</label>
                        <div class="layui-input-block" style="position: relative">
                            <input type="text" name="img" value="<?php echo ($info["img"]); ?>" id="img-input"
                                   class="layui-input layui-disabled"
                                   placeholder="请上传文章配图" readonly style="width: 75%">
                            <div style="position: absolute;left:80% ;top:0;">
                                <button type="button" class="layui-btn" id="layui-upload-file">
                                    <i class="layui-icon">&#xe67c;</i>上传图片
                                </button>
                            </div>
                            <?php if(empty($info)): ?><div style="position: absolute;left:80% ;top:50px;display:none;">
                                    <img src="" width="100" id="img-view">
                                </div>
                                <?php else: ?>
                                <div style="position: absolute;left:80% ;top:50px;">
                                    <img src="<?php echo ($info["img"]); ?>" width="100" id="img-view">
                                </div><?php endif; ?>

                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">文章排序</label>
                        <div class="layui-input-inline">
                            <input type="number" name="sort" value="<?php echo ($info['sort'] ? $info['sort'] : 255); ?>"
                                   placeholder="请输入文章排序"
                                   autocomplete="off"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <input type="hidden" name="id" value="<?php echo ($info['id'] ? $info['id'] : 0); ?>" id="article-id"/>
                            <button class="layui-btn" lay-submit="" lay-filter="save">立即提交</button>
                            <button type="reset" class="layui-btn layui-btn-primary" id="reset">重置</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </fieldset>

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


    <script type="text/javascript" charset="utf-8" src="/Public/Admin/Editor//ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/Public/Admin/Editor//ueditor.all.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="/Public/Admin/Editor//lang/zh-cn/zh-cn.js"></script>
    <script>
        $(function () {
                var index;
                window.UEDITOR_CONFIG.initialFrameHeight = 300; //设置高度
                window.UEDITOR_CONFIG.initialFrameWidth = 800;//设置宽度录
                UE.Editor.prototype._bkGetActionUrl = UE.Editor.prototype.getActionUrl;
                UE.Editor.prototype.getActionUrl = function (action) {
                    if (action == 'uploadimage' || action == 'uploadscrawl' || action == 'uploadimage' || action == 'uploadvideo') {
                        return "<?php echo U('Common/uploadEditImg');?>";
                    } else {
                        return this._bkGetActionUrl.call(this, action);
                    }
                }
                UE.getEditor('content', {
                    toolbars: [['FullScreen', 'simpleupload', 'bold', 'underline', 'fontborder', 'strikethrough', '|', 'selectall', 'cleardoc', '|',
                        'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                        'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify']],
                });
                form.on('select(type)', function (data) {
                    if (data.value > 0) {
                        $.get("<?php echo U('Article/getArticleType');?>", {parent_id: data.value}, function (res) {
                            var type_data = res.info.data;
                            var len = res.info.data.length;
                            if (len > 0) {
                                var html = "<option value='0'>请选择文章二级分类</option>";
                                for (var i = 0; i < len; i++) {
                                    html += "<option value='" + type_data[i].id + "'>" + type_data[i].name + "</option>"
                                }
                                $('#type-id').html(html);
                                form.render();
                            }
                        })
                    }
                });
                upload.render({
                    elem: '#layui-upload-file'
                    , url: '/Common/uploadImg'
                    , before: function () {
                        index = layer.msg('上传中，请稍候', {icon: 16, time: false, shade: 0.8});
                    }
                    , done: function (res) {
                        layer.close(index);
                        if (res.code == 1) {
                            layer.msg(res.msg, {icon: 1});
                            $('#img-input').val(res.data[0]);
                            $('#img-view').attr('src', res.data[0]).parent().show();
                        } else {
                            layer.msg(res.msg, {icon: 2});
                        }
                    }
                });

                //登录按钮事件
                form.on("submit(save)", function (data) {
                    var _btn = data.elem;
                    _btn.disabled = true;
                    var url = "<?php echo U('save');?>";
                    var wait_index = layer.msg('请求中，请稍候', {icon: 16, time: false, shade: 0.8});
                    data.field.content = UE.getEditor('content').getContent();
                    $.post(url, data.field, function (res) {
                        _btn.disabled = false;
                        if (res.status == 1) {
                            layer.msg(res.info, {icon: 1}, function () {
                                location.href = "<?php echo U('Article/index');?>"
                            })
                        } else {
                            layer.msg(res.info, {icon: 2});
                            layer.close(wait_index);
                        }
                    });
                    return false;
                });
            }
        )
    </script>

</body>
</html>