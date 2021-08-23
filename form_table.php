<?php 
/*
Template Name: 申请表单
*/
?>
<!DOCTYPE html>
<html>

<head>
    <title>表单 | 麦客CRM</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
    <meta name="msapplication-tap-highlight" content="no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="robots" content="noindex" />
    <meta name="robots" content="noindex, noarchive" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
    <meta name="renderer" content="webkit" />
</head>

<body>
    <style>
        * {
            margin: 0 auto;
        }
        
        body {
            width: 100%;
            height: 100%;
            padding: 20px 0;
        }
        
		#bg_img {
            position: fixed;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
        }
        
        #bg_img {
            background: url(<?php echo get_template_directory_uri();?>/form-table/158_Bg.png) no-repeat;
            background-size: cover;
        }
        
        #table_img img {
            width:100%;
        }
        
        #table {
            position: relative;
            background: white;
            max-width: 750px;
            width: auto;
            z-index: 1;
        }
        
        table {
            width: 100%;
            padding: 30px 30px;
            padding-top: 12px;
        }
        
        tr {
            padding: 14px 0;
            display: block;
        }
        
        .tenpercent {
            color: red;
            padding-right: 45px;
            min-width: 45px;
        }
        
        .ninepercent {
            width: 80%;
            font-size: 15px;
        }
        
        .checked {
            padding: 2px 0;
        }
        
        .checked_span {
            padding-bottom: 10px;
        }
        
        .checked_span:hover {
            color: #F8BA00;
        }
        
        .checked_span input {
            color: #F8BA00;
        }
        
        label input {
            margin: 2px 8px 0 0;
        }
        
        .bold {
            font-weight: bold;
            color: black;
            font-size: 16px;
        }
        
        label,
        label input {
            cursor: pointer
        }
        
		.text input {
            padding: 8px;
            border: 1px solid #d3d3d3;
            width: 100%;
        }
		
        .text input:focus {
            outline: 2px solid #F8BA00;
        }
        
        #submit-div,
        #submit_go {
            text-align: center;
            margin: 25px;
            margin-top: 0;
			font-size: 18px;
        }
        
        #submit-div input,.hidden input {
            min-width: 120px;
            max-width: 45%;
            height: 45px;
            color: white;
            background: rgb(52, 152, 219);
            border-radius: 5px;
        }
		
		.hidden{
			display:none;
			padding:60px 30px;
			text-align:center;
		}
		.hidden p{
			padding:30px;
			font-size:18px;
		}
		
		.repush,.success{
			height:157px;
			margin-bottom:16px;
		}
		
		.repush{
			background: url(<?php echo get_template_directory_uri();?>/form-table/err.png) no-repeat center;
		}
		
		.success{
			background: url(<?php echo get_template_directory_uri();?>/form-table/success.png) no-repeat center;
		}
    </style>
    <div id="bg_img"></div>
    <div id="table">
		<div id="table_img"><img src="<?php echo get_template_directory_uri();?>/form-table/table_img.png"/></div>
        <form action="" method="post" id="form_table_s">
            <table>
                <tbody>
                    <tr>
                        <td class="tenpercent" valign="top"><span class="bold">选择</span> *</td>
                        <td class="ninepercent checked">
                            <div class="checked_span"><label for="radio1"><input id="radio1" type="radio" name="type" value="0" checked="checked" /><span>代理加盟</span></label></div>
                            <div class="checked_span"><label for="radio2"><input id="radio2" type="radio" name="type" value="1" /><span>产品申请</span></label></div>
                        </td>
                    </tr>
                    <tr style="padding-top:0;">
                        <td class="tenpercent"><span class="bold">姓名</span> *</td>
                        <td class="ninepercent text"><input id="username" type="text" name="username" required value="">
							<span id="check_name" style="color:red;position:absolute;display:none;">请输入姓名</span>
						</td>
                    </tr>
                    <tr>
                        <td class="tenpercent"><span class="bold">手机</span> *</td>
                        <td class="ninepercent text"><input id="number" type="text" name="number" required value="" />
                            <span id="check_number" style="color:red;position:absolute;display:none;">请输入正确的手机号码</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="tenpercent"><span class="bold">微信</span></td>
                        <td class="ninepercent text"><input type="text" name="wechat"/></td>
                    </tr>
                </tbody>
            </table>
            <div id="submit-div">
                <input type="submit" onclick="return check_submit()" id="submit_go" value="确认提交">
            </div>
        </form>
		<div class="hidden succe">
			<div class="success"></div>
			<p>表单提交成功！客服将会在工作时间内与您取得联系，请保持电话畅通！</p>
		</div>
		<div class="hidden error">
			<div class="repush"></div>
			<p>请勿重复提交！客服将会在工作时间内与您取得联系，请保持电话畅通！</p>
			<input type="submit" onclick="go_home();" id="submit_go" value="返回首页">
		</div>
    </div>
    <script>
		var status = 0;
		//栏目检验
        function checkPhone() {
			var num = document.getElementById('number');
			var c_num = document.getElementById('check_number');
			var c_num_right = checknumber(num.value);
			status = checkName();
            if (!num.value) {
				c_num.style.display = '';alert("请输入正确的信息！");
                return false;
            } else {
                if (c_num_right) {
					c_num.style.display = 'none';
					if(status == 1){return true;}
					else{alert("请输入正确的信息！");return false;}
                } 
				else {
					c_num.style.display = '';alert("请输入正确的信息！");
                    return false;
				}
            }
        }
		//检验手机号
		function checknumber(numvalue){
			let reg = /^(1[3-9][0-9])\d{8}$/;
			return reg.test(numvalue);
		}
		//检验姓名栏
		function checkName(){
			var name = document.getElementById('username');
			var c_name = document.getElementById('check_name');
			if (!name.value) {
				c_name.style.display = '';
				status = 0;
            }
			else{
				c_name.style.display = 'none';
				status = 1;
			}
			return status;
		}
		//提交事件
        function check_submit() {
            var submit = document.getElementById('submit_go');
            if (!checkPhone()) {return false;}
        }
    </script>
<?php
$username = isset($_POST['username'])?$_POST['username']:'';
$type = isset($_POST['type'])?$_POST['type']:'';	
$number = isset($_POST['number'])?$_POST['number']:'';
$wechat = isset($_POST['wechat'])?$_POST['wechat']:'';
$type = htmlspecialchars($type);
$username = htmlspecialchars($username);
$number = htmlspecialchars($number);
$wechat = htmlspecialchars($wechat);

$ip = get_ip();
$ip_check_status = true;
//获取ip
function get_ip(){
	if(!empty($_SERVER['HTTP_CLIENT_IP'])){$ip = $_SERVER['HTTP_CLIENT_IP'];}
	elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];}
	else{$ip = $_SERVER['REMOTE_ADDR'];}
	return $ip;
}

if($type == 0){$type = '代理加盟';}else{$type = '产品申请';}
$customer = array(
	'username' => $username,
	'type' => $type,
	'number' => $number,
	'wechat' => $wechat
);
//ip查重
$args = array(
	'author' => '0',
	'post_type' => 'customer',
	'order' => 'ASC'
);
$today = date('Y-m-d');
$the_query = new WP_Query($args);
while ($the_query -> have_posts()){
	$the_query->the_post();
	$ip_check = get_the_excerpt();
	$post_date_check = get_the_date('Y-m-d');
	if($ip_check == $ip){
		if($post_date_check == $today){
		$ip_check_status = false;break;
		}
	}
}
wp_reset_postdata();
//数据数组
$my_post = array(
	'post_title' => $username,
	'post_excerpt' => $ip,
	'post_status' => 'publish',
	'post_type' => 'customer'
);
//检查是否允许申请
if($ip_check_status){
	if($username == '' && $number == ''){}
	else{
		$new_post = wp_insert_post($my_post);
		update_post_meta($new_post,'_customer_info',$customer);
		?><style>.succe{display:block;}form{display:none;}</style>
		<?php
	}
}
else{?>
<style>.error{display:block;}form{display:none;}</style>
<script>function go_home(){window.location.href="/";}</script>
	<?php
	$ip_check_status = true;
	}
?>
</body>

</html>
