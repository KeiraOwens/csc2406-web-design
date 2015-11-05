<?php
if(array_key_exists('form_submit', $_POST)) {
  #If the form has been submitted
    display_header("Abstract Submission");
    $data = trim_data();
    if(test_input($data)){
      if(validate_data($data)) {
        store_data($data);
        success_alert();
      }
  }
}  elseif (array_key_exists('list', $_GET)) {
#If the link to papers has been selected
  display_header("List of Submitted Papers");
  display_list();
} elseif (array_key_exists('abstract', $_GET)) {
    $filename = trim($_GET['abstract']);

    #sanitises data before used to reduce chance of script injection
    $sanitised_fn = htmlentities($filename);
    display_abstract($sanitised_fn);

} else {
    display_header("Abstract Submission and Directory");
    display_form();
}
include_once("footer.php");


/* Function to output the header of a page
  @param: $pagetitle string containing the name to go in the browser
  @return: none
*/
  function display_header($pagetitle) {
    $header = file("header.txt");
    #Searches for the title placeholder and replaces it with $pagetitle
    #$title_key = array_search("%TITLE%", $header);
    $header = str_replace("%TITLE%",$pagetitle, $header);

    foreach ($header as $hline) {
      print $hline;
    }

  }

  function display_form() {
    $body = <<<EOT
<div class="section"> <h2>Abstract Submission and Directory for 2016 Workshop</h2>\n
<img src="../img/tarantula.jpg" class="rounded"/>
<p>A number of abstracts will be uploaded as new presenters register for the Workshop for 2016.
To read the abstracts currently available, please click the button below to view a list
of all abstracts currently submitted.
<a href="papers.php?list" class="button" title="Click here for a list of Submitted Papers and Abstracts">List of Papers</a>
<p>A form is located <a href="#form" title="Click to move down to the abstract submission form">below</a> for presenters
wishing to submit their abstracts</a>
  </div>
  <div class="section odd"><h2 id="form">Abstract Submision Form</h2>
  <p style="margin-left: 30%;">Please note, only authors who have registered for the conference can submit an abstract.
  Please ensure you have registered  before submitting an abstract. The registration form is located here:
    <a href="../registration/registration.html" title="registration page">Registration</a><br/><br/>
\n
  <div style="display: flex;">\n
  <form method="POST" accept-charset='UTF-8'>
  Princicpal Author:<input type="text" id="name" size="40" name="Name" class="form_input"/><br/>
  Name(s) of Author(s): <input type="text" id="name_list" size="40" name="Name_List" class="form_input" /><br/>
  Contact Email: <input type="text" id="email" size="40" name="Email"  maxlength="254" class="form_input"/><br/>
<!-- email addresses have a 254 character limit -->
   Paper Title: <input type="text" size="40" name="Paper_title" class="form_input" /><br/>
   Abstract Text:<br/>
   <textarea name="abstract_text" rows="10" cols="5">
   Please Enter Abstract text here...
   </textarea>
   <input type="reset" value="Clear Form"/>
   <input type="submit" name="form_submit" value="Submit Abstract" />
  </form>\n
  </div>\n
EOT;
  print $body;
  }

  function display_list(){
    #read abstract_list.csv and create a page of links to the abstracts
    print<<<EOT
    <p class="navigation"><a href="../index.html">Home</a> &gt;
     <a href="papers.php">Abstract Submission</a> &gt; Abstract List
  <div class="section"> <h2>Abstract Submission and Directory for 2016 Workshop</h2>\n
EOT;
    if($fp = fopen("abstract_list.csv", "r")){
      $abstract_data = fgetcsv($fp);
        #an empty CSV returns NULL but not an error
        if($abstract_data == NULL){
          print <<<EOT
          <img src="../img/tarantula.jpg" class="rounded"/>\n
          <p>No Abstracts have been submitted. Please try again at another time.<br/>
          If you are a registered presenter, please submit your abstract here
          <a href="papers.php" title="Click here to submit your abstract">Abstract Submission</a></p>
EOT;
        } else {
          print <<<EOT
          <img src="../img/tarantula.jpg" class="rounded"/>\n
          <p>The available abstracts are listed below.
          If you are a registered presenter, please submit your abstract here
          <a href="papers.php\#form" title="Click here to submit your abstract">Abstract Submission</a></p>
          <ol>
EOT;
        }
        do {
          $counter = count($abstract_data);
          if ($counter != 5) continue; #skips over any entry that doens't contain all data

      /* CSV are saved as Principal Author Name, email, Title, abstract filename
        Therefore
        [0] = principal author
        [1] = names,
        [2] = email
        [3] = Title
        [4] = abstract filename
      */
      print <<<EOT
      <li><strong>Title:</strong> {$abstract_data[3]}<br/>
        <strong>Authors:</strong> {$abstract_data[1]}<br/>
        <a href="papers.php?abstract={$abstract_data[4]}" title="Link to abstract text"> Link to Text</a></li><br/>
EOT;

      }while($abstract_data = fgetcsv($fp));
      print "</ol>";
    }

  print "</div>";
  }
/*  Function to display the abstract details and text
    @param: $filename the unique filename of the requested
    abstract
    @return: none
*/
  function display_abstract($filename){
    $fp = fopen("abstract_list.csv", "r");
    $abstract_data = fgetcsv($fp);
    do {
      $counter = count($abstract_data);
      if ($counter != 5) continue;

      if ($abstract_data[4]==$filename); break;
  /* CSV are saved as Principal Author Name, email, Title, abstract filename
    Therefore
    [0] = principal author
    [1] = names,
    [2] = email
    [3] = Title
    [4] = abstract filename
  */} while($abstract_data = fgetcsv($fp));
  display_header($abstract_data[3]);
  print <<<EOT
  <p class="navigation"><a href="../index.html">Home</a> &gt;
   <a href="papers.php">Abstract Submission</a> &gt; <a href="papers.php?list">Abstract List</a> &gt; Abstract </p>
  <div class="section even">
  <h2>Abstract: {$abstract_data[3]}</h2>
  <h3>By {$abstract_data[1]}</h3>
  <strong>Principal Author: </strong>{$abstract_data[0]}<br/>
  <strong>Contact email:</strong>{$abstract_data[2]}<br/>
    <strong>Authors:</strong> {$abstract_data[1]}<br/>
    <br/>
    <h3>Abstract text</h3>
    <div class="section odd">
EOT;
    readfile($filename);
    print <<<EOT
    </div><a class="button" href="papers.php?list" title="Return to list of papers">Abstract Directory</a></div>
EOT;
  fclose($fp);
  }

  /*Function to test whether data for the is in the $_POST super global array
    @param: none
    @return: Boolean value returns true if all input is present
  */
  function test_input($data) {
    #Test to ensure that all forms were filled in
    foreach ($data as $key => $value) {
      if (empty($value)) {
        error_message("The {$key} information is missing.\n");
        return false;
      }
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
      #sanitise data by converting html special char to entity
      $entry = htmlspecialchars($entry);
    }
    return $data;
  }


  /*  Function to check to ensure data is in the correct format
    @param: none
    @return: boolean value returns true if all input data is correctly formatted
  */
  function validate_data($data){
  #check that principal author is in correct format
  if (!ctype_alpha(str_replace(' ', '',$data['Name']))) {
    error_message("<strong>Name:</strong>The name entered has invalid characters; names can only contain letters and spaces.
    Please check and try again\n");
    return  false;
  }
  #check that princicpal author is registered
  $fp_reg = fopen("../registration/payment.csv", "r");
  $data_reg = fgetcsv($fp_reg);
  $i = 0;
do {
  $reg_list[$i] = $data_reg[0];
} while($data_reg = fgetcsv($fp_reg));
if(!in_array($data['Name'], $reg_list)){
  error_message('<strong>Principal Author:</strong> The principal author must be registered before submitting an abstract. The registration form is located here:
    <a href="../registration/registration.html" title="registration page">Registration</a>');
    return false;
}


# check that the names are in correct format
  $author_list = explode(",", $data['Name_List']);
  #trim any leading/trailing whitespace
  $author_list = array_map(trim, $author_list);

  foreach ($author_list as $names) {
    if (!ctype_alpha(str_replace(' ', '',$names))) {
      error_message("<strong>Name:</strong>One of the names entered has invalid characters; names can only contain letters and spaces.
      Please check and try again\n");
      return  false;
  }

  if(!in_array($data['Name'], $author_list)) {
    error_message("The princicpal author must be included in the list of all authors. Please check and try again\n");
    return false;
  }

#check that email is valid
if (!filter_var($data['Email'], FILTER_VALIDATE_EMAIL)) {
  error_message("<strong>Email: </strong>The email address supplied is not valid.\n");
  return  false;
}

  }
  return true;
}

  /* Function outputs a message for the user to indicate the submission was successful
    @param: none
    @return: none
  */
  function success_alert() {
    $abstract_title = $_POST['Paper_title'];
    print<<<EOT
    <div class="section top">\n<h2>Abstract successfully</h2>\n
    <img src="../img/tarantula.jpg" class="rounded"/>
    <p> You have successfully submitted your extract titled <em>$abstract_title</em> for the 17th Intenational Theraphosidae Workshop
    in Rio di Janeiro, 2016<br/><br/><a class="button" href="../index.html">Return Home</a></p>\n</div>
EOT;
  }
  /* Function outputs a message for the user to indicate the submission was unsuccessful
    @param: $msg string containing reason for failure
    @return: none
  */
  function error_message($msg) {
    print <<<EOT
    <div class="section top">\n<h2>Abstract Submission Unsuccessful</h2>\n<img src="../img/tarantula.jpg" class="rounded"/>\n
    <p>{$msg}<br/>  No data has been stored. Please try again\n
    <a class="button" href="papers.php" title="Please click here to refill form">
    Submission Page</a></p>\n</div>
EOT;
  }
/* Function to create a unique filename
  @param $prefix string to include in the filename
  @param $dir directory to store files in
  @return string unique filename
*/
  function uniquefn($prefix) {
    $max_files = 100;

    $count = 1;
    do {
      $filename = sprintf("%s_%03u", "$prefix",$count);
      $count++;
      if( $count > $max_files) {
        return NULL;
      }
    } while (file_exists("$filename"));

    return $filename;
  }
/*  Function to store the user input
  @param: $data an array with the stripped and sanitised
  user input
  @return: none
*/
  function store_data($data) {
    #Store abstract in separate text file
    $ab_text = $data['abstract_text'];
    $filename_text = uniquefn("abstract");
    $fp = fopen("abstract_list.csv", "a");
    $fp_text = fopen($filename_text, "x"); #using x ensures it will not overwrite an existing file
    fwrite($fp_text, $ab_text);
    fclose($fp_text);
    #removing unwanted form data before saving to csv
    unset($data['abstract_text']);
    unset($data['form_submit']);
    $data['abstract_link'] = $filename_text;
    fputcsv($fp, $data);
    fclose($fp);
  }
?>
