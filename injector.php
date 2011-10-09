<?php
class midgardmvc_ui_forms_injector
{
    var $mvc = null;

    public function __construct()
    {
        $this->mvc = midgardmvc_core::get_instance();

        $this->mvc->i18n->set_translation_domain('midgardmvc_ui_forms');

        $default_language = $this->mvc->configuration->default_language;

        if (! isset($default_language))
        {
            $default_language = 'en_US';
        }

        $this->mvc->i18n->set_language($default_language, false);
    }

    /**
     * Some template hack
     */
    public function inject_template(midgardmvc_core_request $request)
    {
        // Add the CSS and JS files needed by Packages
        $this->add_head_elements();
    }

    /**
     * Adds js and css files to head
     */
    private function add_head_elements()
    {
        $this->mvc->head->enable_jquery();
        $this->mvc->head->enable_jquery_ui();

        $this->mvc->head->add_jsfile(MIDGARDMVC_STATIC_URL . '/midgardmvc_ui_forms/js/jquery.ui.datetime.min.js');
        $this->mvc->head->add_jsfile(MIDGARDMVC_STATIC_URL . '/midgardmvc_ui_forms/js/init.js');

        $this->mvc->head->add_link
        (
            array
            (
                'rel' => 'stylesheet',
                'type' => 'text/css',
                'href' => MIDGARDMVC_STATIC_URL . '/midgardmvc_ui_forms/css/jquery.ui.datetime.css'
            )
        );
    }
}
?>
