<?php

require('PHPMailer/PHPMailer.php');
require('PHPMailer/SMTP.php');
require('PHPMailer/Exception.php');
 
function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); // array to string
}; 

if(isset($_POST['type']) && $_POST['type']=='login')
{
	//echo $_POST['type'];
	if(isset($_POST['app_no']) && isset($_POST['password']))
	{
		
		$choice=$_POST['choice'];
		$app_no = $_POST['app_no'];
		$password = $_POST['password'];
		if($choice=='student'){
			
			if(!empty($app_no) && !empty($password))
			{
				$password_hash = $password;
				//$password_hash = md5($password);
				$query = "SELECT app_no FROM students WHERE app_no='".mysqli_real_escape_string($mysql_connect, $app_no)."' AND password='".mysqli_real_escape_string($mysql_connect, $password_hash)."'";
				
				if($query_run = mysqli_query($mysql_connect, $query))
				{
					$query_run = mysqli_query($mysql_connect, $query);
					
					$query_num_rows = mysqli_num_rows($query_run);
					if($query_num_rows==0)
					{
						echo 'Invalid id/password.';
					}
					else if($query_num_rows==1)
					{
						$query_row = mysqli_fetch_assoc($query_run);
						$app_no = $query_row['app_no'];
						$_SESSION['app_no'] = $app_no;
						$_SESSION['login_type']="student";
						header('Location: index.php');
					}
				}
				else
				{
					echo 'error running query';
				}
			}

			else
			{
				echo 'You must enter a app_no and password.';
			}
		}
		else if($choice=="college"){
			if(!empty($app_no) && !empty($password))
			{
				$password_hash = $password;
				//$password_hash = md5($password);
				//
				$query = "SELECT clg_id FROM colleges WHERE clg_id='".mysqli_real_escape_string($mysql_connect, $app_no)."' AND clg_password='".mysqli_real_escape_string($mysql_connect, $password_hash)."'";
				
				if($query_run = mysqli_query($mysql_connect, $query))
				{
					$query_run = mysqli_query($mysql_connect, $query);
					
					$query_num_rows = mysqli_num_rows($query_run);
					if($query_num_rows==0)
					{
						echo 'Invalid id/password.';
					}
					else if($query_num_rows==1)
					{
						$query_row = mysqli_fetch_assoc($query_run);
						$clg_id = $query_row['clg_id'];
						$_SESSION['app_no'] = $clg_id;
						$_SESSION['login_type']="college";
						
						header('Location: index.php');
					}
				}
				else
				{
					echo 'error running query';
				}
			}
			else
			{
				echo 'You must enter a app_no and password.';
			}
		}
		else if($choice="Admin"){

			if(!empty($app_no) && !empty($password))
			{
				$password_hash = $password;
				//$password_hash = md5($password);
				$query = "SELECT user_name FROM admin WHERE user_name='".$_POST["app_no"]."' and password='".$password_hash."'";
				
				if($query_run = mysqli_query($mysql_connect, $query))
				{
					$query_num_rows = mysqli_num_rows($query_run);
					if($query_num_rows==0)
					{
						echo 'Invalid id/password.';
					}
					else if($query_num_rows==1)
					{
						$query_row = mysqli_fetch_assoc($query_run);
						$app_no = $query_row['user_name'];
						$_SESSION['app_no'] = $_POST["app_no"];
						$_SESSION['login_type']="admin";
						header('Location: index.php');
					}
				}
				else
				{
					echo 'error running query...';
				}
			}
			else
			{
				echo 'enter username and password';
			}

		}
	}
}
else if(isset($_POST['type']) && $_POST['type']='request')
{
	$app_no = $_POST['app_no_req'];
	$query = "select email,password from students where app_no='".$app_no."'";
	if($result = mysqli_query($mysql_connect, $query))
	{
		if(mysqli_num_rows($result) == 0)
		{
			echo 'invalid application no.<br>';
		}
		else
		{
			$result=mysqli_fetch_assoc($result);
			if($result['password']!='')
			{
				echo 'password already requested.';
			}
			else
			{
				$pass=randomPassword();
				$mail = new PHPMailer\PHPMailer\PHPMailer();;                
				try {
					
					$mail->SMTPDebug = 2;          
					$mail->isSMTP();               
					$mail->Host = 'smtp.gmail.com';
					$mail->SMTPAuth = true;                             
					$mail->Username = 'rishabh.kalakoti@gmail.com';     
					$mail->Password = 'Percy@3538';                     
					$mail->SMTPSecure = 'tls';                          
					$mail->Port = 587;                                  

					$mail->setFrom('rishabh.kalakoti@gmail.com', 'Mailer');
					$mail->addAddress($result['email']);     
					
					$mail->isHTML(true);                                
					$mail->Subject = 'Password request';
					$mail->Body    = "current password:".$pass."<br>You can change the password later.";
					$mail->AltBody = "current password:".$pass." You can change the password later.";

					$mail->send();
					echo 'Your details have been sent. Thank You.';
					header('Location: index.php?q=1');
				} catch (Exception $e) {
					echo  'Details could not be sent.';
				}
			}
		}
	}
	else
	{
		echo 'error running query<br>';
	}
}

?>

<form action="<?php echo $current_file; ?>" method="POST">
	<input type="hidden" id="type" value="login" name="type">
	<select id='choice' name="choice" onchange='changeVal()'>
		<option  value="college">college</option>
		<option  value="student" selected>student</option>
		<option  value="Admin">Admin</option>

	</select><br><br>



	<script type='text/javascript'>
		function changeVal()
		{
			var xd=document.getElementById('xd');
			var str=document.getElementById('choice').value;
			var val='';
			if(str=='Admin')
				val='Username: ';
			else
				val='Login id: ';
			xd.innerHTML = val;
			//alert('XD');
		}
		changeVal();
	</script>
	<div id='xd' style="display:inline-block;">
	Login Id: 
	</div>
	<input type="text" name="app_no"><br/>
	Password: <input type="password" name="password"><br/>
	<input type="submit" value="Log In">

	<br><br>
	<?php
		if(isset($_GET['q']))
			echo 'Password details has been sent to registered email.<br>';
	?>
	Request password.<Br>
	Application No: <input type="text" name="app_no_req" maxlength="20"><br/>
	<input type="submit" value="Request" onclick="document.getElementById('type').value='request';">
</form>