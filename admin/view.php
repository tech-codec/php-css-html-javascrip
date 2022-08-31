<?php

    require("Database.php");

    function verifyInput($data){
        $data = trim($data);
        $data = stripcslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    if(!empty($_GET["id"]) ){
        $id = verifyInput($_GET["id"]);
        $pdo = Database::connect();
        $statement = $pdo->prepare("SELECT items.id, items.name, items.image, items.description, items.price, categories.name AS category
        From items 
        LEFT JOIN categories ON items.category = categories.id
        WHERE items.id =?");

        $statement->execute([$id]);

        $res = $statement->fetch(PDO::FETCH_ASSOC);

        Database::disconnect();
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
        <link rel="stylesheet" href="../css/styles.css">
    </head>
    
    <body>
        <h1 class="text-logo"><span class="bi-shop"></span> Burger Code <span class="bi-shop"></span></h1>
        <div class="container admin">
            <div class="row">
                <div class="col-md-6">
                    <h1>Voir un Item</h1>
                    <br>
                    <form>
                        <div class="form-group">
                            <label class="form-label">Nom: <?= $res["name"];?></label>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Description: <?= $res["description"];?></label>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Prix: <?=number_format((float)$res["price"],2,'.',' ') ;?>$</label>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Categorie: <?= $res["category"];?></label>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Name image: <?= $res["image"];?></label>
                        </div>
                    </form>
                    <br>
                    <div class="form-action">
                        <a class="btn btn-primary" href="index.php"><span class="bi bi-arrow-left"></span> Retour</a>
                    </div>
                </div>
                <div class="col-md-6 site">
                    <div class="img-thumbnail">
                        <img src="../images/<?= $res["image"];?>" class="img-fluid" alt="...">
                        <div class="price"><?=number_format((float)$res["price"],2,'.',' ') ;?>$</div>
                        <div class="caption">
                            <h4><?= $res["name"];?></h4>
                            <p><?= $res["description"];?></p>
                            <a href="#" class="btn btn-order" role="button"><span class="bi-cart-fill"></span> Commander</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

