<?php
    /*
     * File containing variables retrieved from database etc.
     **/

     include_once('config.php');
     include_once('classes.php');

     // Open database connection
     $conn = new mysqli(HOST, USER, PASS, DTBS);
     if ($conn->connect_error) {
         die("Connection to database failed: " . $conn->connect_error);
     }


     // Retrieving all operational units
     $operational_units = [];

     $stmt = $conn->prepare("SELECT ID, name from operationalunits ORDER BY name");
     if (!$stmt) {
         die("Statement failed (" . $stmt->errno . "): " . $conn->error);
     }
     $stmt->execute();
     $stmt->bind_result($id, $name);
     while ($stmt->fetch()) {
         $operational_units[] = new OperationalUnit($id, $name);
     }
     $stmt->close();

     // Retieving all divisions
     $divisions = [];

     $stmt = $conn->prepare("SELECT ID, name FROM divisions WHERE ID IN (SELECT division_id FROM operationalunits_divisions WHERE operationalunit_id=?) ORDER BY name");
     if (!$stmt) {
         die("Statement failed (" . $stmt->errno . "): " . $conn->error);
     }
     $stmt->bind_param("i", $opunit_);
     $opunit_ = $_GET['opunit'];
     $stmt->execute();
     $stmt->bind_result($id, $name);
     while ($stmt->fetch()) {
         $divisions[] = new Division($id, $name);
     }
     $stmt->close();

     // Retrieving all customers
     $customers = [];

     $stmt = $conn->prepare("SELECT ID, name from customers WHERE ID IN (SELECT customer_id FROM divisions_customers WHERE division_id=?) ORDER BY name");
     if (!$stmt) {
         die("Statement failed (" . $stmt->errno . "): " . $conn->error);
     }
     $stmt->bind_param("i", $division_);
     $division_ = $_GET['division'];
     $stmt->execute();
     $stmt->bind_result($id, $name);
     while ($stmt->fetch()) {
         $customers[] = new Customer($id, $name);
     }
     $stmt->close();

     if (isset($_GET['echo']) && $_GET['echo'] == "customers_select_list") {
         $str = "";
         foreach ($customers as $customer) {
             $str .= "<div class='option' data-value='".$customer->id."'>".$customer->name."</div>";
         }

         echo $str;
     }

     // Retrieving all customers of a opunit
     $customers_opunit = [];
     $stmt = $conn->prepare("SELECT ID, name from customers WHERE ID IN (SELECT customer_id FROM divisions_customers WHERE division_id IN (SELECT division_id FROM operationalunits_divisions WHERE operationalunit_id=?)) ORDER BY name");
     if (!$stmt) {
         die("Statement failed (" . $stmt->errno . "): " . $conn->error);
     }
     $stmt->bind_param("i", $opunit_);
     $opunit_ = $_GET['opunit'];
     $stmt->execute();
     $stmt->bind_result($id, $name);
     while ($stmt->fetch()) {
         $customers_opunit[] = new Customer($id, $name);
     }
     $stmt->close();


     // Retrieving all competences
     $competences = [];

     $stmt = $conn->prepare("SELECT ID, competence FROM competences");
     if (!$stmt) {
         die("Statement failed (" . $stmt->errno . "): " . $conn->error);
     }
     $stmt->execute();
     $stmt->bind_result($id, $competence);
     while ($stmt->fetch()) {
         $competences[] = new Competence($id, $competence);
     }
     $stmt->close();

     // Retrieving all months used in buckets
     $months_in_buckets = ["March", "April"];

     $months = array(
      'Jan',
      'Feb',
      'Mar',
      'Apr',
      'May',
      'Jun',
      'Jul',
      'Aug',
      'Sep',
      'Oct',
      'Nov',
      'Dec'
    );

     function str_to_month($str) {

         $str = ucfirst(mb_strtolower($str));

         $short = array(
          'Jan',
          'Feb',
          'Mar',
          'Apr',
          'May',
          'Jun',
          'Jul',
          'Aug',
          'Sep',
          'Oct',
          'Nov',
          'Dec'
        );


        $long = array(
          'January',
          'February',
          'March',
          'April',
          'May',
          'June',
          'July',
          'August',
          'September',
          'October',
          'November',
          'December'
        );

        for ($i = 0; $i < 12; $i++) {
            if ($str == $short[$i] || $str == $long[$i])
                return $i+1;
        }

        return -1;
     }

     // Retrieving all roles
     $roles = [];
     $stmt = $conn->prepare("SELECT id, role_name FROM roles");
     if (!$stmt) {
         die("Statement failed (" . $stmt->errno . "): " . $conn->error);
     }
     $stmt->execute();
     $stmt->bind_result($id, $role);
     while ($stmt->fetch()) {
         $roles[] = new Role($id, $role);
     }
     $stmt->close();

     $conn->close();


     function strToId($str, $table, $col) {
         $allowed_tables = [
             "competences",
             "competencevalues",
             "customers",
             "definitions",
             "hrcompetences",
             "operationalunits",
             "roles"
         ];

         $allowed_columns = [
             "name",
             "role_name",
             "value",
             "competence",
             "definition"
         ];

         if (!in_array($table, $allowed_tables)) {
             return -1;
         }
         if (!in_array($col, $allowed_columns)) {
             return -1;
         }

         $conn = new mysqli(HOST, USER, PASS, DTBS);
         $return = "";
         $sql = "SELECT ID FROM " . $table . " WHERE upper(" . $col . ") = ?";
         $stmt = $conn->prepare($sql);
         $stmt->bind_param("s", $value_);
         $value_ = strtoupper($str);
         $stmt->execute();
         $stmt->bind_result($value);
         $stmt->fetch();
         $return = $value;
         $stmt->close();
         $conn->close();

         return $return;
     }


     function quarterToMonths($q) {
         $arr = [];

         (int)$q = (int)$q * 3;
         $arr[] = $q;
         $arr[] = (int)$q-1;
         $arr[] = (int)$q-2;

         return $arr;
     }

     function monthToQuarter($m) {
         while ($m % 3 != 0) {
             $m = $m + 1;
         }

         return $m / 3;
     }
