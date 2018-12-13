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
            <a class="layui-btn layui-btn-danger add" href="javascript:;" data-pid="0"><i
                    class="layui-icon">
                &#xe654;</i>添加一级分类
            </a>
        </div>
    </form>
    <div class="layui-tab layui-tab-card" lay-filter="test">
        <ul class="layui-tab-title one-li">
            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?><li
                <?php if($key == 0): ?>class="layui-this"<?php endif; ?>
                >
                <button class="layui-btn layui-btn-xs"><i
                        class="layui-icon save" data-pid="<?php echo ($row["parent_id"]); ?>"
                        data-id="<?php echo ($row["id"]); ?>" data-name="<?php echo ($row["name"]); ?>" data-sort="<?php echo ($row["sort"]); ?>">&#xe642;</i>
                </button>

                <button class="layui-btn layui-btn-xs layui-btn-danger"><i
                        class="layui-icon del" data-url="<?php echo U('delType',array('id'=>$row['id']));?>">&#xe640;</i>
                </button>
                <?php echo ($row["name"]); ?>
                <button class="layui-btn layui-btn-xs layui-btn-normal"><i class="layui-icon add"
                                                                           data-pid="<?php echo ($row["id"]); ?>">&#xe654;</i>
                </button>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <div class="layui-tab-content">
            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><div
                <?php if($key == 0): ?>class="layui-tab-item layui-show"
                    <?php else: ?>
                    class="layui-tab-item"<?php endif; ?>
                >
                <table class="layui-table">
                    <colgroup>
                        <col>
                        <col>
                    </colgroup>
                    <tbody>
                    <?php if(is_array($val["son_data"])): $i = 0; $__LIST__ = $val["son_data"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
                            <td><?php echo ($v["name"]); ?></td>
                            <td><a href="javascript:;" data-id="<?php echo ($v["id"]); ?>" data-pid="<?php echo ($v["parent_id"]); ?>"
                                   data-name="<?php echo ($v["name"]); ?>" data-sort="<?php echo ($v["sort"]); ?>"
                                   class="layui-btn layui-btn-xs save">编辑</a>
                                <a href="javascript:;" data-url="<?php echo U('delType',array('id'=>$v['id']));?>"
                                   class="layui-btn layui-btn-xs layui-btn-danger del">删除</a></td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>

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

    <div id="box" class="hide">
        <form class="layui-form mt20 w80">
            <div class="layui-form-item">
                <label class="layui-form-label">分类名称</label>
                <div class="layui-input-block">
                    <input type="text" name="name" class="layui-input" placeholder="请输入分类名称">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">职位排序</label>
                <div class="layui-input-block">
                    <input type="text" name="sort" value="255" class="layui-input" placeholder="请输入职位排序">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="parent_id" value="0"/>
                    <input type="hidden" name="id" value="0" id="city-id"/>
                    <button class="layui-btn" lay-submit="" lay-filter="save-admin">立即提交</button>
                </div>
            </div>
        </form>
    </div>


    <script>
        $(function () {
            var index;
            //添加管理员账号
            $('body').on('click', '.add', function () {
                $('input[name=parent_id]').val($(this).data('pid'));
                $('input[name=name]').val('');
                $('input[name=sort]').val(255);
                $('#city-id').val(0);
                index = layer.open({
                    title: "添加文章分类",
                    type: 1,
                    area: ['600px', '260px'],
                    content: $('#box'),
                })
            })

            $('body').on('click', '.save', function () {
                $('input[name=parent_id]').val($(this).data('pid'));
                $('input[name=name]').val($(this).data('name'));
                $('input[name=sort]').val($(this).data('sort'));
                $('#city-id').val($(this).data('id'));
                index = layer.open({
                    title: "编辑文章分类",
                    type: 1,
                    area: ['600px', '260px'],
                    content: $('#box'),
                })
            })

            form.on("submit(save-admin)", function (data) {
                var _btn = data.elem;
                _btn.disabled = true;
                var url = "<?php echo U('saveType');?>";
                var wait_index = layer.msg('请求中，请稍候', {icon: 16, time: false, shade: 0.8});
                $.post(url, data.field, function (res) {
                    if (res.status == 1) {
                        layer.msg(res.info, {icon: 1}, function () {
                            layer.closeAll();
                            location.reload();
                        })
                    } else {
                        _btn.disabled = false;
                        layer.msg(res.info, {icon: 2});
                        layer.close(wait_index);
                    }
                });
                return false;
            });


            $('body').on('click', '.del', function () {
                var del_url = $(this).data('url');
                layer.confirm('你确定要删除此文章分类信息吗？', function () {
                    $.get(del_url, {}, function (res) {
                        if (res.status == 1) {
                            layer.msg(res.info, {icon: 1}, function () {
                                location.reload();
                            })
                        } else {
                            layer.msg(res.info, {icon: 2});
                        }
                    })
                })
            })
        })
    </script>

</body>
</html>