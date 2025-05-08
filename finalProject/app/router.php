<?php

$lessonName = trim($_SERVER['PATH_INFO'], '/');

switch ($lessonName) {
    case 'lesson01':
    case 'lesson02':
    case 'lesson03':
    case 'lesson04':
    case 'lesson05':
    case 'lesson06':
    case 'lesson07':
    case 'lesson08':
    case 'lesson09':
    case 'lesson09_save_order':
    case 'check_database':
    case 'lesson10':
    case 'lesson11':
    case 'lesson12':
    case 'lesson12_save_order':
    case 'lesson13':
    case 'lesson13_save_order':
    case 'finalProject':
    case 'finalProjectSaveOrder':
        include "$lessonName.php";
        break; 
    default:
        include '404.html';
        break;
}