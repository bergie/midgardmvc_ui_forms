<?php
class midgardmvc_ui_forms_workflow_condition_instance extends ezcWorkflowConditionType
{
    /**
     * Evaluates this condition and returns true if $value is a GUID of a form instance or false if not.
     *
     * @param  mixed $value
     * @return boolean true when the condition holds, false otherwise.
     * @ignore
     */
    public function evaluate($value)
    {
        if (!mgd_is_guid($value))
        {
            return false;
        }

        try
        {
            $form = new midgardmvc_ui_forms_form_instance($value);
        }
        catch (midgard_error_exception $e)
        {
            return false;
        }

        return true;
    }

    /**
     * Returns a textual representation of this condition.
     *
     * @return string
     * @ignore
     */
    public function __toString()
    {
        return 'is form instance';
    }
}
