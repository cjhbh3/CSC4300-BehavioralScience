<!DOCTYPE html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="./src/styles.css">
  <script type="text/javascript">
    //Contributor for PHP: Skyeler Knuutila
    //Contributor for JS: Chris Barry

    var questionHelpButton, questionHelpPrompt, image_stim1, image_stim2, sound_stim1, sound_stim2, nextQuestionButton;
    var stims; // converts PHP array and stores in JS array of {stimID: name, stimType: type} objects
    var numStims; // used for total number of stims in database
    var blockList;
    var timer = 8; // number of seconds user has to answer
    var questionTimer;
    var clickTime = 0; // how long it takes user to click

    function onLoad() {
      //document.getElementById("title").innerHTML = timer; // remove when done testing
      questionHelpButton = document.getElementById("questionHelpButton");
      questionHelpButton.addEventListener("click", helpToolTip);
      questionHelpPrompt = document.getElementById("questionHelpPrompt");

      imageStim = document.getElementById("imageStim");
      soundStim = document.getElementById("soundStim");
      
      nextQuestionButton = document.getElementById("nextQuestionButton");
      nextQuestionButton.addEventListener("click", nextQuestionClicked);
      nextQuestionButton.innerHTML = "Start";
      getQuestionData();
      getNextComparison(0); // gets first comparison
    }

    function getQuestionData() {
      <?php
      session_start();
      $conn = new mysqli("us-cdbr-east-05.cleardb.net:3306", "b5541841c18a2e", "ee93a776", "heroku_8eb08016ed835ac");
      if (!$conn)
        die("Database Error." . mysqli_connect_error());
      
      //find current phase
      $userID = $_SESSION["userID"];
      $queryString = ("SELECT phaseID FROM user_T WHERE userID = $userID");
      $result =  mysqli_query($conn, $queryString);
      $userPH = $result->fetch_assoc() ?? -1;
      $userPH = $userPH['phaseID']; //userPH now stores the phase the user is on

      //Gets Phase Prompt
      $queryString = "SELECT prompt FROM phase_T WHERE phaseID = $userPH";
      $result=  mysqli_query($conn, $queryString);
      $prompt= $result->fetch_assoc();
      $prompt= $prompt['prompt'];  

      //create an array of blockID's from that phase 
      $blockList = array();
      $queryString = ("SELECT blockID FROM phaseBlock_T WHERE phaseID = $userPH ORDER BY blockOrder");
      $result =  mysqli_query($conn, $queryString);
      while ($row = mysqli_fetch_assoc($result)) {
        array_push($blockList, $row['blockID']);
      }

      //given blockID, return array of all stim and stimTypes
      //in form ["A1.png", "image", "B1.wav", "sound", .....]
      for ($j = 0; $j < sizeOf($blockList); $j++) {
        $blockID = $blockList[$j];

        //start with an array of trialID's
        $trialList = array(); //empty array
        $queryString = ("SELECT trialID FROM blockTrial_T WHERE blockID = $blockID ORDER BY trialOrder");
        $result =  mysqli_query($conn, $queryString);
        while ($row = mysqli_fetch_assoc($result)) {
          array_push($trialList, $row['trialID']);
        }

        //get array of stim and stimType by trial
        $stimList = array();
        for ($i = 0; $i <= sizeOf($trialList) - 1; $i++) {
          //$trialList[$i] is a string, we need an int
          $trialID = intval($trialList[$i]);
          $queryString = ("SELECT * FROM trial_T, stimuli_T WHERE trialID = $trialID AND stimIDOne = stimID OR trialID = $trialID AND stimIDTwo = stimID");
          $result =  mysqli_query($conn, $queryString);
          while ($row = mysqli_fetch_assoc($result)) {
            array_push($stimList, $row['stimID']);
            array_push($stimList, $row['stimtype']);
          }
        }
        // push out array here
        // pushes out to javascript code as "var stimListi = {data here, data here, data here};\n"
        // go to webpage, right click, view page source to view the output
        echo "\tvar block", $j, " = ", json_encode($stimList), "; \n";
      }
      ?>
    }

    function getNextComparison(index) {



      /*if (stims[index].stimType == "sound") {
        //soundStim.innerHTML += "<source src='https://behaviorsci-assets.s3.us-west-1.amazonaws.com/A1.wav' type='audio/wav'>";
        soundStim.src = "https://behaviorsci-assets.s3.us-west-1.amazonaws.com/" + stims[index].stimID + ".wav";
        console.log("Got sound file: ", soundStim.src);
      } else{ //stimType == "image"
        imageStim.src = "https://behaviorsci-assets.s3.us-west-1.amazonaws.com/" + stims[index].stimID + ".png";
        console.log("Got image file: ", imageStim.src);
      }*/
    }

    function helpToolTip() {
      if (questionHelpPrompt.style.display == "none")
        questionHelpPrompt.style.display = "flex";
      else // questionHelpPrompt.style.display == "flex"
        questionHelpPrompt.style.display = "none";
    }

    function nextQuestionClicked() {
      nextQuestionButton.style.visibility = "hidden";
      if(nextQuestionButton.innerHTML == "Start") {
        nextQuestionButton.innerHTML = "Next";
      }
      document.getElementById("boxMain").style.visibility = "visible";
      questionTimer = setInterval(checkTimer, 1000); // calls checkTimer every 1000 milliseconds (every 1 second)
    }

    function checkTimer() {
      if (timer == 1) {
        //document.getElementById("title").innerHTML = "TIMER DONE"; // remove when done testing
        clearInterval(questionTimer); // stops the timer
        document.getElementById("boxMain").style.visibility = "hidden"; // hide the main box
        nextQuestionButton.style.visibility = "visible";
        timer = 8;
        //getNextComparison();
      } else // timer != 0
      {
        timer--;

        //document.getElementById("title").innerHTML = timer; // remove when done testing
      }
    }

    function clicked() {
      //document.getElementById("title").innerHTML = "Button Clicked"; // remove when done testing
      clearInterval(questionTimer); // stops the timer
      document.getElementById("boxMain").style.visibility = "hidden"; // hide the main box
      nextQuestionButton.style.visibility = "visible";
    }
  </script>
  <title>Question</title>
</head>
<style>
  #body {
    background-color: #37111d;
  }

  #boxMain {
    visibility: hidden;
    margin: auto;
    border-radius: 5px;
    padding: 10px;
    box-sizing: content-box;
    width: 60%;
    color: white;
    margin-top: -130px;
    margin-left: 563px;
  }

  #clickButton {
    margin-left: 90px;
    padding: 20px;
    font-size: 40px;
    background-color: #6C2037;
    border: none;
    border-radius: 5px;
    color: #F0C975;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
    font-weight: 600;
  }

  #clickButton:hover {
    background-color: #F0C975;
    color: #6C2037;
  }

  #nextQuestionButton {
    margin-left: 664px;
    margin-top: 326px;
    padding: 20px;
    font-size: 40px;
    background-color: #6C2037;
    border: none;
    border-radius: 5px;
    color: #F0C975;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
    font-weight: 600;
  }

  #nextQuestionButton:hover {
    background-color: #F0C975;
    color: #6C2037;
  }


  #questionHelpPrompt {
    display: none;
  }
</style>

<body id="body" onload="onLoad()">
  <img id="questionHelpButton" src="../images/questionHelpButton.png" width="50" height="50">
  <div id="questionHelpPrompt">Insert question help here:<br>Line 2 <br>Line 3 <br></div><br>

  <a id="nextQuestionButton"></a>
  <div id="boxMain">
    <img id="imageStim"></img>
    <audio id="soundStim" src="" type="audio/wav" controls></audio><br>
    <button class="button" id="clickButton" alt="click" onclick="clicked()">Click</button>
  </div>
</body>

</html>