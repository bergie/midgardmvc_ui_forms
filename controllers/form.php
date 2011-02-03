<?php
class midgardmvc_ui_forms_controllers_form extends midgardmvc_core_controllers_baseclasses_crud
{
    private function load_parent(array $args)
    {
        try 
        {
            $this->parent = midgard_object_class::get_object_by_guid($args['parent']);
        }
        catch (midgard_error_exception $e)
        {
            throw new midgardmvc_exception_notfound("Object not found: " . $e->getMessage());
        }
    }

    public function load_object(array $args)
    {
        midgardmvc_core::get_instance()->authorization->require_user();

        try 
        {
            $this->object = new midgardmvc_ui_forms_form($args['form']);
        }
        catch (midgard_error_exception $e)
        {
            throw new midgardmvc_exception_notfound("Form not found: " . $e->getMessage());
        }
    }
    
    public function prepare_new_object(array $args)
    {
        midgardmvc_core::get_instance()->authorization->require_user();
        $this->load_parent($args);
        $this->object = new midgardmvc_ui_forms_form();
        $this->object->parent = $this->parent->guid;
    }

    public function load_form()
    {
        $this->form = midgardmvc_helper_forms::create('midgardmvc_ui_forms_form');

        $field = $this->form->add_field('title', 'text');
        $field->set_value($this->object->title);
        $widget = $field->set_widget('text');
        $widget->set_label('Form title');
    }

    public function get_read(array $args)
    {
        parent::get_read($args);

        // Load a readonly preview of the form
        $this->data['form_preview'] = midgardmvc_ui_forms_generator::get_by_object($this->object);
        $this->data['form_preview']->set_readonly(true);

        $this->data['field_create_url'] = midgardmvc_core::get_instance()->dispatcher->generate_url
        (
            'field_create', array
            (
                'form' => $this->object->guid,
            ),
            $this->request
        );
    }

    public function get_url_read()
    {
        return midgardmvc_core::get_instance()->dispatcher->generate_url
        (
            'form_read', array
            (
                'form' => $this->object->guid
            ),
            $this->request
        );
    }

    public function get_url_update()
    {
        return midgardmvc_core::get_instance()->dispatcher->generate_url
        (
            'form_update', array
            (
                'form' => $this->object->guid
            ),
            $this->request
        );
    }

}
