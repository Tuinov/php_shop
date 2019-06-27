<?php
// удаление из корзины
if(isset($_GET['deleteId'])) {
    $delId = $_GET['deleteId'];
    // var_dump($delId);
    $link = mysqli_connect('localhost', 'root', '', 'geekbrains');
    $result = mysqli_query($link, "DELETE FROM `cart` WHERE `cart`.`id` = '{$delId}'");
    
}
// Если нажата кнопка купить - прилетает id товара. по нему запрашивается товар из 
// бд  и записывается в карзину
if(isset($_GET['cartId'])) {
    session_start();
    $session = session_id();
    $idProduct = $_GET['cartId'];
    $link = mysqli_connect('localhost', 'root', '', 'geekbrains');
    $result = mysqli_query($link, "SELECT * FROM gallery WHERE id={$idProduct}");
    $goods = mysqli_fetch_assoc($result);
    
    
    $name = $goods['name'];
    $price = $goods['price'];

    
    $result = mysqli_query($link, "INSERT INTO `cart` (`id_product`, `name`, `price`, `quantity`, `session_id`) 
    VALUES ('{$idProduct}', '{$name}', '{$price}', '1', '{$session}')");
    
}

function prepareVariables($page) {
    $params = [];

    switch ($page) {
        case 'index':
        break;
        case 'single':
       
        $id = (int)$_GET['id'];

        // увеличивает просмотры на 1
        likeUp($id);

        $params = [
            'row' => getSinglle($id, 'gallery')
        ];
        break;
        
        case 'catalog':
        $params = [
            'goods' => getGoods('gallery')
        ];
        break;
        
        case 'cart':
        
        $params = [
            'result' => getGoods('cart'),
        ];
        break;
       

        case 'gallery':
        $params = [
            'catalog' => ["Чай", "Печенье", "Вафли"]
        ];
    }
    return $params;
}
// получение всех товаров из таблицы $table
function getGoods($table) {
    $link = getDb();
    $result = mysqli_query($link, "SELECT * FROM $table WHERE 1");
    while($row = mysqli_fetch_assoc($result)) {
        $goods[]  = $row;
        
    }
    return $goods;
}


// увеличивает просмотры на 1
function likeUp($id) {
    $link = getDb();
    mysqli_query($link, "UPDATE gallery SET pop = pop+ 1 WHERE id={$id}");
}
// получает 1 товар
function getSinglle($id, $table) {
    $link = getDb();
    $result = mysqli_query($link, "SELECT * FROM $table WHERE id={$id}");
    $param = mysqli_fetch_assoc($result);
    return $param;
}


//Функция, возвращает текст шаблона $page с подстановкой переменных
//из массива $params, содержимое шабона $page подставляется в
//переменную $content главного шаблона layout для всех страниц
function render($page, $params = []) {
    $content = renderTemplate(LAYOUTS_DIR . 'main', [
        'content' => renderTemplate($page, $params),
        'quantityAll' => quantityCart()
        ]);
    return $content;
}

// Считает колличество товаров в корзине
function quantityCart() {
    $cart = getGoods('cart');
        $quantityAll = (int)0;
        for ($i = 0; $i < count($cart); $i++) {
            $quantityAll += (int)$cart[$i]['quantity'];
        }
        return $quantityAll;
}


//Функция возвращает текст шаблона $page с подставленными переменными из
//массива $params, просто текст
function renderTemplate($page, $params = []) {
    
    
  ob_start();
  if(!is_null($params)) {
    extract($params);
}

   $fileName = TEMPLATES_DIR . $page . ".php";
   if(file_exists($fileName)) {
    include $fileName;
   } else {
       exit("страница {$fileName} не существует!!");
   }
   
  return ob_get_clean();
}
// создаёт уменьшенную копию и загружает его в $save
function create_thumbnail($path, $save) {
    $info = getimagesize($path);

    $width = $info[0];
    $height = $info[1];

    $percent = 0.5;

    $newWidth = $width * $percent;
    $newHeight = $height * $percent;
   
    //imagecreatetruecolor — Создание нового полноцветного изображения
    $thumb = imagecreatetruecolor($newWidth, $newHeight);
    //imagecreatefromjpeg — Создает новое изображение из файла или URL

    //В зависимости от расширения картинки вызываем соответствующую функцию
	if ($info['mime'] == 'image/png') {
		$src = imagecreatefrompng($path); //создаём новое изображение из файла
	} else if ($info['mime'] == 'image/jpeg') {
		$src = imagecreatefromjpeg($path);
	} else if ($info['mime'] == 'image/gif') {
 		$src = imagecreatefromgif($path);
	} else {
		return false;
    }
    
    //imagecopyresized — Копирование и изменение размера части изображения
    imagecopyresampled($thumb, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    imagejpeg($thumb, $save);
};