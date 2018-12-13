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
        
    <form class="layui-form search" action="" name="search">
        <div class="layui-row">
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md3">
                <input type="text" name="name" value="<?php echo I('get.name');?>" placeholder="请输入门店名称" class="layui-input">
            </div>
            <div class="layui-col-xs6 layui-col-sm6 layui-col-md3">
                <input type="text" name="mobile" value="<?php echo I('get.mobile');?>" placeholder="请输入手机号码" class="layui-input">
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
                <label class="layui-form-label">原因</label>
                <div class="layui-input-block">
                    <textarea type="text" name="content" class="layui-textarea" placeholder="请输入审核失败、封禁原因"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="id" value="0" id="shop-id"/>
                    <input type="hidden" name="status" id="shop-status"/>
                    <button class="layui-btn" lay-submit="" lay-filter="save-admin">立即提交</button>
                </div>
            </div>
        </form>
    </div>


    <script type="text/html" id="img">
        {{#  if(d.id_img){ }}
        <div class="open-img-layer cursor"><img layer-src={{d.id_img}} src="{{d.id_img}}" alt="{{d.name}}"
                                                width="120"></div>
        {{#  } else { }}
        <p style="color: #FF5722">尚未上传图片</p>
        {{#  } }}
    </script>
    <script type="text/html" id="type">
        {{#  if(d.shop_type == 1 ){ }}
        <button class="layui-btn layui-btn-xs" type="button">门店</button>
        {{#  } else { }}
        <button class="layui-btn layui-btn-danger layui-btn-xs" type="button">代理</button>
        {{#  } }}
    </script>
    <script type="text/html" id="status">
        {{#  if(d.status == 0){ }}
        未审核
        {{#  } else if(d.status == 1) { }}
        审核成功
        {{#  } else if(d.status == 2) { }}
        审核失败
        {{#  } else { }}
        已封禁
        {{#  } }}
    </script>
    <script type="text/html" id="table-edit">
        <a class="layui-btn layui-btn-xs"
           href="<?php echo U('userList');?>?user_id={{d.user_id}}" target="_blank">查看粉丝</a>
        <a class="layui-btn layui-btn-xs"
           href="<?php echo U('shopOrder');?>?shop_id={{d.id}}" target="_blank">查看业绩</a>
        {{#  if(d.status == 0){ }}
        <a class="layui-btn layui-btn-xs layui-btn-normal confirm"
           data-url="<?php echo U('setShopStatus');?>" data-id="{{d.id}}" data-status="1">审核通过</a>
        <a class="layui-btn layui-btn-xs layui-btn-danger confirm"
           data-url="<?php echo U('setShopStatus');?>" data-id="{{d.id}}" data-status="2">审核失败</a>
        {{#  } else if(d.status == 1) { }}
        <a class="layui-btn layui-btn-xs layui-btn-danger confirm"
           data-url="<?php echo U('setShopStatus');?>" data-id="{{d.id}}" data-status="3" data-title="">立即封禁</a>
        {{#  } else if(d.status == 2) { }}
        <a class="layui-btn layui-btn-danger layui-btn-xs" readonly="true">已审核，审核失败</a>
        {{#  } else { }}
        <a class="layui-btn layui-btn-warm layui-btn-xs" readonly="true">已封禁</a>
        <a class="layui-btn layui-btn-xs confirm"
           data-url="<?php echo U('setShopStatus');?>" data-id="{{d.id}}" data-status="1">立即解封</a>
        {{#  } }}
    </script>
    <script>
        $(function () {
            form.render();
            var index;
            var tableObj;
            var url = "<?php echo U('index');?>";
            refresh();
            $('form.search').on('click', 'button.search', function () {
                refresh();
            });

            $('body').on('mouseover', '.open-img-layer', function () {
                var _this = $(this);
                layer.photos({
                    photos: _this
                });
            })

            function refresh() {
                var param = $('form.search').serialize();
                var get_url = url + '?' + param;
                getData(get_url);
            }

            //添加管理员账号
            $('body').on('click', '.confirm', function () {
                var post_url = $(this).data('url');
                var id = $(this).data('id');
                var status = $(this).data('status');
                var text = $(this).text();
                if (status == 1) {
                    layer.confirm('你确定要将此门店' + text + '吗？', function () {
                        $.post(post_url, {id: id, status: status}, function (res) {
                            if (res.status == 1) {
                                layer.msg(res.info, {icon: 1}, function () {
                                    tableObj.reload();
                                })
                            } else {
                                layer.msg(res.info, {icon: 2});
                            }
                        })
                    })
                } else {
                    $('#shop-id').val(id);
                    $('#shop-status').val(status);
                    $('textarea[name=content]').val('');
                    index = layer.open({
                        title: '门店状态设置',
                        type: 1,
                        area: ['600px', '260px'],
                        content: $('#box'),
                    })
                }
            })

            form.on("submit(save-admin)", function (data) {
                var _btn = data.elem;
                _btn.disabled = true;
                var post_url = "<?php echo U('setShopStatus');?>";
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
                    , cellMinWidth: 80
                    , cols: [[
                        {field: 'id', title: '门店ID', sort: true}
                        , {field: 'name', title: '名称'}
                        , {field: 'mobile', title: '联系电话', width: 120}
                        , {field: 'id_code', title: '身份证号码', width: 180}
                        , {field: 'id_img', title: '身份证照片', toolbar: '#img'}
                        , {field: 'city_info', title: '地址', width: 220}
                        , {field: 'shop_type', title: '身份', toolbar: '#type'}
                        , {field: 'status', title: '门店状态', width: 100, toolbar: '#status'}
                         , {field: 'admin_name', title: '审核人', width: 100}
                        , {field: 'created_at', title: '创建时间', width: 170}
                        , {fixed: 'right', title: '操作', toolbar: '#table-edit', width: '25%'}
                    ]],
                });
            }
        })
    </script>

</body>
</html>