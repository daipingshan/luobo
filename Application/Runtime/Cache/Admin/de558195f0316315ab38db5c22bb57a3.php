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
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md3">
                <input type="text" name="title" value="<?php echo I('get.title');?>" placeholder="请输入广告标题" class="layui-input">
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md3">
                <select name="type_id">
                    <option value="">请选择广告类型</option>
                    <?php if(is_array($type_data)): foreach($type_data as $key=>$val): ?><option value="<?php echo ($key); ?>"
                        <?php if(I('get.type_id') == $key): ?>selected<?php endif; ?>
                        ><?php echo ($val); ?></option><?php endforeach; endif; ?>
                </select>
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md3">
                <button class="layui-btn search" type="button"><i class="layui-icon">
                    &#xe615;</i>查询
                </button>
                <a class="layui-btn layui-btn-danger" id="add-advert"><i class="layui-icon">
                    &#xe654;</i>添加广告
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

    <div class="hide" id="advert-box">
        <div class="layui-row mt20 w80">
            <form class="layui-form" action="">
                <div class="layui-form-item">
                    <label class="layui-form-label">广告类型</label>
                    <div class="layui-input-inline">
                        <select name="type_id">
                            <option value="">请选择广告类型</option>
                            <?php if(is_array($type_data)): foreach($type_data as $key=>$val): ?><option value="<?php echo ($key); ?>"
                                <?php if(I('get.type_id') == $key): ?>selected<?php endif; ?>
                                ><?php echo ($val); ?></option><?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">广告标题</label>
                    <div class="layui-input-block">
                        <input type="text" name="title" placeholder="广告标题"
                               class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item" id="url">
                    <label class="layui-form-label">跳转链接</label>
                    <div class="layui-input-block">
                        <input type="text" name="location_url" value="" placeholder="请输入跳转链接【不跳转可为空】"
                               autocomplete="off"
                               class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">广告图片</label>
                    <div class="layui-input-block" style="position: relative">
                        <input type="text" name="img_url" id="img-input"
                               class="layui-input layui-disabled"
                               placeholder="请上传广告图片" readonly style="width: 75%">
                        <div style="position: absolute;left:80% ;top:0;">
                            <button type="button" class="layui-btn" id="layui-upload-file">
                                <i class="layui-icon">&#xe67c;</i>上传图片
                            </button>
                        </div>
                        <div style="position: absolute;left:80% ;top:50px;display:none;">
                            <img src="" width="100" id="img-view">
                        </div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">开始时间</label>
                    <div class="layui-input-inline">
                        <input type="text" name="begin_time" value="" id="begin-time" placeholder="请选择广告开始时间"
                               class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">结束时间</label>
                    <div class="layui-input-inline">
                        <input type="text" name="end_time" value="" id="end-time" placeholder="请选择广告开始时间"
                               class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">广告排序</label>
                    <div class="layui-input-inline">
                        <input type="number" name="sort" value="255" placeholder="请输入广告排序"
                               autocomplete="off"
                               class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <input type="hidden" name="id" value="0" id="advert-id"/>
                        <button class="layui-btn" lay-submit="" lay-filter="save">立即提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary" id="reset">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script type="text/html" id="table-edit">
        <a class="layui-btn layui-btn-xs save" data-id="{{d.id}}" data-title="{{d.title}}"
           data-type="{{d.advert_type_id}}" data-sort="{{d.sort}}" data-begin="{{d.begin_time}}"
           data-end="{{d.end_time}}" data-pic="{{d.img_url}}" data-url="{{d.location_url}}">编辑</a>
        <a class="layui-btn layui-btn-xs layui-btn-danger del-advert" data-url="<?php echo U('delAdvert');?>?id={{d.id}}">删除</a>
    </script>
    <script type="text/html" id="img">
        {{#  if(d.img_url){ }}
        <div class="open-img-layer cursor"><img layer-src={{d.img_url}} src="{{d.img_url}}" alt="{{d.title}}"
                                                width="120"></div>
        {{#  } else { }}
            <p style="color: #FF5722">尚未上传图片</p>
        {{#  } }}
    </script>
    <script>
        $(function () {
            var index;
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

            $('body').on('mouseover', '.open-img-layer', function () {
                var _this = $(this);
                layer.photos({
                    photos: _this
                });
            })


            $('body').on('click', '.del-advert', function () {
                var del_url = $(this).data('url');
                layer.confirm('你确定要删除此广告？', function () {
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

            //添加管理员账号
            $('body').on('click', '#add-advert', function () {
                $('#reset').click();
                $('#advert-box select[name=type_id] option').each(function () {
                    if ($(this).val() == '') {
                        $(this).attr('selected', true);
                    } else {
                        if ($(this).attr('selected')) {
                            $(this).removeAttr('selected')
                        }
                    }
                })
                $('#advert-box #img-view').attr('src', '').parent().hide();
                $('#advert-id').val(0);
                index = layer.open({
                    title: "添加广告",
                    type: 1,
                    area: ['70%', '70%'],
                    content: $('#advert-box'),
                })
            })

            //添加管理员账号
            $('body').on('click', '.save', function () {
                $('#reset').click();
                var type_id = $(this).data('type')
                $('#advert-box select[name=type_id] option').each(function () {
                    if ($(this).val() == type_id) {
                        $(this).attr('selected', true);
                    } else {
                        if ($(this).attr('selected')) {
                            $(this).removeAttr('selected')
                        }
                    }
                })
                $('#advert-box input[name=title]').val($(this).data('title'));
                $('#advert-box input[name=img_url]').val($(this).data('pic'));
                $('#advert-box #img-view').attr('src', $(this).data('pic')).parent().show();
                $('#advert-box input[name=location_url]').val($(this).data('url'));
                $('#advert-box input[name=begin_time]').val($(this).data('begin'));
                $('#advert-box input[name=end_time]').val($(this).data('end'));
                $('#advert-box input[name=sort]').val($(this).data('sort'));
                $('#advert-id').val($(this).data('id'));
                form.render();
                index = layer.open({
                    title: "编辑广告",
                    type: 1,
                    area: ['70%', '70%'],
                    content: $('#advert-box'),
                })
            })

            //编辑管理员账号
            $('body').on('click', '.update-admin', function () {
                $('#reset').click();
                $('input[name=title]').val($(this).data('title'));
                $('input[name=begin_time]').val($(this).data('begin_time'));
                $('input[name=end_time]').val($(this).data('end_time'));
                $('#advert-id').val($(this).data('id'));
                index = layer.open({
                    title: "编辑广告",
                    type: 1,
                    area: ['600px', '300px'],
                    content: $('#advert-box'),
                })
            })

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

            //日期选择器
            laydate.render({
                elem: '#begin-time'
            });
            //日期选择器
            laydate.render({
                elem: '#end-time'
            });

            //登录按钮事件
            form.on("submit(save)", function (data) {
                var _btn = data.elem;
                _btn.disabled = true;
                var url = "<?php echo U('saveAdvert');?>"
                var wait_index = layer.msg('请求中，请稍候', {icon: 16, time: false, shade: 0.8});
                $.post(url, data.field, function (res) {
                    if (res.status == 1) {
                        layer.msg(res.info, {icon: 1}, function () {
                            layer.closeAll();
                            refresh()
                        })
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
                        {field: 'id', title: '广告ID', sort: true}
                        , {field: 'title', width: 200, title: '广告标题'}
                        , {field: 'pic', title: '图片', width: 150, templet: '#img'}
                        , {field: 'type_view', title: '广告类型'}
                        , {field: 'begin_time', width: 180, title: '开始时间'}
                        , {field: 'end_time', width: 180, title: '结束时间'}
                        , {field: 'sort', title: '广告排序'}
                        , {fixed: 'right', title: '操作', toolbar: '#table-edit', width: '20%'}
                    ]],
                });
            }
        })
    </script>

</body>
</html>