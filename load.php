<?php
class midgardmvc_ui_forms_load
{
    public static function load_form(midgardmvc_helper_forms_group $form, midgardmvc_ui_forms_form_instance $instance)
    {
        $items = $form->items;

        foreach ($items as $key => $item)
        {
            if ($item instanceof midgardmvc_helper_forms_group)
            {
                // TODO: Add support for subforms
                continue;
            }
            $field_instance = self::get_instance_for_field($item, $instance);
            if (is_null($field_instance))
            {
                continue;
            }

            self::load_field($item, $field_instance);
        }

        return $form;
    }


    public static function get_instance_property_for_field(midgardmvc_helper_forms_field $field)
    {
        switch (get_class($field))
        {
            case 'midgardmvc_helper_forms_field_text':
                return 'stringvalue';
            case 'midgardmvc_helper_forms_field_boolean':
                return 'booleanvalue';
        }
        return null;
    }

    public static function get_instance_for_field(midgardmvc_helper_forms_field $field, midgardmvc_ui_forms_form_instance $instance)
    {
        $instance_property = self::get_instance_property_for_field($field);

        if (is_null($instance_property))
        {
            return null;
        }

        // Fetch fields from the database
        $storage = new midgard_query_storage('midgardmvc_ui_forms_form_instance_field');
        $q = new midgard_query_select($storage);
        $q->toggle_readonly(false);

        $qg = new midgard_query_constraint_group('AND');
        $qg->add_constraint
        (
            new midgard_query_constraint
            (
                new midgard_query_property('form'),
                '=',
                new midgard_query_value($instance->id)
            )
        );
        $qg->add_constraint
        (
            new midgard_query_constraint
            (
                new midgard_query_property('field'),
                '=',
                new midgard_query_value($field->get_name())
            )
        );
        $q->set_constraint($qg);

        $q->execute();
        $list_of_field_instances = $q->list_objects();
        if (empty($list_of_field_instances))
        {
            $field_instance = new midgardmvc_ui_forms_form_instance_field();
            $field_instance->form = $instance->id;
            $field_instance->field = $field->get_name();
            return $field_instance;
        }

        return $list_of_field_instances[0];
    }

    public static function load_field(midgardmvc_helper_forms_field $field, $field_instance)
    {
        $instance_property = self::get_instance_property_for_field($field);
        if (is_null($instance_property))
        {
            return true;
        }

        $field->set_value($field_instance->$instance_property);
    }
}
