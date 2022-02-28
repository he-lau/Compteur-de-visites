
<?php
/*
    Réalisation d’un compteur de visites

*/
session_start();

try {

  $pdo = new PDO('mysql:host=localhost;dbname=projet1', 'root', '');

  $ip = $_SERVER['REMOTE_ADDR'];

  $nbre_co = $pdo -> prepare("SELECT COUNT(*) AS exist FROM connectes WHERE ip = :ip");

  $nbre_co -> execute(
    [':ip' => $ip ]
  );

  $result = $nbre_co->fetch(PDO::FETCH_ASSOC);

  //echo $result["exist"];


  // si il y a une itération
  if($result["exist"])
    {
      echo"$ip exist in database."."<br>";

      $update = $pdo -> prepare("UPDATE connectes SET timestamp = UNIX_TIMESTAMP() WHERE ip = :ip");
      $update -> execute([':ip' => $ip ]);

    }
  else
    {
      echo"$ip not exist in database."."<br>";

      $insert = $pdo -> prepare("INSERT INTO connectes VALUES (:ip,UNIX_TIMESTAMP())");
      $insert -> execute([':ip'=>$ip]);

    }
  }
  catch(PDOException $e){
    printf("Échec de la connexion : %s\n", $e->getMessage());
    exit();
  }

    /*
        Supprimer toutes les IP avec timestamp > 5 min (300 s)
    */

    $query_delete_300s = $pdo -> prepare("DELETE FROM connectes WHERE timestamp < UNIX_TIMESTAMP() - 300");
    $query_delete_300s -> execute();

    /*
        Tuples restants : nombre de visiteurs actuellement connectés
    */

    $query_users = $pdo -> prepare("SELECT COUNT(*) AS users_count FROM connectes");
    $query_users -> execute();

    $result = $query_users -> fetch(PDO::FETCH_ASSOC);

    echo "Visiteurs actuellement connectés : " . $result['users_count'] ."<br>";



?>
