<?php
        $host = ''; // адрес сервера 
        $database = ''; // имя базы данных
        $user_name = ''; // имя пользователя
        $password = ''; // пароль

    final class Init{
        private $host; // адрес сервера 
        private $database; // имя базы данных
        private $user_name; // имя пользователя
        private $password; // пароль
        private $arr_result = ['normal', 'illegal', 'failed', 'success'];//варианты поля result(возможно для этого было бы лучше сделать отдельную таблицу и подставлять id)
                            

        public function __construct($host, $database, $user, $password){
            $this->host = $host;
            $this->database = $database;
            $this->user_name = $user;
            $this->password = $password;
            $this->create();
            $this->fill();
        }

        private function query_db($query){
            $connect = mysqli_connect($this->host, $this->user_name, $this->password, $this->database) 
                or die("Ошибка " . mysqli_error($connect));
            if($connect){
                echo "</br>Успешное подключение";
            } 
            // выполняем операции с базой данных
            
            $result = mysqli_query($connect, $query) or die("Ошибка " . mysqli_error($connect));
                if($result){
                    echo "</br>Таблица table создана успешно!";
                }                  
            // закрываем подключение
            mysqli_close($connect);
            return $result;
        }


        private function create(){
            $query = "CREATE TABLE test(id INT NOT NULL AUTO_INCREMENT,
                        script_name VARCHAR(25) NOT NULL,
                        start_time INT NOT NULL,
                        end_time INT NOT NULL,
                        result VARCHAR(10) NOT NULL,
                        PRIMARY KEY (id));";
            $this->query_db($query);
        }

        private function fill(){
            $query = "INSERT INTO test (script_name, start_time, end_time, result) 
                        VALUES ('start', 2, 5, 'normal'),
                        ('begin', 4, 8, 'illegal'),
                        ('going', 7, 10, 'failed'),
                        ('doing', 12, 45, 'success'),
                        ('end', 23, 50, 'normal');";
            $this->query_db($query);
        }

        public function get(){
            $query = "SELECT * FROM test WHERE result = 'normal' OR result = 'success';";
            return $this->query_db($query);
        }
    }

    $init = new Init($host, $database, $user_name, $password);
    var_dump($init->get());
     
?>
