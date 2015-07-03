<?php
 
    // Show PHP errors (during development only)
    error_reporting(E_ALL | E_STRICT);
    ini_set("display_errors", 2);
 
    // Create a Mongo conenction
    $mongo = new Mongo("mongodb://localhost");
 
    // Choose the database and collection
    $db = $mongo->my_db_name;
    $coll = $db->my_collection_name;
 
    // Same a document to the collection
    $coll->save(array(
        "name" => "Jack Sparrow",
        "age" => 34,
        "occupation" => "Pirate"
    ));
 
    // Retrieve the document and display it
    $item = $coll->findOne();
 
    echo "My name is " . $item['name'] . ". I am " . $item['age'] . " years old and work full-time as a " . $item['occupation'] . ".";
?>