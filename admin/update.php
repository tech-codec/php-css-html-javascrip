<?php

    require("Database.php");

    function verifyInput($data){
        $data = trim($data);
        $data = stripcslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function getIdItem(){
        if(!empty($_GET["id"]) ){
            $id = verifyInput($_GET["id"]);
        }
        return $id;
    }
    

    function getAllcategories(){
        $pdo = Database::connect();
        $statement = $pdo->query("SELECT *FROM categories");
        
        $rescat = $statement->fetchAll(PDO::FETCH_ASSOC);
    
        Database::disconnect();
    
        return $rescat;
    }


    $array = array(
        "name"=>"", 
        "description"=>"",
        "price"=>"", 
        "image"=> "",
        "category"=>"", 
        "nameError"=>"",
        "descriptionError"=>"",
        "priceError"=>"",
        "phoneError"=>"",
        "imageError"=>"",
        "categoryError"=>"",
        "catname"=>"",
        "isSuccess"=>false
    );
    


    function getItem(){
            $id = getIdItem();
            $pdo = Database::connect();
            $statement = $pdo->prepare("SELECT items.id, items.name, items.image, items.description, items.category, items.price, categories.name AS catname
            From items 
            LEFT JOIN categories ON items.category = categories.id
            WHERE items.id =?");
    
            $statement->execute([$id]);
    
            $res = $statement->fetch(PDO::FETCH_ASSOC);
            /*$array["name"] = $res['name'];
            $array["description"] = $res["description"];
            $array["price"] = $res["price"];
            $array["image"] = $res["image"];
            $array["category"] = $res["category"];
            $array["catname"] = $res["catname"];
            Database::disconnect();*/
            return $res;
    }

    if(!empty($_POST)){
            $array["name"] = verifyInput($_POST['name']);
            $array["description"] = verifyInput($_POST["description"]);
            $array["price"] = verifyInput($_POST["price"]);
            $array["image"] = verifyInput($_FILES["image"]["name"]);
            $imagePath = '../images/'. basename($array["image"]);
            $imageExtension = pathinfo($imagePath,PATHINFO_EXTENSION);
            $array["category"] = verifyInput($_POST["category"]);
            $array["isSuccess"] = true;
            $isImageUpdated = false;
            $isUploadSuccess = false;
            $id = getIdItem();
            if(empty($array["name"])){
                $array["nameError"] = "le nom n'est pas renseigné";
                $array["isSuccess"] = false;
            }
            if(empty($array["description"])){
                $array["descriptionError"] = "la description n'est pas renseignée";
                $array["isSuccess"] = false;
            }
            if(empty($array["price"])){
                $array["priceError"] = "le prix n'est pas renseigné";
                $array["isSuccess"] = false;
            }
            if(empty($array["category"])){
                $array["categoryError"] = "la catégorie n'est pas renseignée";
                $array["isSuccess"] = false;
            }

            if(empty($array["image"])) {
                $array["imageError"] = "l'image n'est pas renseignée";
                $isImageUpdated = false;
            }
            else {
                $isImageUpdated = true;
                $isUploadSuccess = true;
                if($imageExtension != "jpg" && $imageExtension != "png" && $imageExtension != "jpeg" && $imageExtension != "gif" ) {
                    $array["imageError"]= "Les fichiers autorises sont: .jpg, .jpeg, .png, .gif";
                    $isUploadSuccess = false;
                }
                if(file_exists($imagePath)) {
                    $array["imageError"] = "Le fichier existe deja";
                    $isUploadSuccess = false;
                }
                if($_FILES["image"]["size"] > 500000) {
                    $array["imageError"] = "Le fichier ne doit pas depasser les 500KB";
                    $isUploadSuccess = false;
                }
                if($isUploadSuccess) {
                    if(!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
                        $array["imageError"] = "Il y a eu une erreur lors de l'upload";
                        $isUploadSuccess = false;
                    } 
                } 
            }
            
            if (($array["isSuccess"] && $isImageUpdated && $isUploadSuccess) || ($array["isSuccess"] && !$isImageUpdated)) { 
                $db = Database::connect();
                if ($isImageUpdated) {
                    $statement = $db->prepare("UPDATE items  set name = ?, description = ?, price = ?, category = ?, image = ? WHERE id = ?");
                    $statement->execute(array($array["name"],$array["description"],$array["price"],$array["category"],$array["image"],$id));
                } else {
                    $statement = $db->prepare("UPDATE items  set name = ?, description = ?, price = ?, category = ? WHERE id = ?");
                    $statement->execute(array($array["name"],$array["description"],$array["price"],$array["category"],$id));
                }
                Database::disconnect();
                header("Location: index.php");
                
            } else if ($isImageUpdated && !$isUploadSuccess) {
                $db = Database::connect();
                $statement = $db->prepare("SELECT * FROM items where id = ?");
                $statement->execute(array($id));
                $item = $statement->fetch();
                $array["image"] = $item['image'];
                Database::disconnect();
               
            }
    
            echo json_encode($array);
        }else{
            $array = getItem();
            //var_dump(getItem());
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
                    <h1>Modifier un Item</h1>
                    <br>
                    <form method="POST" action="" enctype="multipart/form-data" role="form">
                        <div class="form-group">
                        <label for="name" class="col-form-label">Name</label>
                            <input type="text" name="name"  class="form-control" id="name" placeholder="Entrer un nom" value="<?= $array["name"];?>">
                            <span class="help-inline"><?=!empty($array["nameError"])?$array["nameError"]:""; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="descrition" class="col-form-label">Description</label>
                            <input type="text" name="description" class="form-control" id="descrition" placeholder="Entrer une description" value="<?= $array["description"];?>">
                            <span class="help-inline"><?=!empty($array["descriptionError"])?$array["descriptionError"]:""; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="price" class="col-form-label">Price</label>
                            <input type="text" name="price"  class="form-control" id="price" placeholder="Entrer un prix" value="<?=number_format((float)$array["price"],2,'.',' ') ;?>">
                            <span class="help-inline"><?=!empty($array["priceError"])?$array["priceError"]:""; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="category_name" class="col-form-label">Catégorie</label>
                            <select class="form-select" id="category_name" name="category" aria-label="select example">
                                <option value="<?=$array["category"];?>" selected="selected"> <?=$array["catname"];?> </option>
                                <?php foreach(getAllcategories() as $categorie):?>
                                    <?php if($categorie["id"]!=$array["category"]): ?>
                                        <option value="<?= $categorie["id"];?>"> <?=$categorie["name"];?> </option>
                                    <?php endif?>
                                <?php endforeach;?>
                                </select>
                                <span class="help-inline"><?=!empty($array["imageError"])?$array["imageError"]:""; ?></span>
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="image" class="col-form-label">Sélectionner une image</label>
                            <input type="file" id="image" class="form-control" name="image">
                            <span class="help-inline"></span>
                        </div>
                        <br>
                        <div class="form-action">
                            <a class="btn btn-primary" href="index.php"><span class="bi bi-arrow-left"></span> Retour</a>
                            <button class="btn btn-success" type="submit"><span class="bi bi-check"></span> Modifier</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6 site">
                    <div class="img-thumbnail">
                        <img src="../images/<?= $array["image"];?>" class="img-fluid" alt="...">
                        <div class="price"><?=number_format((float)$array["price"],2,'.',' ') ;?>$</div>
                        <div class="caption">
                            <h4><?= $array["name"];?></h4>
                            <p><?= $array["description"];?></p>
                            <a href="#" class="btn btn-order" role="button"><span class="bi-cart-fill"></span> Commander</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

