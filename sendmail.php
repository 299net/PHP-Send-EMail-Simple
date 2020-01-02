<?php
/*
php邮件发送函数sendmail(收件地址,标题,内容)说明：
收件地址可以是多个邮箱，用分号(;)隔开。
使用前先设置你的发信服务器、邮箱、密码、编码。
在需要发信的文件中require这个文件，调用sendmail函数即可。
返回值：成功发送的邮件数。
调试：把本文件内echo前面的注释去掉即可。
整理编写：http://bjtime.cn
*/


function sendmail($to, $subject,$body)
{
$smtp_host="你的smtp服务器";
$user="你的发信邮箱";
$pass="你的邮箱密码";
$charset="utf-8"; //可改成你的网页编码

$header= "MIME-Version:1.0\r\n";
$header.= "Content-Type:text/html; charset=".$charset."\r\n";
$header.= "To: ".$to."\r\n";
$header.= "From: ".$user."\r\n";
$header.= "Subject: "."=?".strtoupper($charset)."?B?".base64_encode($subject)."?="."\r\n";
$header.= "Date: ".date("r")."\r\n";
$header.= "X-Mailer:By PHP(".phpversion().")\r\n";
list($msec, $sec) = explode(" ", microtime());
$header.= "Message-ID: ".date("YmdHis", $sec).".".$msec."\r\n";

$sent = 0;
$to_arr = explode(";", $to);
foreach ($to_arr as $rcpt_to)
{
$sock = @fsockopen($smtp_host, 25, $errno, $errstr, 10);
  
if ($sock && smtp_ok($sock)) {
if (smtp_cmd($sock,"HELO localhost"))
if (smtp_cmd($sock,"AUTH LOGIN ".base64_encode($user))) 
if (smtp_cmd($sock,base64_encode($pass)))
if (smtp_cmd($sock,"MAIL FROM: ".$user)) 
if (smtp_cmd($sock,"RCPT TO: ".$to))
if (smtp_cmd($sock,"DATA")) 
if (smtp_data($sock,$header,$body)) 
if (smtp_cmd($sock,"QUIT")) 
$sent++;

fclose($sock);
}
}
return $sent;
}


function smtp_cmd($sock,$cmd)
{
fputs($sock, $cmd."\r\n");
//echo htmlspecialchars($cmd);
return smtp_ok($sock);
}


function smtp_data($sock,$header,$body)
{
fputs($sock,$header."\r\n");    
fputs($sock,$body."\r\n.\r\n");
return smtp_ok($sock);
}


function smtp_ok($sock)
{
$response = fgets($sock, 512);
if (!preg_match("/^[23]/", $response))
{
fputs($sock, "QUIT\r\n");
fgets($sock, 512);
return FALSE;
}
//echo("<br>".$response."<br><br>");
return TRUE;
}


?>
