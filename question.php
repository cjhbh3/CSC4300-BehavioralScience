<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="./src/styles.css">
    <title>Question</title>
    <script type = "text/javascript">
        var questionHelpButton, questionHelpPrompt, image_stim1, image_stim2, sound_stim1, sound_stim2;
        var stims; // converts PHP array and stores in JS array of {stimID: name, stimType: type} objects
        var numStims; // used for total number of stims in database
        var fileName = "https://behaviorsci-assets.s3.us-west-1.amazonaws.com/"
        function onLoad()
        {
          questionHelpButton = document.getElementById("questionHelpButton");
          questionHelpButton.addEventListener("click", helpToolTip);
          questionHelpPrompt = document.getElementById("questionHelpPrompt");

          image_stim1 = document.getElementById("image_stim1");
          image_stim2 = document.getElementById("image_stim2");
          sound_stim1 = document.getElementById("sound_stim1");
          sound_stim2 = document.getElementById("sound_stim2");
          getUserData();
          getQuestionData(); // gets all question data from database
          getNextComparison(0); // gets next comparison
        }


        function getUserData()
        {
          console.log
          (
            <?php 
              session_start(); 
              $conn = new mysqli("us-cdbr-east-05.cleardb.net:3306", "b5541841c18a2e", "ee93a776", "heroku_8eb08016ed835ac"); 
              if (!$conn)
                  die("Database Error.".mysqli_connect_error());
              $userID = $_SESSION["userID"];
              $queryString = ("SELECT phaseID FROM user_T WHERE userID = $userID");
              $result =  mysqli_query($conn, $queryString);
              while ($row=mysqli_fetch_row($result))
                echo $row[0];	
		        ?>
          );
        }
        // get question data from database, convert PHP to JS and store
        // getQuestionData() written by Chris B & Nick Wood
        function getQuestionData()
        {
          <?php
            session_start();
            $conn = new mysqli("us-cdbr-east-05.cleardb.net:3306", "b5541841c18a2e", "ee93a776", "heroku_8eb08016ed835ac");
            if (!$conn)
                die("Database Error.".mysqli_connect_error());
            
            $queryString = ("SELECT stimID, stimType FROM stimuli_T"); // grab stim data from stimuli datatable
            $stimIDs = mysqli_query($conn, $queryString); // query with above info

            $i = 0; // incrementor variable
            while($row = mysqli_fetch_array($stimIDs)) // fetch data
            {
                $stims[$i] = array('stimID' => $row['stimID'], 'stimType' => $row['stimType']); // store values in PHP array
                $i++; // next index
            }
            $numOfStims = $stimIDs -> num_rows; // grab total # of rows in database

          ?>

          stims = <?php echo json_encode($stims); ?>; // converts PHP array and stores in JS array of {stimID: name, stimType: type} objects
          numStims = <?php echo $numOfStims; ?>; // stores total number of stims in database
          
          // to access web console: inspect element in web browser then click console to view the below messages!!
          //console.log(stims); // throws stims object to web console for testing
          //console.log("Successfully loaded stimuli data!!!!!!!"); // confirmation message
        }

        function getNextComparison(index)
        {
          if(stims[index].stimType == "sound")
          {
            //console.log("Sound stim detected, stimID:", stims[index].stimID);
            fileName += stims[index].stimID + ".wav"; 
            sound_stim1
            //console.log("Got stim file: ", fileName);
          }
          else stimType == "image"
          {
            //console.log("Image stim detected, stimID:", stims[index].stimID);
            fileName += stims[index].stimID + ".png"; 
            //console.log("Got stim file: ", fileName);
          }
        }

        function helpToolTip()
        {
          if(questionHelpPrompt.style.display == "none")
            questionHelpPrompt.style.display = "flex";
          else // questionHelpPrompt.style.display == "flex"
            questionHelpPrompt.style.display = "none"; 
        }


    </script>
  </head>
  <style>
    #imgtoimgBody
    {
      background-color: white;
      margin-top: 20%;
      margin-bottom: 20%;
      display: flex;
      margin-left: auto;
      margin-right: auto;
      justify-content: center;
    }

    #image
    {
      position: fixed; /* or absolute */
      top: 50%;
      left: 50%;
    }

    #pressButton
    {
      display: flex;
      vertical-align: center;
      width: 19pc;
      margin-left: auto;
      margin-right: auto;
      justify-content: center;
    }
  </style>
  <body onload = "onLoad()">
    <img id="questionHelpButton" src="./images/questionHelpButton.png" width="50" height="50">
    <br>
    <div id="questionHelpPrompt">Insert question help here:<br>Line 2 <br>Line 3 <br></div>

    <div>
      <img id="image_stim1">
      <img id="image_stim2">
    </div>

    <audio>
      <source id="sound_stim1">
      <source id="sound_stim2">
    </audio>



    <p id="arrayData"></p>
  </body>
</html>
