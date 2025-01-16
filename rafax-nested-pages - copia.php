<?php
/*
Plugin Name: Rafax Nested Pages Widget
Description: Muestra las páginas anidadas de la página actual en un widget.
Version: 1.2
Author: rafax
*/

class Rafax_Nested_Pages extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'rafax_nested_pages', // ID del widget
            __('Rafax Nested Pages', 'rafax_nested_pages'), // Nombre
            ['description' => __('Muestra las páginas anidadas de la página actual.', 'rafax_pages')] // Descripción
        );
        add_action('wp_enqueue_scripts', [$this, 'rafax_pages_widget_enqueue_styles']);
    }

    function rafax_pages_widget_enqueue_styles()
    {
        // Solo cargar estilos en el frontend
        if (!is_admin()) {
            wp_enqueue_style(
                'rafax-pages-widget-style', // Handle del estilo
                plugin_dir_url(__FILE__) . 'assets/style.css', // Ruta al archivo CSS
                [], // Dependencias
                '1.0', // Versión
                'all' // Tipo de medios
            );

            wp_enqueue_script(
                'rafax-pages-widget-script', // Handle del script
                plugin_dir_url(__FILE__) . 'assets/script.js', // Ruta al archivo JS
                [], // Dependencias (si usa jQuery, añadir 'jquery')
                '1.0', // Versión
                true // Cargar en el footer
            );

        }
    }

    public function widget($args, $instance)
    {
        global $post;

        if (!$post || $post->post_type !== 'page') {
            return; // Solo se ejecuta en páginas
        }

        // Obtener todas las páginas
        $all_pages = get_pages([
            'sort_column' => 'menu_order',
            'sort_order' => 'ASC',
        ]);

        // Depuración: Mostrar el contenido de $all_pages
        if (empty($all_pages)) {
            echo 'No se encontraron páginas.';
            return;
        }

        // Obtener el título configurado desde el widget
        $title = !empty($instance['title']) ? $instance['title'] : __('Páginas relacionadas', 'rafax_pages');
        $expand_children = isset($instance['expand_children']) ? (bool) $instance['expand_children'] : false;
        //error_log('Expand children value: ' . ($expand_children ? 'true' : 'false'));
        echo $args['before_widget'];
        echo $args['before_title'] . esc_html($title) . $args['after_title'];

        // Mostrar las páginas anidadas
        echo '<ul>';
        $this->display_nested_pages($all_pages, $post->ID, $expand_children);
        echo '</ul>';

        echo $args['after_widget'];
    }

    private function display_nested_pages($all_pages, $parent_id = 0, $expand_children = false)
    {
        $pages_to_display = [];
        foreach ($all_pages as $page) {
            if ($page->post_parent == $parent_id) {
                $pages_to_display[] = $page;
            }
        }

        if (empty($pages_to_display)) {
            return;
        }

        echo '<ul>';
        foreach ($pages_to_display as $page) {
            // Verificar si la página tiene hijos
            $has_children = false;
            foreach ($all_pages as $child_page) {
                if ($child_page->post_parent == $page->ID) {
                    $has_children = true;
                    break;
                }
            }
            //error_log('Page ID: ' . $page->ID . ' | Has children: ' . ($has_children ? 'true' : 'false'));

            // Ajustar la lógica de expandir o contraer las páginas
            $is_hidden_class = ($expand_children || !$has_children) ? '' : ' hidden';
            //error_log('Page ID: ' . $page->ID . ' | Expand children: ' . ($expand_children ? 'true' : 'false') . ' | Hidden class applied: ' . ($is_hidden_class ? 'yes' : 'no'));

            echo '<li class="nested-page">';
            if ($has_children) {
                echo '<span class="toggle-children"><svg id="Lager_1" style="enable-background:new -265 388.9 64 64; width:20px;height:20px" version="1.1" viewBox="-265 388.9 64 64" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M-239.1,407l14.8,15.1c0.3,0.4,0.3,1.2,0,1.6l-14.8,15.1c-0.3,0.4-0.8-0.1-0.8-0.8v-30.2   C-239.8,407.1-239.4,406.7-239.1,407z"/></g></svg></span>'; // Icono desplegable
            }
            echo '<a href="' . esc_url(get_permalink($page->ID)) . '">' . esc_html($page->post_title) . '</a>';

            if ($has_children) {
                echo '<div class="children' . $is_hidden_class . '">';
                $this->display_nested_pages($all_pages, $page->ID, $expand_children);
                echo '</div>';
            }

            echo '</li>';
        }
        echo '</ul>';
    }




    // Formulario para opciones del widget (backend)

    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : __('Páginas relacionadas', 'rafax_pages');
        $expand_children = !empty($instance['expand_children']) ? (bool) $instance['expand_children'] : false;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Título:', 'rafax_pages'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($expand_children); ?>
                id="<?php echo $this->get_field_id('expand_children'); ?>"
                name="<?php echo $this->get_field_name('expand_children'); ?>">
            <label
                for="<?php echo $this->get_field_id('expand_children'); ?>"><?php _e('Mostrar anidados desplegados por defecto', 'rafax_pages'); ?></label>
        </p>
        <?php
    }


    // Guardar las opciones del widget
    public function update($new_instance, $old_instance)
    {
        //error_log('Saving expand_children: ' . (!empty($new_instance['expand_children']) ? 'true' : 'false'));
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['expand_children'] = !empty($new_instance['expand_children']) ? 1 : 0;

        return $instance;
    }
}

// Registrar el widget
function register_rafax_pages_widget()
{
    register_widget('Rafax_Nested_Pages');
}
add_action('widgets_init', 'register_rafax_pages_widget');
