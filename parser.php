<?php 

    error_reporting(E_ALL);

    $host = ''; // адрес сервера 
        $database = ''; // имя базы данных
        $user_name = ''; // имя пользователя
        $password = ''; // пароль

    $page = file_get_contents('https://www.bills.ru');

    $pattern = '/события на долговом рынке.*?Блок новостей END/us';
    preg_match($pattern, $page, $result); //Получаем блок "События на долговом рынке"

    $date_pattern = '/(?<=news_date\">)(.*?)(?=<)/uism';
    preg_match_all($date_pattern, $result[0], $date_result); //Получаем даты
    
    $title_pattern = '/(\"https\:\/\/www\.bills\.ru\/news\/\?bid=\d+">)(.*?)(?=\<\/a>)/uism';
    preg_match_all($title_pattern, $result[0], $title_result); //Получаем ссылку и заголовок
    
    //разбиваем строку ссылка - заголовок  на ссылку и заголовок
    $dates = [];
    $days = [];
    $months = [];
    $titles = [];   
    $links = [];
    for($i = 0; $i < count($title_result[0]); ++$i){
        $link_title = explode('>', $title_result[0][$i]);
        $links[$i] = $link_title[0];
        $titles[$i] = $link_title[1];

        $dates[$i] = trim(preg_replace('/\s+/', ' ', $date_result[0][$i]));
        
        $days_months = explode(' ', $dates[$i]);
        $days[$i] = $days_months[0];
        $months[$i] = $days_months[1];
    }


    //Функция записи в базу 
    function db_request($host, $user, $password, $database, $query){
        $connect = mysqli_connect($host, $user, $password, $database) 
            or die("Ошибка " . mysqli_error($connect));
        if($connect){
            echo "</br>Успешное подключение";
        } 
        
        $result = mysqli_query($connect, $query) or die("Ошибка " . mysqli_error($connect));
            if($result){
                echo "</br>Запрос обработан успешно!";
            }                  
        mysqli_close($connect);
        return $result;
    }

   //Таблица создавалась по такому запросу 
        /*"CREATE TABLE bills_ru_events(id INT NOT NULL AUTO_INCREMENT,
        date DATETIME,
        title VARCHAR(230),
        url VARCHAR(240) NOT NULL,
        PRIMARY KEY (id))
        character set utf8 collate utf8_unicode_ci engine=InnoDB;"*/;

//Формируем запрос отправки данных в базу 
    $months_arr = [
        'янв' => '01',
        'фев' => '02',
        'мар' => '03',
        'апр' => '04',
        'май' => '05',
        'июн' => '06',
        'июл' => '07',
        'авг' => '08',
        'сен' => '09',
        'окт' => '10',
        'ноя' => '11',
        'дек' => '12'
    ];
$query = "INSERT INTO bills_ru_events (date, title, url) 
                        VALUES";
    for($i = 0; $i < count($dates); ++$i){
        $query .= "('2020-" . $months_arr[$months[$i]]. "-$days[$i]'" . ", '$titles[$i]', '$links[$i]')"; 
        $query .= $i < (count($dates) - 1) ? ', ' : ';';
    }
    
    db_request('localhost', 'root', 'Hesaga0808!', 'shop', $query);
