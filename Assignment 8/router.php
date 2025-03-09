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
    case 'lesson10':
    case 'lesson11':
    case 'lesson12':
    case 'final_project':
        include "$lessonName.php";
        break;
    default:
        include '404.html';
        break;
}