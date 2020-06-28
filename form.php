<?php
/*
Plugin Name: souilmi Plugin
Plugin URI: https://github.com/elmahdi-souilmi/C_plugin
Description: This is my first attempt on writing a custom Plugin
Version: 1.0.0
Author: el mahdi souilmi
License: GPLv2 or later
Text Domain: souilmi-plugin
 */

//Creation de la connection avec la base de donné de wordpress
require_once ABSPATH . 'wp-config.php';
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysqli_select_db($conn, DB_NAME);

//Fonction de la creation d'une nouvelle table pour le stockage des informations de la formulaire
function newTable()
{

    global $conn;

    $sql = "CREATE TABLE infos(id int NOT NULL PRIMARY KEY AUTO_INCREMENT, firstname varchar(255) NOT NULL, lastname varchar(255) NOT NULL, email varchar(255) NOT NULL, subj varchar(255) NOT NULL, msg varchar(255) NOT NULL)";
    $res = mysqli_query($conn, $sql);
    return $res;
}

//Creation du Table si la connection est établie
if ($conn == true) {

    newTable();
}

//Fonction pour laisser ou supprimer des champs du formulaire
function form($atts)
{
    $prenom = "";
    $nom = "";
    $mail = "";
    $sujet = "";
    $msg = "";

    extract(shortcode_atts(
        array(
            'firstname' => 'true',
            'lastname' => 'true',
            'email' => 'true',
            'subject' => 'true',
            'message' => 'true',

        ), $atts));

    if ($firstname == "true") {
        $prenom = '<label>First name:</label><input type="text" name="fname" required>';
    }

    if ($lastname == "true") {
        $nom = '<label>Last name:</label><input type="text" name="lname" required>';
    }

    if ($email == "true") {
        $mail = '<label>Email:</label><input type="email" name="email" required>';
    }
    if ($subject == "true") {
        $sujet = '<label>Subject:</label><input type="text" name="subject" required>';
    }

    if ($message == "true") {
        $msg = '<label>Message:</label><textarea name="msg"></textarea>';
    }

    echo '<form method="POST"  >' . $prenom . $nom . $mail . $sujet . $msg . '<input style="margin-top : 20px;" value="Send" type="submit" name="send"></form>';
}

//Shortcode du plugin
add_shortcode('Form', 'form');

// Fonction d'envoi des informations au base de donnée
function sendToDB($fname, $lname, $email, $subject, $msg)
{
    global $conn;

    $sql = "INSERT INTO infos(firstname,lastname,email,subj, msg) VALUES ('$fname','$lname','$email','$subject','$msg')";
    $res = mysqli_query($conn, $sql);

    return $res;
}

//L'envoi des informations au base de donnée
if (isset($_POST['send'])) {

    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $msg = $_POST['msg'];

    sendToDB($fname, $lname, $email, $subject, $msg);

}

add_action("admin_menu", "addMenu");
function addMenu()
{
    add_menu_page("Contact Form", " Contact Form", 4, " Contact Form", "adminMenu");
}

function adminMenu()
{
    echo <<< EOD
    <div style="font-size : 20px; display : flex; flex-direction : column;">
    <center><h1 style="color:red; font-family : roboto;">
     Contact Form
    </h1></center>

    <h3>
      This plugin generate a contact form with 5 fields.
    </h3>

    <h4>
      This contact form fields :
    </h4>
    <ol>
      <li>firstname</li>
      <li>lastname</li>
      <li>email</li>
      <li>subject</li>
      <li>message</li>
    </ol>
    <h3>
      Use The shortcode [Form] in your page to generate the contact form
    </h3>

    <h3>
      If you want to remove any field just add nameofthefield="false" to the shortcode
    </h3>
    <h4>Example:</h4>
    <p style="font-size : 20px;">
      if you want to remove the last name field use the shortcode [Form lastname="false"] <br>
      you can remove more than one field in the same form <br>
      [Form lastname="false" email="false"] to remove both last name and email fields.
    </p>



  </div>
EOD;
}
