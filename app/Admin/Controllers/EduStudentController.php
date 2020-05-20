<?php

namespace App\Admin\Controllers;

use App\Models\EduStudent;
use App\Models\EduTeacher;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class EduStudentController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Student Manage';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EduStudent());
        $is_admin = in_array(1, Admin::user()->roles()->pluck('id')->toArray());
        if (!$is_admin){
            // 老师只能看到自己学校的学生
            $teacher = EduTeacher::where('email', Admin::user()->username)->first();
            $school_id = $this->handleSchool($teacher);
            $grid->model()->where('school_id', $school_id);
            // 学校管理员可以创建学生
            if (EduTeacher::isMasterOfSchool($teacher, $school_id)){
                $grid->disableCreateButton(false);
                $grid->disableActions(false);
                $grid->actions(function (Grid\Displayers\Actions $actions) {
                    $actions->disableView();
                    $actions->disableEdit();
                    $actions->disableDelete();
                    $actions->append("<div class='mb-5'><a class='btn btn-xs action-btn btn-success grid-send-msg' data-id='{$actions->getKey()}'><i class='fa fa-check'></i> 发通知</a></div>");
                });
                $msg_script = $this->createNotifyButton($teacher->id);
            }
            $schools = optional($teacher->schools())->pluck('name','edu_schools.id')->toArray();
            $selects = "";
            foreach ($schools as $k => $v){
                $now = session('handling_school');
                if ($k == $now){
                    $selects .= "<option value=\"{$k}\" selected>{$v}</option>";
                }else{
                    $selects .= "<option value=\"{$k}\">{$v}</option>";
                }
            }
            $grid->disableTools(false);
            $sc = <<<EOT
<div style="display: inline-block">
    <select id="chose_school" class="form-control">
    {$selects}
    </select>
</div>
EOT;
            $grid->tools(function (Grid\Tools $tools)use($sc){
                $tools->append($sc);
            });
            $script = <<<EOT
$('#chose_school').change(function(){
var school_id = $("#chose_school").val();
$.ajax({
method: 'post',
url: '/admin/edu-student/chose/' + school_id,
data: {
    _token:LA.token,
},
success: function (data) {
    $.pjax.reload('#pjax-container');
}
});
});
EOT;
            $script .= $msg_script ?? '';
            Admin::script($script);
        }

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('avatar', __('Avatar'))->image("",100,100);
        $grid->column('email', __('Email'));
        $grid->column('school.name', __('School Name'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $grid->disableColumnSelector();

        return $grid;
    }

    protected function handleSchool($teacher)
    {
        $school_id = session('handling_school');
        if (empty($school_id)){
            $school_ids = optional($teacher->schools())->pluck('edu_schools.id')->toArray();
            if (!empty($school_ids)){
                $school_id = $school_ids[0];
                session(['handling_school' => $school_ids[0]]);
            }
        }
        return $school_id;
    }

    public function chose($id)
    {
        session(['handling_school' => $id]);
        return response()->json(['status'=>1,'message'=>'操作成功！']);
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(EduStudent::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('avatar', __('Avatar'));
        $show->field('email', __('Email'));
        $show->field('password', __('Password'));
        $show->field('school_id', __('School id'));
        $show->field('role_id', __('Role id'));
        $show->field('remember_token', __('Remember token'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new EduStudent());

        $form->select('school_id', __('School'))->options(function(){
            $name = Admin::user()->username;
            $teacher = EduTeacher::where('email', $name)->first();
            return $teacher->schools()->where('verified', 1)->get()->pluck('name', 'id');
        });
        $form->text('name', __('Name'));
        $form->email('email', __('Email'));
        $form->password('password', __('Password'));
        $form->saving(function (Form $form){
            $form->password = bcrypt($form->password);
        });

        return $form;
    }

    protected function createNotifyButton($teacher_id)
    {
        $script = <<<EOF
var wsServer = 'wss://edu-chat-server.herokuapp.com/ws';
    var websocket = new WebSocket(wsServer);
      websocket.onopen = function () {
        console.log("Connected to WebSocket server.");
        websocket.send(JSON.stringify({
          type:0,
          user_type:2,
          id:0,
          from:{$teacher_id},
        }));
      };

      websocket.onclose = function () {
        console.log("Disconnected");
      };

      websocket.onmessage = function (evt) {
        console.log('Retrieved data from server: ' + evt.data);
      };

      websocket.onerror = function (evt) {
        console.log('Error occured: ' + evt.data);
      };
$('.grid-send-msg').unbind('click').click(function() {
    var id = $(this).data('id');
swal({text: 'Send Msg:', input: 'text'})
.then((value) => {
  websocket.send(JSON.stringify({
          type:2,
          user_type: 1,
          id: parseInt(id),
          to_user_type: 0,
          from:{$teacher_id},
          msg:value.value,
        }));
  swal(`发送成功`);
});
});
EOF;
    return $script;
    }
}
