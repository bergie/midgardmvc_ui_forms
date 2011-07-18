<?php
class midgardmvc_ui_forms_store
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
            $field_instance = midgardmvc_ui_forms_load::get_instance_for_field($item, $instance);
            if (is_null($field_instance))
            {
                continue;
            }

            midgardmvc_ui_forms_load::load_field($item, $field_instance);
        }
    }

    public static function store_form(midgardmvc_helper_forms_group $form, midgardmvc_ui_forms_form_instance $instance)
    {
        $transaction = new midgard_transaction();
        $transaction->begin();

        // Go through form items and fill the object
        $items = $form->items;
        foreach ($items as $key => $item)
        {
            if ($item instanceof midgardmvc_helper_forms_group)
            {
                // TODO: Add support for subforms
                continue;
            }
            $field_instance = midgardmvc_ui_forms_load::get_instance_for_field($item, $instance);
            if (is_null($field_instance))
            {
                continue;
            }

            if (! self::store_field($item, $field_instance))
            {
                $transaction->rollback();
                return false;
            }
        }
        $transaction->commit();
        return true;
    }

    public static function store_field(midgardmvc_helper_forms_field $field, $field_instance)
    {
        $instance_property = midgardmvc_ui_forms_load::get_instance_property_for_field($field);
        if (is_null($instance_property))
        {
            return true;
        }

        if ($field->get_value() == $field_instance->$instance_property)
        {
            return true;
        }

        $field_instance->$instance_property = $field->get_value();

        if (! $field_instance->guid)
        {
            return $field_instance->create();
        }

        return $field_instance->update();
    }

    public static function get_instance_property_for_field(midgardmvc_helper_forms_field $field)
    {
        midgardmvc_ui_forms_load::get_instance_property_for_field($field);
    }

    public static function get_instance_for_field(midgardmvc_helper_forms_field $field, midgardmvc_ui_forms_form_instance $instance)
    {
        midgardmvc_ui_forms_load::get_instance_for_field($field, $instance);
    }

    public static function load_field(midgardmvc_helper_forms_field $field, $field_instance)
    {
        midgardmvc_ui_forms_load::load_field($field, $field_instance);
    }
}
