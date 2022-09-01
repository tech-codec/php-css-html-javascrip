<?php
    require("./admin/Database.php");
    function getId(){
        if(!empty($_GET["id"])){
            $id = VerifyInput($_GET["id"]);
            return $id;
        }
    }
    function getBool(){
        if(!empty($_GET["id"])){
            return true;
        }else{
            return false;
        }
    }
    $id = getId();
    $val = getBool();
    function verifyInput($data){
        $data = trim($data);
        $data = stripcslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    function getAllcategories(){
        $pdo = Database::connect();
        $statement = $pdo->query("SELECT *FROM categories");
        $rescat = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $rescat;
    }

    function getAllItemsBycat(){
        if(!empty($_GET["id"])){
            $id = VerifyInput($_GET["id"]);
            $pdo = Database::connect();
            $statement = $pdo->prepare("SELECT *FROM items INNER JOIN categories ON categories.id = items.category WHERE items.category=?");
            $statement->execute([$id]);
            $res = $statement->fetchAll(PDO::FETCH_ASSOC);
            Database::disconnect();
            return $res;
        }else{
            $pdo = Database::connect();
            $statement = $pdo->query("SELECT *FROM items");
            $res = $statement->fetchAll(PDO::FETCH_ASSOC);
            Database::disconnect();
            return $res;
        }
    }

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Burger Code</title>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
        <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
        <link rel="stylesheet" href="css/styles.css">
    </head>
    <body>
        <div class="container site">
           
            <h1 class="text-logo"><span class="bi-shop"></span> Burger Code <span class="bi-shop"></span></h1>
            
            <nav>
                <ul class="nav nav-pills" role="tablist">
                    <?php foreach(getAllcategories() as $categories): ?>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?= $categories["id"]==$id ?"active":""; ?>" href="index.php?id=<?= $categories["id"]; ?>"><?= $categories["name"]; ?></a>
                        </li>
                    <?php endforeach ?>
                </ul>
            </nav>
            <div class="tab-content">
                <div class="tab-pane active" id="tab1" role="tabpanel">
                    <div class="row">
                    <?php if($val==true):?>
                        <?php foreach(getAllItemsBycat() as $item): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="img-thumbnail">
                                        <img src="images/<?= $item["image"];?>" class="img-fluid" alt="...">
                                        <div class="price"><?= $item["price"];?></div>
                                        <div class="caption">
                                            <h4><?= $item["name"];?></h4>
                                            <p><?= $item["description"];?></p>
                                            <a href="#" class="btn btn-order" role="button"><span class="bi-cart-fill"></span> Commander</a>
                                        </div>
                                    </div>
                                </div>
                        <?php endforeach ?>
                     <?php else: ?>
                        <?php foreach(getAllItemsBycat() as $item): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="img-thumbnail">
                                    <img src="images/<?= $item["image"];?>" class="img-fluid" alt="...">
                                    <div class="price"><?= $item["price"];?></div>
                                    <div class="caption">
                                        <h4><?= $item["name"];?></h4>
                                        <p><?= $item["description"];?></p>
                                        <a href="#" class="btn btn-order" role="button"><span class="bi-cart-fill"></span> Commander</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    <?php endif?>
                    </div>
                </div>

            </div>
    </body>
</html>