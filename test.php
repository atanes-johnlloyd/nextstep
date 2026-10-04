<?php
session_start();
$logged_in = isset($_SESSION['user_id']);
$user_id = $logged_in ? $_SESSION['user_id'] : 0;

// Check if user has results already saved
$show_results = false;
if($logged_in){
    include 'db.php';
    $stmt = $conn->prepare("SELECT COUNT(*) FROM quiz_results WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);  // bind the user ID as integer
    $stmt->execute();
    $stmt->bind_result($count);        // bind the first column (COUNT(*)) to $count
    $stmt->fetch();
    $stmt->close();

    $show_results = $count > 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>NextStep Test</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="test.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .result-card { max-width: 920px; margin: 12px auto; padding: 14px; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.06); background: #fff; }
    .result-title { font-weight: 700; margin-bottom:6px; }
    .result-sub { color: #333; margin-bottom:6px; }
    .btn-view { background: #070069; color: white; border:none; padding: 8px 12px; border-radius: 8px; text-decoration:none; }
    .option { display:flex; align-items:center; margin:6px 0; cursor:pointer; }
    .option input { margin-right: 10px; }
    .btn-next { background:#070069; color:white; border:none; padding:8px 16px; border-radius:6px; }
  </style>
</head>
<body class="footprints-bg">
  <div class="header">NextStep Future Program Test</div>

  <div class="test-card">
    <div class="d-flex navbar">
      <div>
        <img class="logo" src="assets/images/Logo.png" alt="Logo">
        <a href="index.php" class="navbar-brand">NextStep</a>
      </div>
      <div class="progress">
        <div class="progress-bar" id="progressBar" style="width:0%"></div>
      </div>
    </div>

    <div id="quizSection">
      <div class="question" id="questionText">Loading question...</div>
      <div class="options" id="optionsContainer">
        <div class="likert-line"></div>
      </div>
      <div class="text-center mt-4">
        <!-- Previous & Next buttons will be dynamically handled -->
      </div>
    </div>

    <div id="resultsSection" style="display:none;">
      <h3 style="text-align:center; margin-top: 8px;">Your Top 3 Course Matches</h3>
      <div id="top3List" style="margin-top:14px;"></div>
      <div style="text-align:center; margin-top:12px;">
        <button id="retakeBtn" class="btn-next">Retake the Test</button>
      </div>
    </div>
  </div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  
const ANSWER_VALUE = {
  "strongly-agree": 2,
  "agree": 1,
  "neutral": 0,
  "disagree": -1,
  "strongly-disagree": -2
};

let questions = [];
const rawScores = {};
const maxScores = {};
let current = 0;
let isRetakeComplete = false;
let autoSubmitTimeout;
const DELAY_MS = 500; // 1 second

// Load questions from PHP
$.getJSON("get_questions.php", function(data){
    if (!data || data.length === 0) {
        $("#questionText").text("No questions found in database.");
        return;
    }

    questions = data;

    // initialize scores
    questions.forEach(q => {
    for (const [programId, weight] of Object.entries(q.affinities)) {
        rawScores[programId] = 0;
        // max possible points per question is 2 (strongly-agree) * weight
        maxScores[programId] = (maxScores[programId] || 0) + (2 * weight);
    }
});

    const loggedIn = <?php echo $logged_in ? 'true' : 'false'; ?>;
    const showResultsAlready = <?php echo $show_results ? 'true' : 'false'; ?>;
    const userId = <?php echo $user_id; ?>;

    if (loggedIn && showResultsAlready) {
    $("#questionText").text("Loading your saved results...");
    $.post('get_user_results.php', { user_id: userId }, function(data) {
        if (data.success && Array.isArray(data.results)) {
            top3 = data.results;
            showResults(data.results); // <-- pass saved results
        } else {
            Swal.fire("Error", "Failed to load saved results.", "error");
            renderQuestion(0);
        }
    }, 'json').fail(function() {
        Swal.fire("Error", "Error loading saved results.", "error");
        renderQuestion(0);
    });
} else {
    renderQuestion(0); // normal quiz flow
}
}).fail(function(err){
    $("#questionText").text("Error loading questions: " + err.statusText);
});

function renderQuestion(i){
  const q = questions[i];
  if (!q) return;
  
  $("#questionText").text(q.text);
  const optionLabels = ["strongly-agree","agree","neutral","disagree","strongly-disagree"];
  const optionText = ["Strongly Agree","Agree","Neutral","Disagree","Strongly Disagree"];
  
  $("#optionsContainer").html(optionLabels.map((val,idx)=>`
    <div class="option ${val}">
      <input type="radio" name="answer" id="${val}" value="${val}" ${q.selected===val ? "checked" : ""}>
      <label for="${val}"></label><p>${optionText[idx]}</p>
    </div>`).join('') + '<div class="likert-line"></div>');

  $("#progressBar").css("width", Math.round((i+1)/questions.length*100)+"%");

  // Previous button
  if($("#prevBtn").length === 0){
    $(".text-center").prepend('<button id="prevBtn" class="btn-next me-2">Previous</button>');
  }
  $("#prevBtn").prop("disabled", i===0);
  $("#prevBtn").off('click').on('click', ()=>{
    if(autoSubmitTimeout) clearTimeout(autoSubmitTimeout);
    if(current > 0){
      current--;
      renderQuestion(current);
    }
  });

  // Auto-submit after delay
  $("input[name='answer']").off('change').on('change', function(){
    const selectedVal = $(this).val();
    q.selected = selectedVal;
    if(autoSubmitTimeout) clearTimeout(autoSubmitTimeout);
    if(!isRetakeComplete){  // ✅ Add this guard
    autoSubmitTimeout = setTimeout(() => {
        calculateResults();
        current++;
        if (current < questions.length) {
            renderQuestion(current);
        } else {
            isRetakeComplete = true; // mark quiz complete
            showResults();            // call results AFTER finishing
        }
    }, DELAY_MS);
}

  });
}

function calculateResults() {
    // Reset rawScores
    for(const k in rawScores) rawScores[k] = 0;

    questions.forEach(q => {
        const val = ANSWER_VALUE[q.selected] ?? 0;
        for (const [programId, weight] of Object.entries(q.affinities)) {
            const contribution = Math.max(0, val) * weight;
            rawScores[programId] += contribution;
        }
    });
}


function showResults(savedResults = null, isRetake = false) {
    const results = [];
    let totalScore = 0;

    if(savedResults && Array.isArray(savedResults)) {
        // Use saved results from DB
        savedResults.forEach(r => {
            const pct = r.percentage ?? ((r.raw / r.max) * 100);
            totalScore += pct;
            results.push({ programId: r.programId, raw: r.raw, max: r.max, pct });
        });
    } else {
        // calculate from rawScores if user just finished quiz
        for (const programId of Object.keys(rawScores)) {
            const raw = rawScores[programId];
            const max = maxScores[programId] || 1;
            const pct = Math.max(0, Math.min(100, (raw / max) * 100));
            totalScore += pct;
            results.push({ programId, raw, max, pct });
        }
    }

    results.sort((a, b) => b.pct - a.pct);
    const top3 = results.slice(0, 3);

    $("#quizSection").hide();
    $("#resultsSection").show();
    const $list = $("#top3List");
    $list.empty();

    // Fetch program info from DB
    $.ajax({
        url: "get_programs.php",
        type: "POST",
        data: { ids: top3.map(r => r.programId) },
        dataType: "json",
        success: function(programs) {
            top3.forEach((r, idx) => {
                const program = programs.find(p => p.id == r.programId);
                const title = program ? program.title : "Unknown Program";
                const category = program ? program.category : "Unknown Category";

                $list.append(`
                    <div class="result-card">
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                            <div style="flex:1;">
                                <div class="result-title">${idx + 1}. ${title}</div>
                                <div class="result-sub">${category}<br>
                                    Match: ${r.pct.toFixed(1)}%
                                </div>
                                <div class="progress" style="height:10px; border-radius:6px; overflow:hidden;">
                                    <div class="progress-bar" role="progressbar" style="width:${r.pct}%; background:#070069;"></div>
                                </div>
                            </div>
                            <button class="btn-custom btn-view" data-program-id="${r.programId}">View Details</button>
                        </div>
                    </div>
                `);
            });

            $("#totalScore").text("Total score (sum of % across all courses): " + totalScore.toFixed(1));

            const loggedIn = <?php echo $logged_in ? 'true' : 'false'; ?>;
            const userId = <?php echo $user_id; ?>;

            if (loggedIn && isRetakeComplete) {
                // build all program results
                const allResults = [];
                for (const programId of Object.keys(rawScores)) {
                    const raw = rawScores[programId];
                    const max = maxScores[programId] || 1;
                    const pct = Math.max(0, Math.min(100, (raw / max) * 100));
                    allResults.push({ programId, raw, max, pct });
                }

                $.post('save_results.php', { user_id: userId, results: allResults }, function(res) {
                    if (res.success) {
                        if(!sessionStorage.getItem("resultsSaved")) {
                            Swal.fire("Success","Your results have been saved/updated!","success");
                            sessionStorage.setItem("resultsSaved", "true");
                        }
                    } else {
                        Swal.fire("Error", res.message || "Failed to save results","error");
                    }
                }, 'json');
            }

        },
        error: function () {
            Swal.fire("Error", "Unable to load program information.", "error");
        }
    });
}


$("#retakeBtn").click(()=>{ 
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you want to retake the quiz? Your previous results will be updated after you finish.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, retake',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if(result.isConfirmed){
            sessionStorage.removeItem("resultsSaved"); // ✅ reset alert flag

            if(autoSubmitTimeout) clearTimeout(autoSubmitTimeout); // ✅ Clear previous timer

            isRetakeComplete = false; // ✅ Reset completion
            current = 0;       
            for(const k in rawScores) rawScores[k] = 0;
            questions.forEach(q => q.selected = null);

            // Reset rawScores and maxScores
            for (const k in maxScores) maxScores[k] = 0;

            // Recalculate maxScores from scratch
            questions.forEach(q => {
                for (const [programId, weight] of Object.entries(q.affinities)) {
                    maxScores[programId] = (maxScores[programId] || 0) + (2 * weight);
                }
            });


            $("#resultsSection").hide();
            $("#quizSection").show();
            $("#progressBar").css("width","0%");
            renderQuestion(0);
        }
    });
});


$(document).on("click", ".btn-custom", function () {
  const programId = $(this).data("program-id");
  if (programId && programId !== 0) {
    window.location.href = `program_info.php?id=${programId}`;
  } else {
    Swal.fire("Info", "Program details not available for this course.", "info");
  }
});
</script>
</body>
</html>
