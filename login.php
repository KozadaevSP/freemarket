<?php

$email;
$password;
$errors = array();

function testInput($data){
    $data = trim($data); //удаление символов из начала и конца строки
    $data = stripcslashes($data);//удаление экранирующих слэшей
    $data = htmlspecialchars($data);//преобразование специальных символов
    return $data;
}

//возвращает id пользователя с таким email, если такой email есть в базе иначе FALSE\empty
function checkUserExist($email){
    include 'dbConfig.php';
    $id = NULL;
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_NUM);
    $result = $stmt->fetch();
    if($result){
     $id = $result[0];    
    }
    else{
     $id = NULL;
    }
    
    $stmt = NULL;
    $pdo = NULL;
    
    return $id;
}

function checkInputErrors(){
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        
        if(empty($_POST["email"])){
            $errors[] = "Вы не ввели E-mail<br>";   
        }else{
            $email = testInput($_POST["email"]); 
            $id = checkUserExist($email);
            echo "<br> CURRENT id =",$id;
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $errors[] = "Введите настоящий E-mail<br>";
            }elseif(!checkUserExist($email)){
                $errors[] = "Пользователь с таким E-mail не найден<br>";    
            } 
        }
        
        if(empty($_POST["password"])){
            $errors[] = "Вы не ввели пароль<br>";
        }else{
            $password = testInput($_POST["password"]);
            if(mb_strlen($password)<8){
                $errors[] = "Длина пароля должна быть 8 или больше символов<br>";
            }elseif (!checkPasswords($password, $id)) {
                $errors[] = "Неверный пароль<br>";
                
            }
        }              
    }
    return $errors;
}

function checkPasswords($password, $id){
    include 'dbConfig.php';
    
    $stmt = $pdo->prepare("SELECT passwordHash FROM users WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_NUM);
    $result = $stmt->fetch();

    if(password_verify($password, $result[0])){
        setcookie("userId", $id);
        return TRUE;
    }else{
        return FALSE;
    }
}



function sessionWork($errors){
    session_start();
    
    if(empty($errors)){
        unset($_SESSION["errors"]);
        session_destroy();
        header("Location: cabinetUser.html");

    }else{
        $_SESSION["errors"] = implode($errors);
        $nextPage = $_SERVER["HTTP_REFERER"];
        header("Location: $nextPage");
    }
    
}


$errors = checkInputErrors();
sessionWork($errors);
