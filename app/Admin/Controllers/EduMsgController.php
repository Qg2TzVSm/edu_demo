<?php


namespace App\Admin\Controllers;


use App\Http\Controllers\Controller;
use App\Models\EduStudent;
use App\Models\EduTeacher;
use App\Models\LineUser;
use App\Tasks\Line\SendTextMessageToLineUser;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Form;

class EduMsgController extends Controller
{
    public function index(Content $content)
    {
        $form = $this->genForm();
        $script = $this->genScript();
        Admin::script($script);
        return $content
            ->title('消息发送')
            ->description('Description...')
            ->row($form);
    }

    protected function genForm()
    {
        $form = new Form();

        $form->action('example');

        $form->select('user_type','用户类型')->options([1=>'老师',0=>'学生',2=>'line用户'])
            ->load('user_id', 'get-users-via-type');
        $form->select('user_id','用户');
        $form->text('msg', '消息内容');
        $form->action(admin_url('edu-msg/send'));
        return $form;
    }

    public function users()
    {
        switch (request('q')){
            case 0:
                return EduStudent::query()->get(['id', 'name as text']);
            case 1:
                return EduTeacher::query()->get(['id', 'name as text']);
            case 2:
                return LineUser::query()->get(['id', 'openid as text']);
        }
    }

    public function send()
    {
        $user = LineUser::query()->findOrFail(request('user_id'));
        $msg = request('msg');
        $task = SendTextMessageToLineUser::make()->handle($user->openid, $msg);
        if ($task !== true){
            admin_error('发送失败', $task);
        }
        admin_success('发送成功.');
    }

    public function genScript()
    {
        $script = <<<EOF
var wsServer = 'wss://edu-chat-server.herokuapp.com/ws';
    var websocket = new WebSocket(wsServer);
      websocket.onopen = function () {
        console.log("Connected to WebSocket server.");
        websocket.send(JSON.stringify({
          type:0,
          user_type:3,
          id:0,
          from:0,
        }));
      };
$('button[type=submit]').on('click',function(e){
    let user_type = $(".user_type").val();
    let user_id = $(".user_id").val();
    let msg = $(".msg").val();
    if(user_type != 2){
        e.preventDefault();
        websocket.send(JSON.stringify({
          type:3,
          user_type: parseInt(user_type),
          id:user_id,
          to_user_type: parseInt(user_type),
          from:0,
          msg:msg,
        }));
        swal("发送成功","","success");
    }
    var form = $(this).parents('form');
});
EOF;
        return $script;
    }
}