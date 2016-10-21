<?php

$errors[] = NULL;
global $name;
global $email;
global $password;
global $passwordCheck;

function testInput($data){
    $data = trim($data); //удаление символов из начала и конца строки
    $data = stripcslashes($data);//удаление экранирующих слэшей
    $data = htmlspecialchars($data);//преобразование специальных символов
    return $data;
}

//проверка на наличие существующего пользователя с переданным емэйлом
function checkUserExist($email){
    include 'dbConfig.php';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $result = $stmt->fetchObject();
    $stmt = NULL;
    $pdo = NULL;
    if(empty($result)){
        return FALSE;
    }
    else{
        return TRUE;
    }
}


//переписать функцию под универсальную работоспособность, возможно, изменить наименование Айди в БД
function getMaxId(){
    include 'dbConfig.php';

    //через bindParam отказывается работать
    $stmt = $pdo->prepare("SELECT MAX(id) FROM users");
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_NUM);
    $result = $stmt->fetch();
    
    
    return $result[0];    
}

//добавить пользователя в БД
function addUser(){
    include 'dbConfig.php';
    
    $name = testInput($_POST["name"]);
    $email = testInput($_POST["email"]);
    $password = testInput($_POST["password"]);
    
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $id = getMaxId();
    $id++;
    $stmt = $pdo->prepare("INSERT INTO users (id, name, email, passwordHash) VALUES (:id, :name, :email, :hash)");
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':hash', $passwordHash);
    $stmt->execute();
    
    setcookie("userId", $id,time()+3600);
}

function checkInputErrors(){
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(empty($_POST["name"])){
            $errors[] = "Вы не ввели имя<br>";
        }else{
            $name = testInput($_POST["name"]);
        }
        
        if(empty($_POST["email"])){
            $errors[] = "Вы не ввели E-mail<br>";   
        }else{
            $email = testInput($_POST["email"]);       
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $errors[] = "Введите настоящий E-mail<br>";
            }
            elseif(checkUserExist($email)==TRUE){
                $errors[] = "Пользователь с таким E-mail уже существует<br>";    
            } 
        }
        
        if(empty($_POST["password"])){
            $errors[] = "Вы не ввели пароль<br>";
        }else{
            $password = testInput($_POST["password"]);
            if(mb_strlen($password)<8){
                $errors[] = "Длина пароля должна быть 8 или больше символов<br>";
            }
        }
        
        if(empty($_POST["passwordCheck"])){
            $errors[] = "Вы не ввели подтверждение пароля<br>";
        }else{
            $passwordCheck = testInput($_POST["passwordCheck"]);
        }
        
        if(strcmp($password, $passwordCheck)!= 0){
            $errors[] = "Пароли не совпадают<br>";
        }                            
    }
    return $errors;
}

function sessionWork($errors){
    session_start();
    
    if(empty($errors)){
        unset($_SESSION["errors"]);
        session_destroy();
        addUser();
        header("Location: main.html");
    }else{
        $_SESSION["errors"] = implode($errors);
        $nextPage = $_SERVER["HTTP_REFERER"];
        header("Location: $nextPage");
    }
    
}





$errors = checkInputErrors();
sessionWork($errors);

    


