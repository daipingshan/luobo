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
        <legend>招聘信息</legend>
        <div class="layui-field-box">
            <div class="layui-row mt20 w80">
                <form class="layui-form" action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">分类信息</label>
                        <div class="layui-input-inline" style="padding-top: 8px">
                            <?php echo ($info['project_type_info']); ?>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">重置分类</label>
                        <div class="layui-input-inline">
                            <select class="layui-select" name="project_type_id">
                                <option value="">请选择分类</option>
                                <?php if(is_array($project_type_data)): foreach($project_type_data as $key=>$val): ?><option value="<?php echo ($val["id"]); ?>"><?php echo ($val["name"]); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">城市信息</label>
                        <div class="layui-input-block" style="padding-top: 8px">
                            <?php echo ($info['city_info']); ?>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">重置城市</label>
                        <div class="layui-input-inline">
                            <select name="province_id" lay-filter="province">
                                <option value="">请选择省份</option>
                                <?php if(is_array($city_data)): foreach($city_data as $key=>$val): ?><option value="<?php echo ($key); ?>"><?php echo ($val); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <select name="city_id" id="city" lay-filter="city">
                                <option value="">请选择城市</option>
                            </select>
                        </div>
                        <div class="layui-input-inline">
                            <select name="district_id" id="district">
                                <option value="">请选择区域</option>
                            </select>
                        </div>
                    </div>
                    <div class="layui-form-item" id="url">
                        <label class="layui-form-label">项目名称</label>
                        <div class="layui-input-block">
                            <input type="text" name="name" value="<?php echo ($info["name"]); ?>" placeholder="请输入项目名称"
                                   autocomplete="off"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">项目logo</label>
                        <div class="layui-input-block" style="position: relative">
                            <input type="text" name="image" value="<?php echo ($info["image"]); ?>" id="img-input"
                                   class="layui-input layui-disabled"
                                   placeholder="请上传项目logo" readonly style="width: 75%">
                            <div style="position: absolute;left:80% ;top:0;">
                                <button type="button" class="layui-btn" id="layui-upload-file">
                                    <i class="layui-icon">&#xe67c;</i>上传图片
                                </button>
                            </div>
                            <div style="position: absolute;left:80% ;top:50px;">
                                <img src="<?php echo ($info["image"]); ?>" width="100" id="img-view">
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">最低薪资</label>
                        <div class="layui-input-inline">
                            <input type="number" name="salary_min" value="<?php echo ($info["salary_min"]); ?>" placeholder="请输入最低薪资"
                                   class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">K</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">最高薪资</label>
                        <div class="layui-input-inline">
                            <input type="number" name="salary_max" value="<?php echo ($info["salary_max"]); ?>" placeholder="请输入最高薪资"
                                   class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">K</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">联系人</label>
                        <div class="layui-input-inline">
                            <input type="text" name="contacts" value="<?php echo ($info["contacts"]); ?>" placeholder="请输入联系人"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">联系电话</label>
                        <div class="layui-input-block">
                            <input type="text" name="tel" value="<?php echo ($info["tel"]); ?>" placeholder="请输入联系电话"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">项目排序</label>
                        <div class="layui-input-inline">
                            <input type="number" name="sort" value="<?php echo ($info["sort"]); ?>" placeholder="请输入项目排序"
                                   class="layui-input">
                        </div>
                        <div class="layui-form-mid layui-word-aux">数值越小排序越靠前</div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">门店返费</label>
                        <div class="layui-input-inline">
                            <input type="number" name="subsidy" value="<?php echo ($info["subsidy"]); ?>" placeholder="请输入项目补助"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">平台补助</label>
                        <div class="layui-input-inline">
                            <input type="number" name="rebate" value="<?php echo ($info["rebate"]); ?>" placeholder="请输入平台补助"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">补助说明</label>
                        <div class="layui-input-block">
                            <script id="desc" type="text/plain"><?php echo ($info["rebate_desc"]); ?></script>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">工作地址</label>
                        <div class="layui-input-block">
                            <input type="text" name="address" value="<?php echo ($info["address"]); ?>" placeholder="请输入工作地址"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">招聘内容</label>
                        <div class="layui-input-block">
                            <script id="content" type="text/plain"><?php echo ($info["content"]); ?></script>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <input type="hidden" name="id" value="<?php echo ($info["id"]); ?>">
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
                toolbars: BASE_BAR,
            });
            UE.getEditor('desc', {
                toolbars: BASE_BAR,
            });
            form.on('select(province)', function (data) {
                if (data.value > 0) {
                    var html = "<option value='0'>请选择城市</option>"
                    $.get("<?php echo U('getCity');?>", {id: data.value}, function (res) {
                        html += res.data.html;
                        $('#city').html(html);
                        $('#district').html("<option value='0'>请选择区域</option>");
                        form.render();
                    }, 'json')
                }
            });
            form.on('select(city)', function (data) {
                if (data.value > 0) {
                    var html = "<option value='0'>请选择区域</option>"
                    $.get("<?php echo U('getCity');?>", {id: data.value}, function (res) {
                        html += res.data.html;
                        $('#district').html(html);
                        form.render();
                    }, 'json')
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
                        layer.msg(res.msg, {icon: 1})
                        $('#img-input').val(res.data[0]);
                        $('#img-view').attr('src', res.data[0]).parent().show();
                    } else {
                        layer.msg(res.msg, {icon: 2})
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
                data.field.rebate_desc = UE.getEditor('desc').getContent();
                $.post(url, data.field, function (res) {
                    _btn.disabled = false;
                    if (res.status == 1) {
                        layer.msg(res.info, {icon: 1}, function () {
                            location.href = "<?php echo U('index');?>"
                        })
                    } else {
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