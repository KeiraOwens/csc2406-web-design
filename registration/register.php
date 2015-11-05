<?xml version='1.0' encoding='UTF-8'>
<!DOCTYPE HTML PUBLIC "-//W3C/DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title> Registration | Theraphosidae Workshop 2016 | Rio de Janeiro</title>
<!-- Links to the CSS style sheets, separate for browser and print viewing-->
    <link rel="stylesheet" type="text/css" href="../site.css" media="screen" title="Site Style Sheet">
    <link rel="stylesheet" type="text/css" href="../print.css" media="print" title="Site Style Sheet Print version">
    <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon" />
    <link rel="icon" href="../img/favicon.ico" type="image/ico" />

  </head>
  <body>
<!-- Top horizontal navigation bar-->
    <div id="navtop">
      <ul>
        <li><a href="../index.html" class="list-item" title="Return to the home page">Home</a></li>
        <li><a href="../program/program.html" class="list-item" title="List of Workshop Program">Workshop Program</a>
          <ul>
            <li><a href="../papers/papers.php" class="list-item sub-menu"title="Abstract">Abstract Submission</a></li>
          </ul>
        </li>
        <li><a href="registration.html" class="list-item" title="Registration" id="registration">Registration</a></li>
        <li><a href="../accommodation/accommodation.html" class="list-item" title="Main Accommodation Page">Accommodation</a>
          <ul>
            <li><a href="../accommodation/partnerhotels.html" class="list-item sub-menu" title="Workshop Partner Hotels">Partner Hotels</a></li>
            <li><a href="../accommodation/nonpartnerhotels.html" class="list-item sub-menu" title="Other Recommended Hotels (regular booking fees apply)">Recommended Hotels</a></li>
            <li><a href="../accommodation/fulllist.html" class="list-item sub-menu" title="Full list of Recommended Hotels">Hotel Directory</a></li>
          </ul>
        </li>
        <li><a href="../tourism/tourist.html" class="list-item" title="Things to see and Do in Rio de Janeiro">Tourism</a>
          <ul>
            <li><a href="../tourism/siteseeing.html" class="list-item sub-menu" title="Things To see in Rio de Janeiro">Siteseeing</a></li>
            <li><a href="../tourism/events.html" class="list-item sub-menu" title="Things to do in Rio de Janeiro">Things to Do</a></li>
          </ul>
        </li>
        <li><a href="../sitemap.html" class="list-item" title="Site map of all pages (documentation for CSC2406 requirements)">Site Map</a></li>
      </ul>
      <a href="../index.html" title="Return to the home-page"><img src="../img/logo.png" alt="Logo" id="logo" width="200" height="196" /></a><br />
      <h1>17th Intenational Theraphosidae Workshop</h1>
      <p id="subtitle">Rio di Janeiro, Brazil, March 21-25, 2016</p>
      <br />
    </div>

    <h2 id="page-title">2016 Workshop Registration</h2>
  <!-- PHP script to process the form data -->
<?php

# Checking that form data using POST has been sent
    if (array_key_exists('register_page', $_POST)) {
      #tests if input is in POST array
      if(test_input()){
        $trimmed_data = trim_data();
        if(validate_data($trimmed_data)) {
          store_data($trimmed_data);
          success_alert();
        }
      }
    } else {
        error_message("<p>Unfortunately, An error has occured and the form was not processed correctly.");
    }


?>
    <!-- Secondary footer navigation -->
        <div id="navfoot">
          <a href="../index.html" title="Back to the Homepage"> Home </a>
          <a href="../program/program.html" title="List of Program">Workshop Program</a>
          <a href="registration.html" title="Current Page (registration)">Registration</a>
          <a href="../program/accommodation.html" title="Hotels in Rio de Janeiro">Accommodation</a>
          <a href="../tourism/tourist.html" title="Things to see and do in Rio de Janeiro">Tourism</a>
          <a href="../sitemap.html" title="Site map of all pages (documentation for CSC2406 requirements)"> Site Map</a>
        </div>
      </body>
    </html>
<?php

/*Function to test whether data for the is in the $_POST super global array
  @param: none
  @return: Boolean value returns true if all input is present
*/
  function test_input() {
    foreach ($_POST as $key => $value) {
      if (empty($value)) {
        error_message("The {$key} information is missing.\n");
        return false;
      }
    }
#Special condition for those with default values
    if($_POST['Card_number'] == "Please enter digits only (no spaces)") {
      error_message("The Credit Card number is missing\n");
      return  false;
    }

    if($_POST['Expiry_Year'] == "yyyy") {
      error_message("The Credit Card Expiry information is missing\n");
      return false;
    }

  return true;
  }

  /*  Function to trim whitespace from start/end of strings
    @param: none
    @return: $data an array of formatted input data
  */
  function trim_data(){
    $data = $_POST;
    foreach ($data as $entry) {
      $entry = trim($entry);
      #sanitise data of html special characters
      $entry = htmlspecialchars($entry);
    }
    return $data;
  }

/*  Function to check to ensure data is in the correct format
  @param: $data array of form data; trimmed of whitespace
  @return: boolean value returns true if all input data is correctly formatted
*/
  function validate_data($data) {
#check that name data only contains alphabet
#Using str_replace to remove whitespace before checking characters
  if (!ctype_alpha(str_replace(' ', '',$data['Name']))) {
    error_message("<strong>Name:</strong>The name entered has invalid characters; names can only contain letters and spaces.
    Please check and try again\n");
    return  false;
  }
#Check that email address is in a valid format
  if (!filter_var($data['Email'], FILTER_VALIDATE_EMAIL)) {
    error_message("<strong>Email: </strong>The email address supplied is not valid.\n");
    return  false;
  }

#check for valid format Using str_replace to remove the whitespace before checking characters
  if (!ctype_alpha(str_replace(' ', '',$data['Institute_Name']))) {
    error_message("<strong>Institution Name:</strong>The Institution name can only contain letters. Please check and try again");
    return  false;
  }

#check for valid Credit Card number
  if (!is_numeric($data['Card_number'])) {
    error_message("<strong>Card Number: </strong>The card number must contain 16 digits only with no spaces or dashes.\n");
    return  false;
  }
  if (strlen($data['Card_number']) != 16) {
    error_message("<strong>Card Number:</strong>The card number must be 16 digits exactly with no spaces or dashes.\n");
    return false;
  }
#using the current date to check if a CC with an expiry of the current year has passed
  date_default_timezone_set('UTC');
  $current_year = date('Y');
  $current_month = date('m');

#check for valid exp year
  if(!is_numeric($data['Expiry_Year'])) {
    error_message("<strong>Card Expiry date:</strong> The Credit Card Expiry Date is invalid. Please try again\n");
    return false;
  }
#checking that the year input is the correct 4 digits format
  if(strlen($data['Expiry_Year'])!= 4) {
    error_message("<strong>Card Expiry:</strong> The Credit Card Expiry date is in the wrong format. Please enter 4 digits for the year\n");
    return false;
  }
#Checking that the Expiry date is not earlier than the current year
  if($data['Expiry_Year'] < $current_year) {
    error_message("<strong>Card Expiry:</strong> The Credit Card has expired. Please input correct expiry date, or try another payment method.\n");
    return false;
  }

#check for valid exp month
  if ($data['Expiry_Year'] == $current_year) {
    if ($data['expmonth'] < $current_month) {
      error_message("<strong>Credit Card Expiry:</strong> The credit card entered has expired.\n");
      return false;
    }
  }

  return true;
}

/* Function outputs a message for the user to indicate the registration was successful
  @param: none
  @return: none
*/
  function success_alert() {
    print<<<EOT
      <div class="section top">\n<h2>Registration Successful</h2>\n<img src="../img/tarantula.jpg" class="rounded"/>
    <p> You have successfully registered for the 17th Intenational Theraphosidae Workshop
    in Rio di Janeiro, 2016<br/><br/><a class="button" href="../index.html">Return Home</a></p>\n</div>
EOT;
  }
  /* Function outputs a message for the user to indicate the registration was unsuccessful
    @param: $msg string containing reason for failure
    @return: none
  */
  function error_message($msg) {
    print <<<EOT
    <div class="section top">\n<h2>Registration Unsuccessful</h2>\n<img src="../img/tarantula.jpg" class="rounded" alt="Image of tarantula" id="spider" width="250" height="187"/>\n
    <p>{$msg}<br/>  No data has been stored. Please try again\n
    <a class="button" href="registration.html" title="Please click here to refill form">
    Registration page</a></p>\n</div>
EOT;
  }
/* Function to store payment data in a CSV text file
  Each registration is stored on a single line
  @param: none
  @return: none
*/
  function store_data($data) {
    $filename = "payment.csv";
    #Check to ensure the file is available and able to be written to
    if (file_exists($filename) && is_writable($filename)){
      $fp = fopen($filename, "a"); #file handler set to append
      unset($data['register_page']);
      fputcsv($fp, $data);
      fclose($fp);
    }
  }

?>
