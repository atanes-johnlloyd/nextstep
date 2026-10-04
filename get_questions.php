<?php
header('Content-Type: application/json');
$host = "localhost"; // your DB host
$user = "root";      // your DB username
$pass = "";          // your DB password
$db   = "nextstep_db";  // your DB name

$conn = new mysqli($host,$user,$pass,$db);
if($conn->connect_error){
    die(json_encode(["error"=>"Database connection failed"]));
}

// Fetch all questions
$questions_sql = "SELECT * FROM questions where field_id=1";
$q_res = $conn->query($questions_sql);

$questions = [];

while($row = $q_res->fetch_assoc()){
    $qid = $row['id'];
    
    // fetch affinities for this question
    $aff_sql = "SELECT program_id, weight FROM question_program_weights WHERE question_id = $qid";
    $aff_res = $conn->query($aff_sql);

    $affinities = [];
    while($aff = $aff_res->fetch_assoc()){
        $affinities[$aff['program_id']] = floatval($aff['weight']);
    }
    
    $questions[] = [
        "id" => intval($row['id']),
        "text" => $row['question'],
        "affinities" => $affinities
    ];
}

echo json_encode($questions);
$conn->close();
?>