<?php
/*
Plugin Name:  Parse Product for WC
Description:  This plugin Parse Product for WC from Internet Shop.
Version:      1.1.0
Author:       Denis Belocerkovec, Dmitry Taran
Text Domain:  parse-product
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html

*/

// При активации плагина
function parse_install()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'parser_news';
    if ($wpdb->get_var("SHOW TABLES LIKE $table_name") != $table_name) {
        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                `id_news` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
                `body` VARCHAR(1500) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
                `path_img` VARCHAR(1500) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
                `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $wpdb->query($sql);
    }

    add_option('qty_news', 20);
}

// При ДЕактивации плагина
function parse_uninstall()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'parser_news';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);

    delete_option('qty_news');
}

//register_activation_hook(__FILE__, 'parse_install');
//register_deactivation_hook(__FILE__, 'parse_uninstall');

// Самописный плагин
function parse_prod()
{

    if(isset($_GET['gather_prod']) && $_GET['gather_prod'] === 'gather_prod') {
        require_once 'includes/collect.php';
    } else {
        require_once 'includes/form.php';
    }
}

function editor_admin_menu()
{
// Создаём пункт меню в админке в записях
    /* 1)Title 2)Имя пункта меню 3)Уровень доступа 4)Уникальное название url адресса 5)Название функции которая исполниться*/
    add_menu_page('Parse Prod', 'Parse Prod', 'manage_options', 'parse_prod', 'parse_prod');
}

// Подцепляем функцию parse_news при загрузке нашей страницы
add_action('admin_menu', 'editor_admin_menu');
