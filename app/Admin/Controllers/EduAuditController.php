<?php

namespace App\Admin\Controllers;

use App\Models\EduSchool;
use App\Models\EduTeacher;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class EduAuditController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'School Audit';
    protected $script;

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EduSchool());
        $grid->model()->where('verified', 0);

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('creator', __('Creator Name'))->display(function (){
            return optional(optional($this->teachers())->where('is_creator', 1)->first())->name;
        });
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $grid->disableActions(false);
        $this->script = <<<EOT
$('.grid-row-pass').unbind('click').click(function() {
    var id = $(this).data('id');

    swal({
        title: "确认通过？",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "确认",
        showLoaderOnConfirm: true,
        cancelButtonText: "取消",
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    method: 'post',
                    url: '/admin/edu-school/audit/' + id,
                    data: {
                        _token:LA.token,
                        audit: 1,
                    },
                    success: function (data) {
                        $.pjax.reload('#pjax-container');

                        resolve(data);
                    }
                });
            });
        }
    }).then(function(result) {
        var data = result.value;
        if (typeof data === 'object') {
            if (data.status) {
                swal(data.message, '', 'success');
            } else {
                swal(data.message, '', 'error');
            }
        }
    });
});
$('.grid-row-refusal').unbind('click').click(function() {
    var id = $(this).data('id');


    swal({
        title: "确认不通过？",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "确认",
        showLoaderOnConfirm: true,
        cancelButtonText: "取消",
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    method: 'post',
                    url: '/admin/edu-school/audit/' + id,
                    data: {
                        _token:LA.token,
                        audit: 2,
                    },
                    success: function (data) {
                        $.pjax.reload('#pjax-container');

                        resolve(data);
                    }
                });
            });
        }
    }).then(function(result) {
        var data = result.value;
        if (typeof data === 'object') {
            if (data.status) {
                swal(data.message, '', 'success');
            } else {
                swal(data.message, '', 'error');
            }
        }
    });
});
EOT;
        Admin::script($this->script);
        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->disableView();
            $actions->disableEdit();
            $actions->disableDelete();
            $actions->append("<div class='mb-5'><a class='btn btn-xs action-btn btn-success grid-row-pass' data-id='{$actions->getKey()}'><i class='fa fa-check'></i> 通过</a></div>");
            $actions->append("<div class='mb-5'><a class='btn btn-xs action-btn btn-danger grid-row-refusal' data-id='{$actions->getKey()}'><i class='fa fa-ban'></i> 驳回</a></div>");
        });
        return $grid;
    }

    public function audit($id)
    {
        $audit = request('audit');
        if (empty($audit) || !in_array(intval($audit), [1,2])){
            return response()->json(['status'=>0,'message'=>'操作失败！']);
        }
        $school = EduSchool::findOrFail($id);
        $school->update(['verified' => intval($audit)]);
        if (intval($audit) === 1){
            $creator = optional($school->teachers())->where('is_creator', 1)->first();
            EduTeacher::createBackendAccountForTeacher($creator, 1);
        }
        return response()->json(['status'=>1,'message'=>'操作成功！']);
    }
}
