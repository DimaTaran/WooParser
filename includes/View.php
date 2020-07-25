<?php


class View
{
    public $form = '';


    public function get_from_Get(string $name, $default = '')
    {
        $$name = isset($_GET[$name]) ? $_GET[$name] : $default;
    }

    public function get_number_input(string $name, string $label, string $desc, int $default = 1)
    {
     return
        '<div class="item">
            <label for="' . $name . '">' . $label .'</label>
            <span class="description">' . $desc .'</span>
            <input type="number" name="' . $name . '" id="' . $name . '" value="' . $default . '">
         </div><br>';
    }

    public function get_form()
    {
        $this->form .= '<style>
            @import url("https://fonts.googleapis.com/css?family=Rubik&display=swap");
            h1 {
                font-family: "Rubik", sans-serif;
                font-size: 32px;
                border-bottom: 1px solid #222;
                color: #222;
                margin-top: 3rem;
                margin-bottom: 3rem;
                margin-left: 15px;
            }
            .item {
                margin: 1rem;
            }           
            .item label {
                color: #333;
                font-size: 20px;
                display: block;
                font-family: "Rubik", sans-serif;
            }
            .item input {
                height: 30px;
                text-indent: .5rem;               
                min-width: 320px;
                max-width: 700px;
                border-radius: 3px;
                border: 2px solid #e0e0e0;
            }
            .item {
                display: inline-block;
            }
            #save > input {
                height: 50px;
                border-radius: 0 50px 50px 0;
                background: #4a80ad;
                box-shadow: 1px 1px 5px rgba(50, 50, 50, 0.5);
                color: #fff;
                font-family: "Rubik", sans-serif;
            }
            
            #save > input:active,
            #save > input:focus {
                border: none;
                outline: none;
            }
            
            #save > input:hover {
                background: #47709a;
                font-family: "Rubik", sans-serif;
                box-shadow: 1px 1px 10px rgba(50, 50, 50, 0.5);
                cursor: pointer;
            }
            .description {
                font-size: 14px;
                display: block;
                color: #969191;
            }
        </style>';

        $this->form .= '<h1>Собираем новости</h1>';

        $this->form .= '<form action="' . get_site_url(). '/wp-admin/admin.php" method="get">';

        $this->form .= '<input type="hidden" name="page" value="parse_prod">';

        $link_donor = isset($_GET['link_donor']) ? $_GET['link_donor'] : '';

//        $this->get_from_Get('link_donor', $default = '');

        $this->form .= '<div class="item">
            <label for="link_donor">Сайт донор:</label>
            <span class="description">Без последнего слэша, - https://test.ru</span>
            <input type="text" name="link_donor" id="link_donor" value="' . $link_donor . '" required>
         </div><br>';

        $class_all_link_news = isset($_GET['class_all_link_news']) ? $_GET['class_all_link_news'] : '';
        $this->form .= '<div class="item">
            <label for="class_all_link_news">Классы со ссылками на статьи:</label>
            <span class="description">В формате CSS селекторов, например: .post_link</span>
            <input type="text" name="class_all_link_news" id="class_all_link_news" value="' . $class_all_link_news . '" required>
         </div><br>';

        $this->get_from_Get('prod_start', 999);
        $this->form .= $this->get_number_input('prod_start', 'Номер первого товара:', 'С какого товара начать выборку');

        $this->get_from_Get('prod_end', 999);
        $this->form .= $this->get_number_input('prod_end', 'Номер последнего товара:', 'Каким товаром закончить выборку', 999);

//        $this->form .= '<div class="item">
//            <label for="prod_end">Номер последнего товара:</label>
//            <span class="description">Каким товаром закончить выборку</span>
//            <input type="number" name="prod_end" id="prod_end" value="' . $prod_end . '">
//         </div><br>';


        $class_title_one_news = isset($_GET['class_title_one_news']) ? $_GET['class_title_one_news'] : '';
        $this->form .= '<div class="item">
            <label for="class_title_one_news">Класс или id заголовка одной полной статьи:</label>
            <span class="description">В формате CSS селекторов, например: #post_title</span>
            <input type="text" name="class_title_one_news" id="class_title_one_news" value="' . $class_title_one_news . '" required>
         </div><br>';

        $class_img_link = isset($_GET['class_img_link']) ? $_GET['class_img_link'] : '';
        $this->form .= '<div class="item">
            <label for="class_img_link">Класс или id со ссылками на изображение внутри одной полной статьи:</label>
            <span class="description">В формате CSS селекторов, например: .post_img</span>
            <input type="text" name="class_img_link" id="class_img_link" value="' . $class_img_link . '">
         </div><br>';

        $class_video_link = isset($_GET['class_video_link']) ? $_GET['class_video_link'] : '';
        $this->form .= '<div class="item">
            <label for="class_video_link">Класс или id со ссылками на видео внутри одной полной статьи:</label>
            <span class="description">В формате CSS селекторов, например: .post_video</span>
            <input type="text" name="class_video_link" id="class_video_link" value="' . $class_video_link . '">
         </div><br>';

        $class_link_text = isset($_GET['class_link_text']) ? $_GET['class_link_text'] : '';
        $this->form .= '<div class="item">
            <label for="class_link_text">Класс текста полной статьи:</label>
            <span class="description">В формате CSS селекторов, например: .post_text > p</span>
            <input type="text" name="class_link_text" id="class_link_text" value="' . $class_link_text . '">
         </div><br><br>';

        $class_link_short_text = isset($_GET['class_link_short_text']) ? $_GET['class_link_short_text'] : '';
        $this->form .= '<div class="item">
            <label for="class_link_short_text">Класс краткого описания товара:</label>
            <span class="description">В формате CSS селекторов, например: .post_text > p</span>
            <input type="text" name="class_link_short_text" id="class_link_short_text" value="' . $class_link_short_text . '">
         </div><br><br>';

        $class_prod_price = isset($_GET['class_prod_price']) ? $_GET['class_prod_price'] : '';
        $this->form .= '<div class="item">
            <label for="class_prod_price">Класс цены товара:</label>
            <span class="description">В формате CSS селекторов, например: .prod_price > span</span>
            <input type="text" name="class_prod_price" id="class_prod_price" value="' . $class_prod_price . '">
         </div><br><br>';


        $custom_fields = isset($_GET['custom_fields']) ? $_GET['custom_fields'] : '';
        $this->form .= '<div class="item">
            <label for="class_link_text">Ваши кастомные поля через запятую:</label>
            <span class="description">В формате, например: color, size</span>
            <input type="text" name="custom_fields" id="custom_fields" size="100" value="' . $custom_fields . '">
         </div><br><br>';

        $custom_fields_value = isset($_GET['custom_fields_value']) ? $_GET['custom_fields_value'] : '';
        $this->form .= '<div class="item">
            <label for="class_link_text">Класс значения для вашего кастомного поля через запятую в соотвествии с CF:</label>
            <span class="description">В формате CSS селекторов, например: .post_text > p, .prod_text > div</span>
            <input type="text" name="custom_fields_value" id="custom_fields_value" size="100" value="' . $custom_fields_value . '">
         </div><br><br>';



        $this->form .= '<div class="item" id="save">
             <input type="submit" name="gather_prod" value="gather_prod">
          </div>';

        $this->form .= '</form>';

        return $this->form;
    }
}