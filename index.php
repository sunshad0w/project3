<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();

require_once __DIR__ . "./DbSimple/Generic.php";
require_once __DIR__ . "./CONST.php";
require_once __DIR__ . "./Spectacle.php";
$DB = DbSimple_Generic::connect(BD);

//$DB->setLogger('myLogger');
function myLogger($db, $sql)
{
  // Находим контекст вызова этого запроса.
  $caller = $db->findLibraryCaller();
  $tip = "at ".@$caller['file'].' line '.@$caller['line'];
  // Печатаем запрос (конечно, Debug_HackerConsole лучше).
  echo "<xmp title=\"$tip\">"; 
  print_r($sql); 
  echo "</xmp>";
}

 $s =new Spectacle($DB);
 $s->addSpectacle("Гамлет"); 
 $s->addRole("Гамлет", "Гамлет");
 $s->addRole("Гамлет", "Клавдий");
 $s->addRole("Гамлет", "Полоний");
 $s->addRole("Гамлет", "Горацио");
 
 $s->addSpectacle("Евгений Онегин"); 
 $s->addRole("Евгений Онегин", "Онегин");
 $s->addRole("Евгений Онегин", "Татьяна");
 $s->addRole("Евгений Онегин", "Ольга");
 $s->addRole("Евгений Онегин", "Ленский");
 
 
 
 $roles = $s->spectacleList();
 if ($roles !== 0)
 {    
 echo '
    <h2>
        Выберите спектакль:
    </h2><br>
    <form method="post" action="index.php"><br>
    ';
//    printf($roles);

    foreach ($roles as $spectacle_name) {
        printf('<input type="submit" name="spectacle" value="%s"><br>', $spectacle_name);
    }


    echo '</form>';
 }

if (isset($_POST['spectacle'])){
 //   printf(': '.$id_spectacle);
    //die(': '.$id_spectacle);  
    $spectacle_name = $_POST['spectacle'];
    $role = $s->checkId($spectacle_name, session_id());
    if ($role !== 0){
        printf('Your role in this spectacle: '.$role.'<br>');
    }
    else
    {
    $roles_id = $s->selectFreeRolesId($_POST['spectacle']);
    switch ($roles_id){
        case 0:
            echo 'all roles busy<br>';
            break;
        case -1:
            echo 'spectacle not exist<br>';
            break;
        default:
            $role_id = $roles_id[rand(0,count($roles_id)-1)];
            $role_name = $DB->selectCell('SELECT role FROM roles WHERE id = ?',$role_id);
            printf('Your role in this spectacle set to: '.$role_name.'<br>');
            $s->setUserToRole(session_id(), $role_id);
        }
    }
    
    /*foreach ($roles as $role){
        printf($role."<br>");
    }*/
}
 
//echo '<a href="./register.php">register</a>';
?>