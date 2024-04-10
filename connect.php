<?  
    
    $connect = mysqli_connect('localhost', 'username', 'password', 'Название БД');

        if (!$connect) {

        die('Error connect to DataBase');

        }
?>