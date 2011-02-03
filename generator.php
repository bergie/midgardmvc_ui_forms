<?php
class midgardmvc_ui_forms_generator
{
    public static function get_by_guid($guid)
    {
        try
        {
            $db_form = new midgardmvc_ui_forms_form($guid);
        }
        catch (midgard_error_exception $e)
        {
            return false;
        }
        return self::get_by_form($db_form);
    }

    public static function get_by_form(midgardmvc_ui_forms_form $db_form)
    {
        $form = midgardmvc_helper_forms::create($db_form->guid);
        $list_of_fields = self::list_fields($db_form);
        foreach ($list_of_fields as $db_field)
        {
            self::add_field_to_form($form, $db_field);
        }
        return $form;
    }

    public static function has_object_forms(midgard_object $object)
    {
        $storage = new midgard_query_storage('midgardmvc_ui_forms_form');
        $q = new midgard_query_select($storage);
        $q->set_constraint
        (
            new midgard_query_constraint
            (
                new midgard_query_property('parent', $storage),
                '=',
                new midgard_query_value($object->guid)
            )
        );
        $q->execute();
        if ($q->get_results_count() == 0)
        {
            return false;
        }
        return true;
    }

    public static function list_for_object(midgard_object $object)
    {
        $list_of_forms = array();
        $storage = new midgard_query_storage('midgardmvc_ui_forms_form');
        $q = new midgard_query_select($storage);
        $q->set_constraint
        (
            new midgard_query_constraint
            (
                new midgard_query_property('parent', $storage),
                '=',
                new midgard_query_value($object->guid)
            )
        );
        $q->execute();
        $list_of_db_forms = $q->list_objects();
        foreach ($list_of_db_forms as $db_form)
        {
            $list_of_forms[] = self::get_by_form($db_form);
        }
        return $list_of_forms;
    }

    public static function list_fields(midgardmvc_ui_forms_form $db_form)
    {
        // Fetch fields from the database
        $storage = new midgard_query_storage('midgardmvc_ui_forms_form_field');
        $q = new midgard_query_select($storage);
        $q->set_constraint
        (
            new midgard_query_constraint
            (
                new midgard_query_property('form', $storage),
                '=',
                new midgard_query_value($db_form->id)
            )
        );

        $q->add_order
        (
          new midgard_query_property('metadata.score', $storage),
          SORT_DESC
        );

        $q->execute();
        return $q->list_objects();
    }

    public static function add_field_to_form(midgardmvc_helper_forms_group $form, midgardmvc_ui_forms_form_field $db_field)
    {
        $field = $form->add_field($db_field->guid, $db_field->field, $db_field->required);
        $widget = $field->set_widget($db_field->widget);
        $widget->set_label($db_field->title);
    }
}
