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
        
    <div class="layui-tab">
        <ul class="layui-tab-title">
            <li class="layui-this">网站设置</li>
            <li>企业套餐</li>
            <li>企业规模</li>
            <li>会员套餐</li>
            <li>工作性质</li>
            <li>工作状态</li>
            <li>工作经验</li>
            <li>学历要求</li>
            <li>最高学历</li>
            <li>融资信息</li>
            <li>入职时间</li>
            <li>个人模板</li>
            <li>企业模板</li>
        </ul>
        <div class="layui-tab-content">
            <div class="layui-tab-item layui-show">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">网站域名</label>
                        <div class="layui-input-block w80">
                            <input type="text" name="BASE[domain]" value="<?php echo ($content['BASE']['domain']); ?>"
                                   placeholder="请输入网站域名"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">网站标题</label>
                        <div class="layui-input-block w80">
                            <input type="text" name="BASE[title]" value="<?php echo ($content['BASE']['title']); ?>"
                                   placeholder="请输入网站标题"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">网站关键字</label>
                        <div class="layui-input-block w80">
                            <input type="text" name="BASE[keyword]" value="<?php echo ($content['BASE']['keyword']); ?>"
                                   placeholder="请输入网站关键字"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">网站描述</label>
                        <div class="layui-input-block w80">
                            <input type="text" name="BASE[desc]" value="<?php echo ($content['BASE']['desc']); ?>"
                                   placeholder="请输入网站描述"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">热门搜索</label>
                        <div class="layui-input-block w80">
                            <input type="text" name="BASE[search]" value="<?php echo ($content['BASE']['search']); ?>"
                                   placeholder="请输入热门搜索，多个使用英文逗号隔开"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="submit" type="submit">提交</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tab-item">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-col-xs6">
                        <fieldset class="layui-elem-field">
                            <legend>普通会员</legend>
                            <div class="layui-field-box">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">套餐名称</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="COMPANY[0][name]"
                                               value="<?php echo ($content['COMPANY']['0']['name']); ?>"
                                               placeholder="请输入套餐名称"
                                               class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">套餐价格</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="COMPANY[0][price]"
                                               value="<?php echo ($content['COMPANY']['0']['price']); ?>"
                                               placeholder="请输入套餐价格"
                                               class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">免费刷新次数</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="COMPANY[0][refresh_num]"
                                               value="<?php echo ($content['COMPANY']['0']['refresh_num']); ?>"
                                               placeholder="请输入免费刷新次数"
                                               class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">发布岗位数量</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="COMPANY[0][send_num]"
                                               value="<?php echo ($content['COMPANY']['0']['send_num']); ?>"
                                               placeholder="请输入发布岗位数量"
                                               class="layui-input">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="layui-col-xs6">
                        <fieldset class="layui-elem-field">
                            <legend>黄金会员 - 1888</legend>
                            <div class="layui-field-box">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">套餐名称</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="COMPANY[1888][name]"
                                               value="<?php echo ($content['COMPANY']['1888']['name']); ?>"
                                               placeholder="请输入套餐名称"
                                               class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">套餐价格</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="COMPANY[1888][price]"
                                               value="<?php echo ($content['COMPANY']['1888']['price']); ?>"
                                               placeholder="请输入套餐价格"
                                               class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">免费刷新次数</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="COMPANY[1888][refresh_num]"
                                               value="<?php echo ($content['COMPANY']['1888']['refresh_num']); ?>"
                                               placeholder="请输入免费刷新次数"
                                               class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">发布岗位数量</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="COMPANY[1888][send_num]"
                                               value="<?php echo ($content['COMPANY']['1888']['send_num']); ?>"
                                               placeholder="请输入发布岗位数量"
                                               class="layui-input">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="layui-col-xs6">
                        <fieldset class="layui-elem-field">
                            <legend>铂金会员 - 5888</legend>
                            <div class="layui-field-box">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">套餐名称</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="COMPANY[5888][name]"
                                               value="<?php echo ($content['COMPANY']['5888']['name']); ?>"
                                               placeholder="请输入套餐名称"
                                               class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">套餐价格</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="COMPANY[5888][price]"
                                               value="<?php echo ($content['COMPANY']['5888']['price']); ?>"
                                               placeholder="请输入套餐价格"
                                               class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">免费刷新次数</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="COMPANY[5888][refresh_num]"
                                               value="<?php echo ($content['COMPANY']['5888']['refresh_num']); ?>"
                                               placeholder="请输入免费刷新次数"
                                               class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">发布岗位数量</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="COMPANY[5888][send_num]"
                                               value="<?php echo ($content['COMPANY']['5888']['send_num']); ?>"
                                               placeholder="请输入发布岗位数量"
                                               class="layui-input">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="layui-col-xs6">
                        <fieldset class="layui-elem-field">
                            <legend>钻石会员 - 8888</legend>
                            <div class="layui-field-box">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">套餐名称</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="COMPANY[8888][name]"
                                               value="<?php echo ($content['COMPANY']['8888']['name']); ?>"
                                               placeholder="请输入套餐名称"
                                               class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">套餐价格</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="COMPANY[8888][price]"
                                               value="<?php echo ($content['COMPANY']['8888']['price']); ?>"
                                               placeholder="请输入套餐价格"
                                               class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">免费刷新次数</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="COMPANY[8888][refresh_num]"
                                               value="<?php echo ($content['COMPANY']['8888']['refresh_num']); ?>"
                                               placeholder="请输入免费刷新次数"
                                               class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">发布岗位数量</label>
                                    <div class="layui-input-inline">
                                        <input type="number" name="COMPANY[8888][send_num]"
                                               value="<?php echo ($content['COMPANY']['8888']['send_num']); ?>"
                                               placeholder="请输入发布岗位数量"
                                               class="layui-input">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">下载简历</label>
                        <div class="layui-input-block w80">
                            <input type="text" name="COMPANY[base][down]" value="<?php echo ($content['COMPANY']['base']['down']); ?>"
                                   placeholder="下载简历消耗几斤萝卜【等值人民币：元】"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">刷新招聘</label>
                        <div class="layui-input-block w80">
                            <input type="text" name="COMPANY[base][refresh]"
                                   value="<?php echo ($content['COMPANY']['base']['refresh']); ?>"
                                   placeholder="刷新招聘信息消耗几斤萝卜【等值人民币：元】"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">置顶招聘</label>
                        <div class="layui-input-block w80">
                            <input type="text" name="COMPANY[base][top]" value="<?php echo ($content['COMPANY']['base']['top']); ?>"
                                   placeholder="置顶招聘信息消耗几斤萝卜【等值人民币：元】"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">推荐首页</label>
                        <div class="layui-input-block w80">
                            <input type="text" name="COMPANY[base][index]"
                                   value="<?php echo ($content['COMPANY']['base']['index']); ?>"
                                   placeholder="推荐首页招聘信息消耗几斤萝卜【等值人民币：元】"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">置顶天数</label>
                        <div class="layui-input-inline">
                            <input type="text" name="COMPANY[base][top_day][1]"
                                   value="<?php echo ($content['COMPANY']['base']['top_day'][1]); ?>"
                                   placeholder="置顶天数"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="COMPANY[base][top_day][2]"
                                   value="<?php echo ($content['COMPANY']['base']['top_day'][2]); ?>"
                                   placeholder="置顶天数"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="COMPANY[base][top_day][3]"
                                   value="<?php echo ($content['COMPANY']['base']['top_day'][3]); ?>"
                                   placeholder="置顶天数"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="COMPANY[base][top_day][4]"
                                   value="<?php echo ($content['COMPANY']['base']['top_day'][4]); ?>"
                                   placeholder="置顶天数"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="submit" type="submit">提交</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tab-item">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <input type="text" name="ENTERPRISE_SCALE[1]"
                                   value="<?php echo ($content['ENTERPRISE_SCALE'][1]); ?>"
                                   placeholder="请输入企业规模人数范围"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="ENTERPRISE_SCALE[2]"
                                   value="<?php echo ($content['ENTERPRISE_SCALE'][2]); ?>"
                                   placeholder="请输入企业规模人数范围"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="ENTERPRISE_SCALE[3]"
                                   value="<?php echo ($content['ENTERPRISE_SCALE'][3]); ?>"
                                   placeholder="请输入企业规模人数范围"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="ENTERPRISE_SCALE[4]"
                                   value="<?php echo ($content['ENTERPRISE_SCALE'][4]); ?>"
                                   placeholder="请输入企业规模人数范围"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="submit" type="submit">提交</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tab-item">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-col-xs4">
                        <fieldset class="layui-elem-field">
                            <legend>普通会员</legend>
                            <div class="layui-field-box">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">套餐名称</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="USER[0][name]"
                                               value="<?php echo ($content['USER']['0']['name']); ?>"
                                               placeholder="请输入套餐名称"
                                               class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">套餐价格</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="USER[0][price]"
                                               value="<?php echo ($content['USER']['0']['price']); ?>"
                                               placeholder="请输入套餐价格"
                                               class="layui-input">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="layui-col-xs4">
                        <fieldset class="layui-elem-field">
                            <legend>优享会员 - 9.9</legend>
                            <div class="layui-field-box">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">套餐名称</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="USER[9][name]"
                                               value="<?php echo ($content['USER']['9']['name']); ?>"
                                               placeholder="请输入套餐名称"
                                               class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">套餐价格</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="USER[9][price]"
                                               value="<?php echo ($content['USER']['9']['price']); ?>"
                                               placeholder="请输入套餐价格"
                                               class="layui-input">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="layui-col-xs4">
                        <fieldset class="layui-elem-field">
                            <legend>尊享会员 - 19.9</legend>
                            <div class="layui-field-box">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">套餐名称</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="USER[19][name]"
                                               value="<?php echo ($content['USER']['19']['name']); ?>"
                                               placeholder="请输入套餐名称"
                                               class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">套餐价格</label>
                                    <div class="layui-input-inline">
                                        <input type="text" name="USER[19][price]"
                                               value="<?php echo ($content['USER']['19']['price']); ?>"
                                               placeholder="请输入套餐价格"
                                               class="layui-input">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">注册资料</label>
                        <div class="layui-input-block w80">
                            <input type="text" name="USER[base][register]"
                                   value="<?php echo ($content['USER']['base']['register']); ?>"
                                   placeholder="首次注册，填写完整简历，获取萝卜数【等值人民币：元】"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">分享岗位</label>
                        <div class="layui-input-block w80">
                            <input type="text" name="USER[base][share]" value="<?php echo ($content['USER']['base']['share']); ?>"
                                   placeholder="分享岗位，获取萝卜数【等值人民币：元】"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="submit" type="submit">提交</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tab-item">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <input type="text" name="WORK_NATURE[full_time]"
                                   value="<?php echo ($content['WORK_NATURE']['full_time']); ?>"
                                   placeholder="请输入工作性质"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="WORK_NATURE[part_time]"
                                   value="<?php echo ($content['WORK_NATURE']['part_time']); ?>"
                                   placeholder="请输入工作性质"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="WORK_NATURE[internship]"
                                   value="<?php echo ($content['WORK_NATURE']['internship']); ?>"
                                   placeholder="请输入工作性质"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="submit" type="submit">提交</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tab-item">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <input type="text" name="WORK_TYPE[0]"
                                   value="<?php echo ($content['WORK_TYPE']['0']); ?>"
                                   placeholder="请输入工作状态"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="WORK_TYPE[1]"
                                   value="<?php echo ($content['WORK_TYPE']['1']); ?>"
                                   placeholder="请输入工作状态"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="WORK_TYPE[2]"
                                   value="<?php echo ($content['WORK_TYPE']['2']); ?>"
                                   placeholder="请输入工作状态"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="submit" type="submit">提交</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tab-item">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <input type="text" name="WORK_EXPERIENCE[0]" value="<?php echo ($content['WORK_EXPERIENCE'][0]); ?>"
                                   placeholder="请输入工作经验"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="WORK_EXPERIENCE[1]" value="<?php echo ($content['WORK_EXPERIENCE'][1]); ?>"
                                   placeholder="请输入工作经验"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="WORK_EXPERIENCE[2]" value="<?php echo ($content['WORK_EXPERIENCE'][2]); ?>"
                                   placeholder="请输入工作经验"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="WORK_EXPERIENCE[3]" value="<?php echo ($content['WORK_EXPERIENCE'][3]); ?>"
                                   placeholder="请输入工作经验"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="WORK_EXPERIENCE[4]" value="<?php echo ($content['WORK_EXPERIENCE'][4]); ?>"
                                   placeholder="请输入工作经验"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="WORK_EXPERIENCE[5]" value="<?php echo ($content['WORK_EXPERIENCE'][5]); ?>"
                                   placeholder="请输入工作经验"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="submit" type="submit">提交</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tab-item">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <input type="text" name="EDUCATION[1]" value="<?php echo ($content['EDUCATION'][1]); ?>"
                                   placeholder="请输入学历要求"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="EDUCATION[2]" value="<?php echo ($content['EDUCATION'][2]); ?>"
                                   placeholder="请输入学历要求"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="EDUCATION[3]" value="<?php echo ($content['EDUCATION'][3]); ?>"
                                   placeholder="请输入学历要求"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="EDUCATION[4]" value="<?php echo ($content['EDUCATION'][4]); ?>"
                                   placeholder="请输入学历要求"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="EDUCATION[5]" value="<?php echo ($content['EDUCATION'][5]); ?>"
                                   placeholder="请输入学历要求"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="EDUCATION[6]" value="<?php echo ($content['EDUCATION'][6]); ?>"
                                   placeholder="请输入学历要求"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="EDUCATION[7]" value="<?php echo ($content['EDUCATION'][7]); ?>"
                                   placeholder="请输入学历要求"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="EDUCATION[8]" value="<?php echo ($content['EDUCATION'][8]); ?>"
                                   placeholder="请输入学历要求"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="EDUCATION[9]" value="<?php echo ($content['EDUCATION'][9]); ?>"
                                   placeholder="请输入学历要求"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="submit" type="submit">提交</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tab-item">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <input type="text" name="HIGH_EDUCATION[1]" value="<?php echo ($content['HIGH_EDUCATION'][1]); ?>"
                                   placeholder="请输入最高学历"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="HIGH_EDUCATION[2]" value="<?php echo ($content['HIGH_EDUCATION'][2]); ?>"
                                   placeholder="请输入最高学历"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="HIGH_EDUCATION[3]" value="<?php echo ($content['HIGH_EDUCATION'][3]); ?>"
                                   placeholder="请输入最高学历"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="HIGH_EDUCATION[4]" value="<?php echo ($content['HIGH_EDUCATION'][4]); ?>"
                                   placeholder="请输入最高学历"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="HIGH_EDUCATION[5]" value="<?php echo ($content['HIGH_EDUCATION'][5]); ?>"
                                   placeholder="请输入最高学历"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="HIGH_EDUCATION[6]" value="<?php echo ($content['HIGH_EDUCATION'][6]); ?>"
                                   placeholder="请输入最高学历"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="HIGH_EDUCATION[7]" value="<?php echo ($content['HIGH_EDUCATION'][7]); ?>"
                                   placeholder="请输入最高学历"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="HIGH_EDUCATION[8]" value="<?php echo ($content['HIGH_EDUCATION'][8]); ?>"
                                   placeholder="请输入最高学历"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="submit" type="submit">提交</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tab-item">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <input type="text" name="SCALES[1]" value="<?php echo ($content['SCALES'][1]); ?>"
                                   placeholder="请输入融资信息"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="SCALES[2]" value="<?php echo ($content['SCALES'][2]); ?>"
                                   placeholder="请输入融资信息"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="SCALES[3]" value="<?php echo ($content['SCALES'][3]); ?>"
                                   placeholder="请输入融资信息"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="SCALES[4]" value="<?php echo ($content['SCALES'][4]); ?>"
                                   placeholder="请输入融资信息"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="SCALES[5]" value="<?php echo ($content['SCALES'][5]); ?>"
                                   placeholder="请输入融资信息"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="SCALES[6]" value="<?php echo ($content['SCALES'][6]); ?>"
                                   placeholder="请输入融资信息"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="submit" type="submit">提交</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tab-item">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <div class="layui-input-inline">
                            <input type="text" name="POST_TIME[1]" value="<?php echo ($content['POST_TIME'][1]); ?>"
                                   placeholder="请输入最快入职时间"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="POST_TIME[2]" value="<?php echo ($content['POST_TIME'][2]); ?>"
                                   placeholder="请输入最快入职时间"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="POST_TIME[3]" value="<?php echo ($content['POST_TIME'][3]); ?>"
                                   placeholder="请输入最快入职时间"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="POST_TIME[4]" value="<?php echo ($content['POST_TIME'][4]); ?>"
                                   placeholder="请输入最快入职时间"
                                   class="layui-input">
                        </div>
                        <div class="layui-input-inline">
                            <input type="text" name="POST_TIME[5]" value="<?php echo ($content['POST_TIME'][5]); ?>"
                                   placeholder="请输入最快入职时间"
                                   class="layui-input">
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="submit" type="submit">提交</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tab-item">
                <form class="layui-form" action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">自我简介</label>
                        <div class="layui-input-block w80">
                            <textarea name="USER_DISPLAY[self_display]" class="layui-textarea" placeholder="请输入自我简介模板"><?php echo ($content['USER_DISPLAY']['self_display']); ?></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="submit" type="submit">提交</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="layui-tab-item">
                <form class="layui-form" action="">
                    <div class="layui-form-item">
                        <label class="layui-form-label">岗位职责</label>
                        <div class="layui-input-block w80">
                            <textarea name="COMPANY_DISPLAY[position_display]" class="layui-textarea" rows="8"
                                      placeholder="请输入岗位职责模板"><?php echo ($content['COMPANY_DISPLAY']['position_display']); ?></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">任职要求</label>
                        <div class="layui-input-block w80">
                            <textarea name="COMPANY_DISPLAY[tenure_display]" class="layui-textarea" rows="10"
                                      placeholder="请输入任职要求模板"><?php echo ($content['COMPANY_DISPLAY']['tenure_display']); ?></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">福利待遇</label>
                        <div class="layui-input-block w80">
                            <textarea name="COMPANY_DISPLAY[welfare_display]" class="layui-textarea"
                                      placeholder="请输入福利待遇模板"><?php echo ($content['COMPANY_DISPLAY']['welfare_display']); ?></textarea>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="submit" type="submit">提交</button>
                        </div>
                    </div>
                </form>
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
            form.render();
            form.on('submit(submit)', function (data) {
                var _this = this;
                _this.disabled = true;
                $.post("<?php echo U('save');?>", data.field, function (res) {
                    if (res.status == 1) {
                        layer.msg(res.info, {icon: 1}, function () {
                            location.href = "<?php echo U('index');?>"
                        });
                    } else {
                        _this.disabled = false;
                        layer.msg(res.info, {icon: 2});
                    }
                });
                return false;
            });
        })
    </script>

</body>
</html>