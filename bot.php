<?
include "connect.php";
include "config.php";

#старт бота
if ($text == '/start')
{
    $check_user = mysqli_query($connect, "SELECT * FROM `proverka` WHERE `chat_id` = '$chat_id'");
    if (mysqli_num_rows($check_user) == 0)
    {
        mysqli_query($connect, "INSERT INTO `proverka` (`chat_id`) VALUES ('$chat_id')");
    }

    $menu[] = ['text' => "Библиотека", 'callback_data' => "/mylib"];
    $menu = array_chunk($menu, 1);
    $menu = ['inline_keyboard' => $menu];

    sendTelegram('sendMessage', array(
        'chat_id' => $chat_id,
        'text' => "<i>👋🏻 Приветствую, @$name!</i>

<b>Заходи в библиотеку!</b>",
        'parse_mode' => 'html',
        'reply_markup' => json_encode($menu)
    ));
    mysqli_query($connect, "UPDATE `proverka` SET `books` = '0' WHERE `chat_id` = '$chat_id' ");
}

#выбор действий
if ($callbackInline == '/mylib')
{
    mysqli_query($connect, "UPDATE `proverka` SET `books` = '0' WHERE `chat_id` = '$chat_id_in' ");
    $menu[] = ['text' => "📚 Список книг", 'callback_data' => "/allbooks"];
    $menu[] = ['text' => "🔎 Поиск книги", 'callback_data' => "/searchbook"];
    $menu[] = ['text' => "➕ Добавить книгу", 'callback_data' => "/addbook"];
    $menu = array_chunk($menu, 2);
    $menu = ['inline_keyboard' => $menu];

    sendTelegram('sendMessage', array(
        'chat_id' => $chat_id_in,
        'text' => "<i>👋🏻 Привет, @$nameinline!</i>

<b>Выбери действие:</b>",
        'parse_mode' => 'html',
        'reply_markup' => json_encode($menu)
    ));
}

#добавление книги 
if ($callbackInline == '/addbook')
{
    $menu[] = ['text' => "🔙 В меню", 'callback_data' => "/mylib"];
    $menu = array_chunk($menu, 2);
    $menu = ['inline_keyboard' => $menu];
    mysqli_query($connect, "UPDATE `proverka` SET `books` = '2' WHERE `chat_id` = '$chat_id_in' ");
    sendTelegram('editMessageText', array(
        'chat_id' => $chat_id_in,
        'message_id' => $message_id_in,
        'text' => "<b>➕ Добавление книги:</b>

<b>Напишите название книги:</b>",
        'parse_mode' => 'html',
        'reply_markup' => json_encode($menu)
    ));
    return false;
}

$request = mysqli_query($connect, "SELECT * FROM `proverka` WHERE `chat_id` = '$chat_id' LIMIT 1 ");
while ($books = mysqli_fetch_assoc($request))
{
    $act = $books['books'];
}
if ($act == 2)
{
    $find = mysqli_real_escape_string($connect, $text);
    mysqli_query($connect, "INSERT INTO `books` (`book`,`author`,`description`,`genre`) VALUES ('$find','NULL','NULL','NULL')");
    sendTelegram('sendMessage', array(
        'chat_id' => $chat_id,
        'text' => "<b>➕ Добавление книги:</b>

<b>Напишите Автора книги:</b>",
        'parse_mode' => 'html'
    ));
    mysqli_query($connect, "UPDATE `proverka` SET `books` = '3' WHERE `chat_id` = '$chat_id' ");
    return false;
}
if ($act == 3)
{
    sendTelegram('sendMessage', array(
        'chat_id' => $chat_id,
        'text' => "<b>➕ Добавление книги:</b>

<b>Напишите описание книги:</b>",
        'parse_mode' => 'html'
    ));
    $find = mysqli_real_escape_string($connect, $text);
    mysqli_query($connect, "UPDATE `books` SET `author` = '$find' WHERE `author` = 'NULL' ");
    mysqli_query($connect, "UPDATE `proverka` SET `books` = '4' WHERE `chat_id` = '$chat_id' ");
    return false;
}
if ($act == 4)
{
    sendTelegram('sendMessage', array(
        'chat_id' => $chat_id,
        'text' => "📝",
        'parse_mode' => 'html'
    ));
        $request = mysqli_query($connect, "SELECT * FROM `genre`");
    $rows = mysqli_num_rows($request);
    while ($books = mysqli_fetch_assoc($request))
    {
        $genre = $books['genre'];
        $id = $books['id'];
        $bookmenu[] = ['text' => "$genre", 'callback_data' => "/addbookgenre_$id"];
    }
    $bookmenu = array_chunk($bookmenu, 2);
    $bookmenu = ['inline_keyboard' => $bookmenu];
    sendTelegram('sendMessage', array(
        'chat_id' => $chat_id,
        'text' => "<b>➕ Добавление книги:</b>

<b>Выбери жанр книги:</b>",
        'reply_markup' => json_encode($bookmenu),
        'parse_mode' => 'html'
    ));
    $find = mysqli_real_escape_string($connect, $text);
    mysqli_query($connect, "UPDATE `books` SET `description` = '$find' WHERE `description` = 'NULL' ");
    mysqli_query($connect, "UPDATE `proverka` SET `books` = '5' WHERE `chat_id` = '$chat_id' ");
    return false;
}

#поиск книги
if ($callbackInline == '/searchbook')
{
    $menu[] = ['text' => "🔙 В меню", 'callback_data' => "/mylib"];
    $menu = array_chunk($menu, 2);
    $menu = ['inline_keyboard' => $menu];
    mysqli_query($connect, "UPDATE `proverka` SET `books` = '1' WHERE `chat_id` = '$chat_id_in' ");
    sendTelegram('editMessageText', array(
        'chat_id' => $chat_id_in,
        'message_id' => $message_id_in,
        'text' => "<b>🔎 Поиск книги:</b>

<b>Введи ключевые слова автора книги, или название книги для ее поиска.</b>",
        'parse_mode' => 'html',
        'reply_markup' => json_encode($menu)
    ));
    return false;
}

$request = mysqli_query($connect, "SELECT * FROM `proverka` WHERE `chat_id` = '$chat_id' LIMIT 1 ");
while ($books = mysqli_fetch_assoc($request))
{
    $act = $books['books'];
    if ($act == 1)
    {
        sendTelegram('sendMessage', array(
            'chat_id' => $chat_id,
            'text' => "🔎",
            'parse_mode' => 'html'
        ));
        $find = mysqli_real_escape_string($connect, $text); #зеркалим
        $result = mysqli_query($connect, "SELECT * FROM `books` WHERE `book` LIKE '%$find%' OR `author` LIKE '%$find%'");
        $rows = mysqli_num_rows($result);
        if ($rows > 0)
        {
            while ($books = mysqli_fetch_assoc($result))
            {
                $book = $books['book'];
                $author = $books['author'];
                $id = $books['id'];
                $bookmenu[] = ['text' => "$book | $author", 'callback_data' => "/showbook_$id"];
            }

            $bookmenu = array_chunk($bookmenu, 2);
            $bookmenu = ['inline_keyboard' => $bookmenu];
            sendTelegram('sendMessage', array(
                'chat_id' => $chat_id,
                'text' => "<b>✅ Найдено</b> <code>$rows</code> <b>книг:</b>
<b>Выбери книгу для просмотра подробной информации:</b>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode($bookmenu)
            ));
        }
        if ($rows == '0')
        {
            sendTelegram('sendMessage', array(
                'chat_id' => $chat_id,
                'text' => "<b>❌ К сожалению книги по вашему запросу не найдены :/</b>",
                'parse_mode' => 'html'
            ));
            mysqli_query($connect, "UPDATE `proverka` SET `books` = '0' WHERE `chat_id` = '$chat_id' ");
        }
    }
}

#все книги
if ($callbackInline == '/allbooks')
{
    $menu[] = ['text' => "Поиск книги по жанру", 'callback_data' => "/showgenrebooks"];
    $menu[] = ['text' => "Отобразить все книги", 'callback_data' => "/showallbooks"];
    $menu = array_chunk($menu, 1);
    $menu = ['inline_keyboard' => $menu];

    sendTelegram('editMessageText', array(
        'chat_id' => $chat_id_in,
        'message_id' => $message_id_in,
        'text' => "<b>📋 Общий список книг:</b>

<b>Выбери метод отображения списка книг</b>",
        'parse_mode' => 'html',
        'reply_markup' => json_encode($menu)
    ));
}

if ($callbackInline == '/showallbooks')
{
    $request = mysqli_query($connect, "SELECT * FROM `books`");
    $rows = mysqli_num_rows($request);
    while ($books = mysqli_fetch_assoc($request))
    {
        $book = $books['book'];
        $author = $books['author'];
        $id = $books['id'];
        $bookmenu[] = ['text' => "$book | $author", 'callback_data' => "/showbook_$id"];
    }
    $bookmenu = array_chunk($bookmenu, 2);
    $bookmenu = ['inline_keyboard' => $bookmenu];

    sendTelegram('editMessageText', array(
        'chat_id' => $chat_id_in,
        'message_id' => $message_id_in,
        'text' => "<b>📋 Общий список книг:</b>
<b>Количество книг в нашей библиотеке:</b> <code>$rows</code>

<b>Выбери книгу для просмотра подробной информации:</b>",
        'parse_mode' => 'html',
        'reply_markup' => json_encode($bookmenu)
    ));
}

#книги по жанру
if ($callbackInline == '/showgenrebooks')
{
    $request = mysqli_query($connect, "SELECT * FROM `genre`");
    $rows = mysqli_num_rows($request);
    while ($books = mysqli_fetch_assoc($request))
    {
        $genre = $books['genre'];
        $id = $books['id'];
        $bookmenu[] = ['text' => "$genre", 'callback_data' => "/showbookgenre_$id"];
    }
    $bookmenu = array_chunk($bookmenu, 2);
    $bookmenu = ['inline_keyboard' => $bookmenu];

    sendTelegram('editMessageText', array(
        'chat_id' => $chat_id_in,
        'message_id' => $message_id_in,
        'text' => "<b>📋 Общий список книг:</b>

<b>Выбери жанр:</b>",
        'parse_mode' => 'html',
        'reply_markup' => json_encode($bookmenu)
    ));
}

#добавление книги
if (in_array(mb_strtolower($explodeinline[0]) , ['trimcmd', '/addbookgenre']))
{
$trimcmd = substr_replace($callbackInline, null, 0, 14);
    $menu[] = ['text' => "🔙 В меню", 'callback_data' => "/mylib"];
    $menu = array_chunk($menu, 2);
    $menu = ['inline_keyboard' => $menu];
    sendTelegram('sendMessage', array(
        'chat_id' => $chat_id_in,
        'message_id'=>$message_id_in,
        'text' => "✅ Книга успешно добавлена!",
        'parse_mode' => 'html',
        'reply_markup' => json_encode($menu)
    ));
    mysqli_query($connect, "UPDATE `books` SET `genre` = '$trimcmd' WHERE `genre` = 'NULL' ");
    mysqli_query($connect, "UPDATE `proverka` SET `books` = '0' WHERE `chat_id` = '$chat_id_in' ");
    return false;
}

#отобразить книги по жанру
if (in_array(mb_strtolower($explodeinline[0]) , ['trimcmd', '/showbookgenre']))
{
    $trimcmd = substr_replace($callbackInline, null, 0, 15);
    $request = mysqli_query($connect, "SELECT * FROM `books` WHERE `genre` = '$trimcmd' ");
    $rows = mysqli_num_rows($request);
    while ($books = mysqli_fetch_assoc($request))
    {
        $book = $books['book'];
        $author = $books['author'];
        $id = $books['id'];
        $bookmenu[] = ['text' => "$book | $author", 'callback_data' => "/showbook_$id"];
    }
    $bookmenu = array_chunk($bookmenu, 2);
    $bookmenu = ['inline_keyboard' => $bookmenu];

    sendTelegram('editMessageText', array(
        'chat_id' => $chat_id_in,
        'message_id' => $message_id_in,
        'text' => "<b>📋 Общий список книг по выбранному жанру $trimcmd:</b>
<b>Количество книг в нашей библиотеке:</b> <code>$rows</code>

<b>Выбери книгу для просмотра подробной информации:</b>",
        'parse_mode' => 'html',
        'reply_markup' => json_encode($bookmenu)
    ));

}

#удалить книгу
if (in_array(mb_strtolower($explodeinline[0]) , ['trimcmd', '/deletebook']))
{

    $trimcmd = substr_replace($callbackInline, null, 0, 12);

    $menu[] = ['text' => "🔙 В меню", 'callback_data' => "/mylib"];
    $menu = array_chunk($menu, 2);
    $menu = ['inline_keyboard' => $menu];
    sendTelegram('editMessageText', array(
        'chat_id' => $chat_id_in,
        'message_id' => $message_id_in,
        'text' => "✅ Книга успешно удалена!",
        'parse_mode' => 'html',
        'reply_markup' => json_encode($menu)
    ));
    mysqli_query($connect, "DELETE FROM `books` where `id` = '$trimcmd'");
}

#показать книги
if (in_array(mb_strtolower($explodeinline[0]) , ['trimcmd', '/showbook']))
{
    $trimcmd = substr_replace($callbackInline, null, 0, 10);
    $request = mysqli_query($connect, "SELECT * FROM `books` WHERE `id` = '$trimcmd' ");
    $rows = mysqli_num_rows($request);
    while ($books = mysqli_fetch_assoc($request))
    {
        $book = $books['book'];
        $author = $books['author'];
        $id = $books['id'];
        $desc = $books['description'];
    }
    $bookmenu[] = ['text' => "✖️ Удалить книгу", 'callback_data' => "/deletebook_$id"];
    $bookmenu = array_chunk($bookmenu, 2);
    $bookmenu = ['inline_keyboard' => $bookmenu];
    sendTelegram('editMessageText', array(
        'chat_id' => $chat_id_in,
        'message_id' => $message_id_in,
        'text' => "<b>📖 Выбранная книга:</b> $book
<b>©️ Автор книги:</b> $author
<b>ℹ️ Описание книги:</b> $desc",
        'parse_mode' => 'html',
        'reply_markup' => json_encode($bookmenu)
    ));

}

?>
