<?php

require_once '../model/bdd.php';
include './data/header.php';

if (isset($_GET['action'])) {
  switch ($_GET['action']) {
    case 0:
      search($bdd);
      break;
    case 1:
      list_($bdd);
      break;
  }
} else {
  header('Location: ./');
  exit();
}

function search()
{
  if (isset($_GET["text"])) {
    if ($_GET["text"] == "") {
      exit();
    } else {
      $nom = $_GET["text"];
    }

    if (isset($_GET['id'])) {
      $bdd = new Bdd();
      $bdd->filter($nom, $_GET['id']);
    }
  }
}

function list_()
{
  if (isset($_GET['id'])) {
    $bdd = new Bdd();
    $bdd->show_users_inscript($_GET['id']);
  }
}
