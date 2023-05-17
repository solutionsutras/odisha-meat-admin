<?php

namespace App\Admin\Controllers;

use App\Models\Notification;
use App\Models\UserType;
use App\Models\Customer;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class NotificationController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Notifications';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Notification());

        $grid->column('id', __('Id'));
        $grid->column('notification_type', __('Notification Type'))->display(function($user_types){
            $user_types = UserType::where('id',$user_types)->value('user');
            return $user_types;
        });
        $grid->column('destination_id', __('Destination '))->display(function($customers){
            if($customers == 0){
            return "NILL";
            }else{
                $customers = Customer::where('id',$customers)->value('customer_name');
            return $customers;
            }
        });
        $grid->column('title', __('Title'));
        //$grid->column('description', __('Description'));
        $grid->column('image', __('Image'))->image();
   
        $grid->disableExport();
        if(env('MODE') == 'DEMO'){
            $grid->disableCreateButton();
            $grid->disableActions();
        }else{
            $grid->actions(function ($actions) {
                $actions->disableView();
            });
        }
        $grid->filter(function ($filter) {
            //Get All status
         $user_types = UserType::pluck('user', 'id');
         $customers = Customer::pluck('customer_name', 'id');

         $filter->like('title', __('Title'));
         $filter->equal('notification_type', __('Notification Type'))->select($user_types);
         $filter->equal('destination_id', __('Destination '))->select($customers);
         
        });



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
        $show = new Show(Notification::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('notification_type', __('Notification type'));
        $show->field('destination_id', __('Destination id'));
        $show->field('title', __('Title'));
        $show->field('description', __('Description'));
        $show->field('image', __('Image'));
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
        $form = new Form(new Notification());
        $user_types = UserType::pluck('user', 'id');
        $customers = Customer::pluck('customer_name','id');

        $form->select('notification_type', __('Notification Type'))->options($user_types)->rules(function ($form) {
            return 'required';
        });

        $form->select('destination_id', __('Destination '))->options($customers);
        

        $form->text('title', __('Title')) ->rules(function ($form) {
            return 'required|max:500';
        });
        $form->textarea('description', __('Description'))->rules('required');
        $form->image('image', __('Image'))->uniqueName()->move('notifications')->rules('required');
        
        $form->saving(function ($form) {
           if(!$form->destination_id){
              $form->destination_id = 0;
           }
        });
        $form->tools(function (Form\Tools $tools) {
            $tools->disableDelete(); 
            $tools->disableView();
        });
        $form->footer(function ($footer) {
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
        });



        return $form;
    }
}
