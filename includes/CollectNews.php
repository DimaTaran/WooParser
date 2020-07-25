<?php

use \voku\helper\HtmlDomParser;

require_once ( __DIR__ . '/../vendor/autoload.php');

class CollectNews
{
/* TODO
different price (OR)
list of custom field, order
grab img for 1 product by ID

*/


    public $link_donor;
    public $class_all_link_news;
    public $prod_start;
    public $prod_end;
    public $class_title_one_news;
    public $class_img_link;
    public $class_video_link;
    public $class_link_text;
    public $class_prod_price;
    public $class_link_short_text;
    public $custom_fields;
    public $custom_fields_value;

    public function __construct($link_donor, $class_all_link_news,  $prod_start = 1 , $prod_end = 99, $class_title_one_news = '', $class_img_link = '', $class_video_link = '', $class_link_text = '', $class_prod_price = '', $class_link_short_text = '', $custom_fields = '', $custom_fields_value = '')
    {
        $this->link_donor = $link_donor;
        $this->class_all_link_news = $class_all_link_news;
        $this->prod_start =$prod_start;
        $this->prod_end = $prod_end;
        $this->class_title_one_news = $class_title_one_news;
        $this->class_img_link = $class_img_link;
        $this->class_video_link = $class_video_link;
        $this->class_link_text = $class_link_text;
        $this->class_prod_price = $class_prod_price;
        $this->class_link_short_text = $class_link_short_text;
        $this->custom_fields = $custom_fields;
        $this->custom_fields_value = $custom_fields_value;


        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
    }

    public function collect()
    {

        // Разбираем главную страницу
        $html = HtmlDomParser::file_get_html($this->link_donor);

        $arr_link = [];

        // Get links for product from Donor page

        $i = 0; // counter
        foreach ($html->find($this->class_all_link_news) as $e) {
            if ( $i >= ($this->prod_start - 1) && $i < $this->prod_end ) {
                $arr_link[] = $e->href;
            }
           $i++;
        }

        foreach ( $arr_link as $k => $v ) {
            $res_str = parse_url($v);
            $domain =  parse_url( $this->link_donor );

            if ( !isset( $res_str['scheme'] ) && !isset( $res_str['host'] ) ) {
                $arr_link[$k] = $domain['scheme'] . '://' . $domain['host'] . $arr_link[$k];
            }

        }

        if (count($arr_link) > 0) {
            foreach ($arr_link as $key => $value) {

                unset($arr_text);
                $arr_text = [];
                $short_desc = '';
                
                // Разбираем страницу поста
                $html = HtmlDomParser::file_get_html($value);

                // Поиск главного заголовка поста
                $res_title = $html->find($this->class_title_one_news);
                $title = $res_title->innertext;


                // Поиск текста
                foreach ($html->find($this->class_link_text) as $k => $v) {
                    $arr_text[] = '<p>' . $v->innertext . '</p>';
                }

                // Search Product Short description
                foreach ($html->find($this->class_link_short_text) as $k => $v) {
                    $short_desc = '<p>' . $v->innertext . '</p>';
                }


                // Search price
                $res_price = $html->find($this->class_prod_price);
                $stringPrice =  $res_price->innertext;
                $arrPrice = explode(' ', $stringPrice[0]);
                $price = intval($arrPrice[0]);

                // Поиск видео
                $res_video = $html->find($this->class_video_link);
                $video_link = $res_video->src;

                if (isset($video_link) && count($video_link) > 0) {
                    foreach ($video_link as $k => $v) {
                        if (strpos($v, 'youtube')) {
                            $arr_text[] = '<iframe width="560" height="450" src="' . $v . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                        }
                    }
                }

                // Поиск изображения
                $res_img = $html->find($this->class_img_link);

                $img_links = $res_img->src;
                $img_link = [];
                foreach ($img_links as $img_l) {
                    $img_link_url = parse_url( $img_l );
                    $domain =  parse_url( $this->link_donor );
                    if ( isset( $img_link_url['scheme'] ) && isset( $img_link_url['host'] ) ) {
                        $img_link[] = $img_l;
                    } else {
                        $img_link[] = $domain['scheme'] . '://' . $domain['host'] . $img_link_url['path'];
                    }
                }


//                if (isset($img_link) && count($img_link) > 0) {
//                    foreach ($img_link as $k => $v) {
//                        if (isset($v)) {
//                            $arr_text[] = '<img src="' . $v . '" alt="' . $title[0] . '">';
//                        }
//                    }
//                }

                $long_str = implode(' ', $arr_text);


                // Создаем массив данных новой записи WP
                $post_data = array(
                    'post_title' => $title[0],
                    'post_excerpt' => $short_desc,
                    'post_content' => $long_str,
                    'post_status' => 'draft',
                    'post_author' => 1,
                    'post_type' => 'product'
                );

                // Вставляем запись в базу данных WP
                $post_id = wp_insert_post($post_data);

                //Add price to product

                update_post_meta( $post_id, '_regular_price', $price );
                update_post_meta( $post_id, '_price', $price );

                //Add custom fields to product

                $arrCustom_fields_value = explode(',', $this->custom_fields_value);
                $arrCustom_fields = explode(',',  $this->custom_fields);
                $arr_count = count($arrCustom_fields);

                for( $i = 0; $i < $arr_count; $i++ ) {
                    $cf_value_row = $html->find( trim( $arrCustom_fields_value[$i] ) );
                    $cf_value =  $cf_value_row->innertext;

                    update_post_meta( $post_id, trim( $arrCustom_fields[$i] ), $cf_value );
                }




                /**************************************************/
                //Add img to product
                if(isset($img_link[0])){
                    $title = $title[0];
                    $img_name = basename($img_link[0]);
                    $arrImg_name = explode('.', $img_name);
                     if ( $arrImg_name[1] == 'webp') {
                         $tmp_file = imagecreatefromwebp($img_link[0]);
                         $img_name = $arrImg_name[0] .'.jpg';
                         $path = wp_upload_dir();
                         // Сконвертировать его в jpeg-файл
                         imagejpeg($tmp_file, $path['path'] . '/'. $img_name);
                         // загружаем временный $tmp_file-файл в медиатеку WordPress
                         $att_id = media_handle_sideload( array(
                             'name' => $img_name, // имя файла берем из URL-а
                             'tmp_name' => $path['path'] . '/'. $img_name, // путь к временному файлу
                         ), $post_id, $title );
                         imagedestroy($tmp_file);

                     }  else {
                         // скачиваем файл по URL-адрес хранящемуся в переменной $url
                         $tmp_file = download_url( $img_link[0] );
                         $img_name = basename($img_link[0]);
                         // загружаем временный $tmp_file-файл в медиатеку WordPress
                         $att_id = media_handle_sideload( array(
                             'name' => $img_name, // имя файла берем из URL-а
                             'tmp_name' => $tmp_file, // путь к временному файлу
                         ), $post_id, $title );
                     }
                    // Get original filename
//                    $full_img_name = basename($img_link[0]);
                    // Get filename without extension
//                    $arrImg_name = explode('.', $full_img_name);
//
//                    if (strlen($arrImg_name[0]) > 40) {
//                        $img_name = substr($arrImg_name[0], 0,40) . '.jpg';
//
//                    } else $img_name = $arrImg_name[0] . '.jpg';




                    // устанавливаем загруженный файл для записи в качестве миниатюры (thumbnail)
                    set_post_thumbnail($post_id, $att_id);
                }
                /**************************************************/

            }


            return "<h2 style='font-size: 2rem;color:#009556;'>Проверьте, - добавились ли новые записи?!</h2>";
        } else {
            return "<h2 style='font-size: 2rem;color:#c43343;'>Ошибка, попробуйте изменить CSS селекторы. Возможно у сайта 'донора' динамические CSS селекторы.</h2>";
        }
    }

//    public function curl_get($url, $referer = 'http://www.google.com')
//    {
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_HEADER, 0);
//        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36");
//        curl_setopt($ch, CURLOPT_REFERER, $referer);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $data = curl_exec($ch);
//        curl_close($ch);
//        return $data;
//    }
}