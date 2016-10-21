<?php

//В процессе наполнения, 

function testInput($data){
    $data = trim($data);
    $data = stripcslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


$uploaddir = 'photos/advertPhotos/';
$uploadfile = $uploaddir . basename($_FILES['picture']['name']);

if (move_uploaded_file($_FILES['picture']['tmp_name'], $uploadfile)) {
    echo "Файл корректен и был успешно загружен.\n";
} else {
    $errors = "";
}

echo 'Некоторая отладочная информация:';
print_r($_FILES);

print "</pre>";