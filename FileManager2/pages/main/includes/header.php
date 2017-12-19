<?php
/*
 *	Made by SirHyperNova
 *  NamelessMC version 2.0.0-pr3
 *
 *  License: MIT
 *
 *  File Manager Module
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?> &bull; <?php echo SITE_NAME; ?></title>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>
    <body>
        <nav class="<?php echo $color; ?><?php echo ($colorModifier!='normal'?' '.$colorModifier:null) ?>">
            <div class="nav-wrapper container">
                <a href="?route=/" class="brand-logo truncate"><?php echo SITE_NAME; ?></a>
                <ul class="right hide-on-med-and-down">
                    <li class="active"><a href="?route=/files">Files</a></li>
                </ul>
            </div>
        </nav>
        <main>