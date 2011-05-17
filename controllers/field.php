<?php
class midgardmvc_ui_forms_controllers_field extends midgardmvc_core_controllers_baseclasses_crud
{
    public function __construct(midgardmvc_core_request $request)
    {
        parent::__construct($request);

        $this->mvc = midgardmvc_core::get_instance();

        $this->mvc->i18n->set_translation_domain('midgardmvc_ui_forms');

        $default_language = $this->mvc->configuration->default_language;

        if (! isset($default_language))
        {
            $default_language = 'en_US';
        }

        $this->mvc->i18n->set_language($default_language, false);
    }

    private function load_parent(array $args)
    {
        try
        {
            $this->parent = new midgardmvc_ui_forms_form($args['form']);
        }
        catch (midgard_error_exception $e)
        {
            throw new midgardmvc_exception_notfound("Form not found: " . $e->getMessage());
        }
    }

    public function load_object(array $args)
    {
        midgardmvc_core::get_instance()->authorization->require_user();

        $this->load_parent($args);

        try
        {
            $this->object = new midgardmvc_ui_forms_form_field($args['field']);
        }
        catch (midgard_error_exception $e)
        {
            throw new midgardmvc_exception_notfound("Form field not found: " . $e->getMessage());
        }

        if ($this->object->form != $this->parent->id)
        {
            throw new midgardmvc_exception_notfound("Field {$this->object->guid} does not belong to form {$this->parent->guid}");
        }
    }

    public function prepare_new_object(array $args)
    {
        midgardmvc_core::get_instance()->authorization->require_user();
        $this->load_parent($args);
        $this->object = new midgardmvc_ui_forms_form_field();
        $this->object->form = $this->parent->id;
    }

    public function load_form()
    {
        $this->form = midgardmvc_helper_forms::create('midgardmvc_ui_forms_field');

        $field = $this->form->add_field('title', 'text');
        $field->set_value($this->object->title);
        $widget = $field->set_widget('text');
        $widget->set_label('Label');


        $field = $this->form->add_field('fieldwidget', 'text');
        $list_of_fieldoptions = array();
        $list_of_fieldwidgets = midgardmvc_core::get_instance()->configuration->form_fieldwidgets;
        foreach ($list_of_fieldwidgets as $name => $fieldwidget)
        {
            $list_of_fieldoptions[] = array
            (
                'value' => $name,
                'description' => $fieldwidget['description'],
            );

            if (   $this->object->field == $fieldwidget['field']
                && $this->object->widget == $fieldwidget['widget'])
            {
                $field->set_value($name);
            }
        }
        $widget = $field->set_widget('selectoption');
        $widget->set_label('Field');
        $widget->set_options($list_of_fieldoptions);

        $field = $this->form->add_field('required', 'boolean');
        $field->set_value($this->object->required);
        $widget = $field->set_widget('checkbox');
        $widget->set_label('Required');
    }

    public function process_form()
    {
        $this->form->process_post();

        $this->object->title = $this->form->title->get_value();

        $fieldwidget = $this->form->fieldwidget->get_value();
        if (isset(midgardmvc_core::get_instance()->configuration->form_fieldwidgets[$fieldwidget]))
        {
            $this->object->field = midgardmvc_core::get_instance()->configuration->form_fieldwidgets[$fieldwidget]['field'];
            $this->object->widget = midgardmvc_core::get_instance()->configuration->form_fieldwidgets[$fieldwidget]['widget'];
        }

        $this->object->required = $this->form->required->get_value();
    }

    public function get_url_read()
    {
        // Reading always goes back to form
        return midgardmvc_core::get_instance()->dispatcher->generate_url
        (
            'form_read', array
            (
                'form' => $this->parent->guid
            ),
            $this->request
        );
    }

    public function get_url_update()
    {
        return midgardmvc_core::get_instance()->dispatcher->generate_url
        (
            'field_update', array
            (
                'form' => $this->parent->guid,
                'field' => $this->object->guid,
            ),
            $this->request
        );
    }

   /**
     * Prepares stuff for creation
     */
    public function get_create(array $args)
    {
        parent::get_create($args);
        $this->data['title'] = midgardmvc_core::get_instance()->i18n->get('title_create_field');
    }

    /**
     * Prepares stuff for creation
     */
    public function get_update(array $args)
    {
        parent::get_update($args);
        $this->data['title'] = midgardmvc_core::get_instance()->i18n->get('title_update_field');
    }
}
