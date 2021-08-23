<?php 

/*注册页面设置存储字段，保存在options表中
$field		组名称
$section	区域id
$field_name	字段名称
$field_id	字段id
$name		字段标题
$func		字段函数
*/
function register_setting_func($field,$section,$field_name,$field_id,$name,$func){
	$option_group = $field;
	$setting_section = $section;
	register_setting($option_group,$field_name);
	add_settings_section($setting_section,'','',$option_group);
	add_settings_field($field_id,$name,$func,$option_group,$setting_section);
}
//空字段处理
function custom_field_isset($field,$args){return isset($field[$args]) ? $field[$args] : '';}
//注册设置字段
function register_smtp_field(){register_setting_func('group_smtp','smtp','custom_setting_smtp','smtpid','邮箱SMTP','custom_setting_smtp_field');}
add_action('admin_init','register_smtp_field','');
//定义字段显示
function custom_setting_smtp_field(){
	$custom_setting = get_option('custom_setting_smtp');
?>
	<form action="options.php" method="post">
		<p><label>发件人邮件：</label>
			<input style="width:230px" type="text" name="custom_setting_smtp[fromname]" value="<?php echo custom_field_isset($custom_setting,'fromname');?>" placeholder="填写发件人的邮件"/>
		</p><br>
		<p>
			<label>SMTP地址：</label>
			<input style="width:230px" type="text" name="custom_setting_smtp[host]" value="<?php echo custom_field_isset($custom_setting,'host');?>" placeholder="STMTP服务器地址"/>
		</p><br>
		<p>
			<label>SMTP端口：</label>
			<input style="width:60px;float:none;margin-left:25px;" type="text" name="custom_setting_smtp[port]" value="<?php echo custom_field_isset($custom_setting,'port');?>"/>
			<label>开启SSL：</label>
			<select name="custom_setting_smtp[ssl]">
				<option value="开启" <?php selected('开启',custom_field_isset($custom_setting,'ssl'));?>>开启</option>
				<option value="关闭" <?php selected('关闭',custom_field_isset($custom_setting,'ssl'));?>>关闭</option>
			</select>
		</p><br>
		<p>
			<label>邮箱账号：</label>
			<input style="width:230px" type="text" name="custom_setting_smtp[username]" value="<?php echo custom_field_isset($custom_setting,'username');?>" placeholder="填写发送邮件的邮箱账号"/>
		</p><br>
		<p>
			<label>邮箱密码：</label>
			<input style="width:230px" type="password" name="custom_setting_smtp[password]" value="<?php echo custom_field_isset($custom_setting,'password');?>" placeholder="填写第三方登录授权码"/>
		</p><br>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class=button button-primary" value="更新设置">
		</p>
	</form>
<?php
}
//smtp设置页面
function table_smtp_page(){
?>
	<style>
	.form-table th {display:none;}
	.form-table td{padding:0 10px;}
	.form-table input{float:right;margin-right:25px;}
	.form-table .submit{text-align:center;}
	.form-table .submit input{float:none;}
	</style>
	<div class="wrap">
		<form action="options.php" method="post">
			<?php $option_group = 'group_smtp';settings_fields($option_group);do_settings_sections($option_group);?>
		</form>
	</div>
<?php
}
//添加仪表盘内容
add_action('wp_dashboard_setup',function(){wp_add_dashboard_widget('smtp_dashboard_widget','表单推送邮箱设置','table_smtp_page');},10,2);
//自定义内容类型
function customer_post_type(){
	$labels = array(
		'name' => '在线申请管理',
		'singular_name' => '在线申请管理',
		'all_items' => '所有申请',
		'edit_item' => '申请人',
		'search_items' => '搜索申请',
		'not_found' => '没有找到申请',
		'not_found_in_trash' => '垃圾桶空空如也',
		'menu_name' => '在线申请管理'
	);
	$args = array(
		'labels' => $labels,
		'menu_position' => 21,
		'supports' => array('title','author'),
		'register_meta_box_cb' => 'customer_post_type_metabox',
		'public' => true
	);
	register_post_type('customer',$args);
}
add_action('init','customer_post_type');
//自定义字段
function customer_post_type_metabox(){add_meta_box('customer_info','申请信息','customer_info_metabox','customer','advanced','high');}
//字段显示
function customer_info_metabox(){
	global $post;
	wp_nonce_field('customer_info_metabox','customer_info_metabox_nonce');
	$value = get_post_meta($post->ID,'_customer_info',true);
?>
	<span>申请类型：</span>
	<input style="width:180px" type="text" name="customer_info[type]" value="<?php echo custom_field_isset($value,'type');?>" placeholder="输入类型"/>
	<span>手机：</span>
	<input style="width:180px" type="text" name="customer_info[number]" value="<?php echo custom_field_isset($value,'number');?>" placeholder="输入手机"/>
	<span>微信：</span>
	<input style="width:180px" type="text" name="customer_info[wechat]" value="<?php echo custom_field_isset($value,'wechat');?>" placeholder="输入微信"/>
	<span id="check_id_hidden" value="1"></span>
	<style>.page-title-action,.view,edit-slug-box{display:none !important;}</style>
<?php
}
//字段保存
function customer_save_metabox($post_id){
	if(!isset($_POST['customer_info_metabox_nonce']) || !wp_verify_nonce($_POST['customer_info_metabox_nonce'],'customer_info_metabox') || !current_user_can('edit_post',$post_id)){return;}
    update_post_meta($post_id,'_customer_info',$_POST['customer_info']);
}
add_action('save_post','customer_save_metabox');
add_action('admin_menu',function(){remove_submenu_page( 'edit.php?post_type=customer','post-new.php?post_type=customer');},10,2);
//邮件内容
function push_email($post_ID){
	$get_post_info = get_post($post_ID);
	$get_post_type = $_POST['type'];
	//后台中手动更新时时
	if($get_post_type == ''){
		$value = get_post_meta($post_ID,'_customer_info',true);
		$get_post_type = $value['type'];
		if($get_post_type == 0){$get_post_type = '代理加盟';}else{$get_post_type = '产品申请';}
		$get_post_number = $value['number'];
		$get_post_wechat = $value['wechat'];
	}
	else{
		if($get_post_type == 0){$get_post_type = '代理加盟';}else{$get_post_type = '产品申请';}
		$get_post_number = $_POST['number'];
		$get_post_wechat = $_POST['wechat'];
	}
	if($get_post_info->post_status == 'publish' && $_POST['original_post_status'] != 'publish'){
		$emailAddrs = "xxxxxxxxx@qq.com";//此处填入收件人的邮箱地址
		$emailTitle = "您有一条来自 [" . get_option("blogname") . "] 网站的表单申请";
		$emailMassage = '<table cellpadding="0" cellspacing="0" class="email-container" align="center" width="550"
    style="font-size: 15px; font-weight: normal; line-height: 22px; text-align: left; border: 1px solid rgb(177, 213, 245); width: 550px;">
    <tbody>
        <tr>
            <td>
                <table cellpadding="0" cellspacing="0" class="padding" width="100%"
                    style="padding-left: 40px; padding-right: 40px; padding-top: 30px; padding-bottom: 35px;">
                    <tbody>
                        <tr class="content">
                            <td>
                                <hr style="height: 1px;border: 0;width: 100%;background: #eee;margin: 15px 0;display: inline-block;">
                                <p>Hi 您有一条来自《'.get_option("blogname").'》网站的表单申请<br>申请人：</p>
                                <p style="background: #eee;padding: 1em;text-indent: 2em;line-height: 30px;">'.$get_post_info->post_title.'</p>
                                <p>申请类型:</p>
                                <p style="background: #eee;padding: 1em;text-indent: 2em;line-height: 30px;">'.$get_post_type.'</p>
                                <p>手机:</p>
                                <p style="background: #eee;padding: 1em;text-indent: 2em;line-height: 30px;">'.$get_post_number.'</p>
                                <p>微信:</p>
                                <p style="background: #eee;padding: 1em;text-indent: 2em;line-height: 30px;">'.$get_post_wechat.'</p>
								<p>时间:</p>
								<p style="background: #eee;padding: 1em;text-indent: 2em;line-height: 30px;">'.$get_post_info->post_date.'</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
<table border="0" cellpadding="0" cellspacing="0" align="center" class="footer"
    style="max-width: 550px; font-family: Lato, \'Lucida Sans\', \'Lucida Grande\', SegoeUI, \'Helvetica Neue\', Helvetica, Arial, sans-serif; font-size: 15px; line-height: 22px; color: #444444; text-align: left; padding: 20px 0; font-weight: normal;">
    <tbody>
        <tr>
            <td align="center"
                style="text-align: center; font-size: 12px; line-height: 18px; color: rgb(163, 163, 163); padding: 5px 0px;">
            </td>
        </tr>
        <tr>
            <td
                style="text-align: center; font-weight: normal; font-size: 12px; line-height: 18px; color: rgb(163, 163, 163); padding: 5px 0px;">
                <p>请不要回复此邮件，它由服务器自动发出。</p>
                <p>© '.date("Y").' <a name="footer_copyright" href="' . home_url() . '"
                        style="color: rgb(43, 136, 217); text-decoration: underline;" target="_blank">' .
                        get_option("blogname") . '</a></p>
            </td>
        </tr>
    </tbody>
</table>
<style>table p{font-size:16px;}</style>';
        $emailHeaders ="Content-Type: text/html; charset=UTF-8";
		wp_mail($emailAddrs,$emailTitle,$emailMassage,$emailHeaders);
	}
}
add_action('publish_customer','push_email',10,2);
//邮件配置
function yjp_mail_smtp($phpmailer) {
	$custom_setting = get_option('custom_setting_smtp');
	$phpmailer->From = $custom_setting['fromname']; //发件人地址
	$phpmailer->FromName = "WordPress"; //发件人昵称
	$phpmailer->Host = $custom_setting['host']; //SMTP服务器地址
	$phpmailer->Port = $custom_setting['port']; //SMTP邮件发送端口
	if($custom_setting['ssl']){$phpmailer->SMTPSecure = 'ssl';}
	else{$phpmailer->SMTPSecure = '';}
	$phpmailer->Username = $custom_setting['username']; //邮箱帐号
	$phpmailer->Password = $custom_setting['password']; //邮箱密码
	$phpmailer->IsSMTP();
	$phpmailer->SMTPAuth = true; //启用SMTPAuth服务
}
add_action('phpmailer_init', 'yjp_mail_smtp');

//为WordPress后台的文章、分类等显示自定义列、ID
function ssid_column() {
	$cols['title'] = '申请人';
	$cols['type'] = '申请类型';
	$cols['number'] = '手机';
	$cols['wechat'] = '微信';
	$cols['date'] = '申请日期';
	$cols['author'] = '';
	$cols['ssid'] = 'ID';
	return $cols;
}
function ssid_value($column_name, $id) {
	$value = get_post_meta($id,'_customer_info',true);
	if($column_name == 'ssid')	echo $id;
	if($column_name == 'type')	echo $value['type'];
	if($column_name == 'number') echo $value['number'];
	if($column_name == 'wechat') echo $value['wechat'];
}
function ssid_css() {
?>
<style type="text/css">#ssid{width:50px;}</style>
<script>
window.onload = function(){
    set = document.getElementsByName('post_type');
	set1 = document.getElementsByClassName('page-title-action');
	set2 = document.getElementsByClassName('view');
	check_id_hidden = document.getElementById('check_id_hidden');
	if(!check_id_hidden){
		if(set[0].defaultValue == 'customer'){
			for(var i=0;i<set1.length;i++){set1[i].style.display = 'none';}
			for(var i=0;i<set2.length;i++){set2[i].style.display = 'none';}
		}
	}else{
		document.getElementById('edit-slug-box').style.display = 'none';
		document.getElementById('post-preview').style.display = 'none';
	}
}
</script>
<?php
}
function ssid_add() {
add_action('admin_head', 'ssid_css');
add_filter('manage_customer_posts_columns', 'ssid_column');
add_action('manage_customer_posts_custom_column', 'ssid_value', 10, 2);
}
?>
