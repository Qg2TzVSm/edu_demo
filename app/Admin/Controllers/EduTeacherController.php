<?php

namespace App\Admin\Controllers;

use App\Models\EduTeacher;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class EduTeacherController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Teacher Manage';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new EduTeacher());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('avatar', __('Avatar'));
        $grid->column('email', __('Email'));
        $grid->column('verified', __('Verified'))->display(function ($v){return $v ? 'Verified' : 'Un Verified';});
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(EduTeacher::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('avatar', __('Avatar'));
        $show->field('email', __('Email'));
        $show->field('password', __('Password'));
        $show->field('verified', __('Verified'));
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
        $form = new Form(new EduTeacher());

        $form->text('name', __('Name'));
        $form->image('avatar', __('Avatar'));
        $form->email('email', __('Email'));
        $form->password('password', __('Password'));
        $form->switch('verified', __('Verified'));
        $form->text('remember_token', __('Remember token'));

        return $form;
    }
}
