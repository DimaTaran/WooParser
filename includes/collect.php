<?php

require_once 'CollectNews.php';

$link_donor = cleanData($_GET['link_donor']);
$class_all_link_news = cleanData($_GET['class_all_link_news']);

$prod_start = cleanData($_GET['prod_start']);
$prod_end = cleanData($_GET['prod_end']);

$class_title_one_news = cleanData($_GET['class_title_one_news']);
$class_img_link = cleanData($_GET['class_img_link']);
$class_video_link = cleanData($_GET['class_video_link']);
$class_link_text = cleanData($_GET['class_link_text']);
$class_prod_price = cleanData($_GET['class_prod_price']);
$class_link_short_text = cleanData($_GET['class_link_short_text']);
$custom_fields = cleanData($_GET['custom_fields']);
$custom_fields_value = cleanData($_GET['custom_fields_value']);


unset($_GET);

function cleanData($value = '')
{
/*    if (is_string($value)) {
        $value = trim($value);
    }*/
    return $value;
}

$collect = new CollectNews($link_donor, $class_all_link_news, $prod_start, $prod_end, $class_title_one_news, $class_img_link, $class_video_link, $class_link_text,
    $class_prod_price, $class_link_short_text, $custom_fields, $custom_fields_value);
echo 'Результат: ' . $collect->collect();