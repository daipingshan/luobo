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
        
    <div class="layui-tab layui-tab-card">
        <ul class="layui-tab-title">
            <li class="layui-this">企业基本信息</li>
            <li>企业资质证书</li>
            <!-- <li>企业交易记录</li>-->
            <li>企业招聘信息</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <div class="layui-card">
                    <div class="layui-card-header">企业资料</div>
                    <div class="layui-card-body">
                        公司logo：<img src="<?php echo ($info["avatar"]); ?>" alt="" width="200" height="200"><br>
                        公司名称：<?php echo ($info["company_name"]); ?><br>
                        公司简称：<?php echo ($info["short_name"]); ?><br>
                        行业领域：<?php echo ($info["industry_field"]); ?><br>
                        公司规模：<?php echo ($info["company_size"]); ?><br>
                        发展阶段：<?php echo ($info["development_stage"]); ?><br>
                        公司地址：<?php echo ($info["company_address"]); ?>
                    </div>
                </div>
                <div class="layui-card">
                    <div class="layui-card-header">身份信息</div>
                    <div class="layui-card-body">
                        称呼：<?php echo ($info["hr_name"]); ?><br>
                        手机号码：<?php echo ($info["tel"]); ?><br>
                    </div>
                </div>
            </div>
            <div class="layui-tab-item">
                <div class="layui-card">
                    <div class="layui-card-header">资质证书</div>
                    <div class="layui-card-body">
                        <img src="<?php echo ($info["business_lincense"]); ?>" style="width：80%;"><br>
                    </div>
                </div>
            </div>
            <!--<div class="layui-tab-item">-->
            <!--<table class="layui-table">-->
            <!--<colgroup>-->
            <!--<col width="150">-->
            <!--<col width="200">-->
            <!--<col>-->
            <!--</colgroup>-->
            <!--<thead>-->
            <!--<tr>-->
            <!--<th>类型</th>-->
            <!--<th>金额</th>-->
            <!--<th>时间</th>-->
            <!--</tr>-->
            <!--</thead>-->
            <!--<tbody>-->
            <!--<?php if(is_array($flow)): $i = 0; $__LIST__ = $flow;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>-->
            <!--<tr>-->
            <!--<td><?php echo ($vo["source_view"]); ?></td>-->
            <!--<td><?php echo ($vo["money"]); ?></td>-->
            <!--<td><?php echo (date("Y-m-d",$vo["add_time"])); ?></td>-->
            <!--</tr>-->
            <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
            <!--</tbody>-->
            <!--</table>-->
            <!--</div>-->
            <div class="layui-tab-item">
                <div class="layui-tab layui-tab-card">
                    <ul class="layui-tab-title">
                        <li class="layui-this">在招</li>
                        <li>下线</li>
                        <li>违规</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <table class="layui-table">
                                <colgroup>
                                    <col>
                                    <col>
                                    <col>
                                    <col>
                                    <col>
                                    <col>
                                    <col>
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>岗位名称</th>
                                    <th>工作性质</th>
                                    <th>工作经验</th>
                                    <th>学历要求</th>
                                    <th>薪资</th>
                                    <th>发布时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($recruit_data[0])): $i = 0; $__LIST__ = $recruit_data[0];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                        <td><?php echo ($vo["name"]); ?></td>
                                        <td><?php echo ($vo["nature_view"]); ?></td>
                                        <td><?php echo ($vo["experience_view"]); ?></td>
                                        <td><?php echo ($vo["limit_educate_view"]); ?></td>
                                        <td>
                                            <?php if(($vo['salary_min']) == 0 AND ($vo['salary_max'] == 0)): ?>面议
                                                <?php else: ?>
                                                <?php echo ($vo['salary_min']); ?>K~<?php echo ($vo['salary_max']); ?>K<?php endif; ?>
                                        </td>
                                        <td><?php echo (date("Y-m-d",$vo["create_time"])); ?></td>
                                        <td>
                                            <a class="layui-btn layui-btn-xs layui-btn-danger set-fail"
                                               data-id="<?php echo ($vo["id"]); ?>">设置违规</a>
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="layui-tab-item">
                            <table class="layui-table">
                                <colgroup>
                                    <col>
                                    <col>
                                    <col>
                                    <col>
                                    <col>
                                    <col>
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>岗位名称</th>
                                    <th>工作性质</th>
                                    <th>工作经验</th>
                                    <th>学历要求</th>
                                    <th>联系人</th>
                                    <th>发布时间</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($recruit_data[1])): $i = 0; $__LIST__ = $recruit_data[1];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                        <td><?php echo ($vo["name"]); ?></td>
                                        <td><?php echo ($vo["nature_view"]); ?></td>
                                        <td><?php echo ($vo["experience_view"]); ?></td>
                                        <td><?php echo ($vo["limit_educate_view"]); ?></td>
                                        <td><?php echo ($vo["contacts"]); ?><br><?php echo ($vo["contacts_mobile"]); ?></td>
                                        <td><?php echo (date("Y-m-d",$vo["create_time"])); ?></td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="layui-tab-item">
                            <table class="layui-table">
                                <colgroup>
                                    <col>
                                    <col>
                                    <col>
                                    <col>
                                    <col>
                                    <col>
                                    <col>
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>岗位名称</th>
                                    <th>工作性质</th>
                                    <th>工作经验</th>
                                    <th>学历要求</th>
                                    <th>联系人</th>
                                    <th>发布时间</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($recruit_data[2])): $i = 0; $__LIST__ = $recruit_data[2];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                        <td><?php echo ($vo["name"]); ?></td>
                                        <td><?php echo ($vo["nature_view"]); ?></td>
                                        <td><?php echo ($vo["experience_view"]); ?></td>
                                        <td><?php echo ($vo["limit_educate_view"]); ?></td>
                                        <td><?php echo ($vo["contacts"]); ?><br><?php echo ($vo["contacts_mobile"]); ?></td>
                                        <td><?php echo (date("Y-m-d",$vo["create_time"])); ?></td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

    <div id="time-box" class="hide">
        <form class="layui-form mt20 w80">
            <div class="layui-form-item">
                <label class="layui-form-label">过期时间</label>
                <div class="layui-input-block">
                    <input type="text" name="expire_time" id="expire-time" class="layui-input" placeholder="请选择推荐过期时间">
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="id"/>
                    <button class="layui-btn" lay-submit="" lay-filter="save-time">立即提交</button>
                </div>
            </div>
        </form>
    </div>
    <div id="fail-box" class="hide">
        <form class="layui-form mt20 w80">
            <div class="layui-form-item">
                <label class="layui-form-label">违规原因</label>
                <div class="layui-input-block">
                    <textarea type="text" name="content" class="layui-textarea" placeholder="请输入违规原因"></textarea>
                </div>
            </div>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="id" value="0"/>
                    <button class="layui-btn" lay-submit="" lay-filter="save-fail">立即提交</button>
                </div>
            </div>
        </form>
    </div>


    <script>
        $(function () {
            var index;
            //日期选择器
            laydate.render({
                elem: '#expire-time'
            });
            //添加管理员账号
            $('body').on('click', '.set-recommend', function () {
                $('#expire-time').val('');
                $('#time-box input[name=id]').val($(this).data('id'));
                form.render();
                index = layer.open({
                    title: "设置招聘信息推荐",
                    type: 1,
                    area: ['600px', '200px'],
                    content: $('#time-box'),
                })
            })

            //添加管理员账号
            $('body').on('click', '.set-fail', function () {
                $('#fail-box textarea[name=content]').val('');
                $('#fail-box input[name=id]').val($(this).data('id'));
                form.render();
                index = layer.open({
                    title: "设置招聘信息违规",
                    type: 1,
                    area: ['600px', '260px'],
                    content: $('#fail-box'),
                })
            })

            form.on("submit(save-fail)", function (data) {
                var _btn = data.elem;
                _btn.disabled = true;
                var url = "<?php echo U('setRecruitStatus');?>";
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

            form.on("submit(save-time)", function (data) {
                var _btn = data.elem;
                _btn.disabled = true;
                var url = "<?php echo U('setRecruitRecommend');?>";
                var wait_index = layer.msg('请求中，请稍候', {icon: 16, time: false, shade: 0.8});
                $.post(url, data.field, function (res) {
                    if (res.status == 1) {
                        layer.msg(res.info, {icon: 1}, function () {
                                layer.closeAll();
                                location.reload();
                            }
                        )
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