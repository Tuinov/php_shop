<?php foreach($result as $value):?>
        
            <p><?=$value['name']?> шт:<?=$value['quantity']?></p>
                 
            <p>price:<?=$value['price']*$value['quantity']?> </p>
            <a href="?page=cart&deleteId=<?=$value['id']?>">[x]Удалить</a>
          
<?php endforeach; ?>