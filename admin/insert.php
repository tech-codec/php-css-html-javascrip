<?php
     require("Database.php");

     function getAllcategories(){
        $pdo = Database::connect();
        $statement = $pdo->query("SELECT *FROM categories");
    
        $rescat = $statement->fetchAll(PDO::FETCH_ASSOC);

        var_dump($rescat);

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
        "message"=>"",
        "isSuccess"=>false
    );
    
        if(!empty($_POST)){
            $array["name"] = verifyInput($_POST['name']);
            $array["description"] = verifyInput($_POST["description"]);
            $array["price"] = verifyInput($_POST["price"]);
            $array["image"] = verifyInput($_FILES["image"]["name"]);
            $imagePath = '../images/'. basename($array["image"]);
            $imageExtension = pathinfo($imagePath,PATHINFO_EXTENSION);
            $isUploadSuccess = false;
            $array["category"] = verifyInput($_POST["category"]);
            $array["isSuccess"] = true;
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
                $array["isSuccess"] = false;
            }
            else {
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
            
            if($array["isSuccess"] && $isUploadSuccess) {
                $db = Database::connect();
                $statement = $db->prepare("INSERT INTO items (name, description, price, category, image) values(?, ?, ?, ?, ?)");
                $statement->execute(array($array["name"],$array["description"],$array["price"],$array["category"],$array["image"]));
                Database::disconnect();
                header("Location: index.php");
            }
    
            echo json_encode($array);
        }
    
        function verifyInput($var){
            $var = trim($var);
            $var = stripcslashes($var);
            $var = htmlspecialchars($var);
            return $var;
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
                <div class="col-md-12">
                    <h1>Ajouter un item</h1>
                    <br>
                    <form method="POST" action="" enctype="multipart/form-data" role="form">
                        <div class="form-group">
                        <label for="name" class="col-sm-2 col-form-label">Name</label>
                            <input type="text" name="name"  class="form-control" id="name" placeholder="Entrer un nom" value="<?=$array["name"]?>">
                            <span class="help-inline"><?=$array["nameError"]; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="descrition" class="col-sm-2 col-form-label">Description</label>
                            <input type="text" name="description" class="form-control" id="descrition" placeholder="Entrer une description" value="<?=$array["description"]?>">
                            <span class="help-inline"><?=$array["descriptionError"]; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="price" class="col-sm-2 col-form-label">Price</label>
                            <input type="text" name="price"  class="form-control" id="price" placeholder="Entrer un prix" value="<?=$array["price"]?>">
                            <span class="help-inline"><?=$array["priceError"]; ?></span>
                        </div>
                        <div class="form-group">
                            <label for="category_name" class="col-sm-2 col-form-label">Catégorie</label>
                            <select class="form-select" id="category_name" name="category" aria-label="select example">
                                <?php foreach(getAllcategories() as $categorie):?>
                                <option value="<?=$categorie["id"]; ?>"><?=$categorie["name"]; ?></option>
                                <?php endforeach?>
                            </select>
                            <span class="help-inline"><?=$array["categoryError"]; ?></span>
                        </div>
                        <br>
                        <div class="form-group">
                            <label for="image" class="col-sm-2 col-form-label">Sélectionner une image</label>
                            <input type="file" id="image" class="form-control" name="image">
                            <span class="help-inline"><?=$array["imageError"]; ?></span>
                        </div>
                        <br>
                        <div class="form-action">
                            <a class="btn btn-primary" href="index.php"><span class="bi bi-arrow-left"></span> Retour</a>
                            <button class="btn btn-success" type="submit"><span class="bi bi-check"></span> Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>

