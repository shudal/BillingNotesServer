<?php
namespace app\index\controller;

use think\Controller;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class Regis extends Controller {
    public function add() {
        if (! request()->isPost()) {
            return json(['status' => 0, 'msgname' => 'invalid_request_way']);
        }

        $data = input('post.');

        try {
            if ( model('User')->get(['email' => $data['email']])) {
                return json(['status' => 0, 'msgname' => 'email_existed']);
            }

            if ( model('User')->get(['username' => $data['username']])) {
                return json(['status' => 0, 'msgname' => 'username_existed']);
            }




            $newUser = [];
            $newUser['nickname'] = $data['nickname'];
            $newUser['username'] = $data['username'];
            $newUser['password'] = $data['password'];
            $newUser['email']    = $data['email'];
            $newUser['create_time'] = strtotime('now');
          

            $mail = new PHPMailer();
            $mail->Charset = "UTF-8";
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = "smtp.qq.com";
            $mail->SMTPAuth = true;
            $mail->Username = "shudal@foxmail.com";
            $mail->Password = "bzbdhjzaaqytfcfi";
            $mail->SMTPSecure = 'ssl';
            $mail->Priority =  3;
            $mail->Port = 465;

            $mail->setFrom("shudal@foxmail.com");
            $mail->FromName = 'Billing Notes';
            $mail->addAddress($newUser['email'], 'Billing Notes');
            $mail->addReplyTo("shudal@foxmail.com", 'Information');
            $mail->isHTML(true);
            $mail->Subject = 'Confirm for Billing Notes ';
            $mail->Body = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>请点击<a href='http://". $_SERVER['HTTP_HOST'] ."/regis_check?username=". $newUser['username'] . "'>此处</a>来验证邮箱，此链接十分钟后过期。"  . "Click <a href='http://" . $_SERVER['HTTP_HOST']  . "/regis_check?username=". $newUser['username']  . "'>here</a> to confirm your email. The url will expire in 10 minutes.";

            $result = $mail->send();
            if ($result) {
                model('User')->save($newUser);
            } else {

                return json(['status' => 0, 'msgname' => 'unknown_error'.$result]);
            }

            return json(['status' => 1, 'msgname' => 'regis_success']);

        } catch (\Exception $e) {
            return json(['status' => 0, 'msgname' => 'unknown_error:'.$e->getMessage()]);
        }
    }

    public function check() {
        if (request()->isGet()) {

            try {
                $username = input('get.username');

                $user  = model('User')->get(['username' => $username]);

                $now_time = strtotime('now');

                if ($now_time - $user->create_time < 600) {
                    if ($user->status == 1) {
                        return '已经验证过了。It has benn confirmed.';
                    }

                    $user->status = 1;
                    $user->save();

                    $newUserM = [];
                    $newUserM['user_id'] = $user->id;
                    model('UserMeta')->save($newUserM);
                    return ('用户 ' . $user->nickname . ' 注册成功!');
                } else {

                    return '该链接已经失效';
                }
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }
    }
}
