<?php

    //ini_set('auto_detect_line_endings',TRUE);
    ini_set('display_errors', true);

    include_once('classes.php');
    include_once('config.php');
    include_once('data.php');

    // Open database connection
    $conn = new mysqli(HOST, USER, PASS, DTBS);
    if ($conn->connect_error) {
        die("Connection to database failed: " . $conn->connect_error);
    }

    function detect_separator($file) {
        $separators = [',', ';'];
        $n = 0;
        $d = $separators[0];

        foreach ($separators as $s) {
            $f = fgetcsv($file, 10000, $s);
            if (sizeof($f) > $n) {
                $d = $s;
                $n = sizeof($f);
            }
        }

        return $d;
    }

    //Getting all lists
    $operational_units = [];
    $divisions = [];
    $advisors = [];
    $definitions = [];
    $competences = ["Bradford"];
    $competence_values = [];
    $hr_competences = [];
    $customers = [];
    $roles = [];

    // Relations
    $opunits_divisions = [];
    $advisor_division = [];
    $divisions_customers = [];


    // Upload Advisor
    if (isset($_POST['btnUploadAdvisors'])) {

        ini_set('auto_detect_line_endings', true);
        $file_name = $_FILES['file_advisors']['tmp_name'];
        $file = fopen($file_name, 'r');

        if ($_FILES['file_advisors']['size'] <= 0) {
            echo "Error";
            return;
        }

        mysqli_query($conn, "DELETE FROM Advisors");
        while ( ($column = fgetcsv($file, 10000, ";")) != false) {
            // advisors
            $mwd_id = $column[1];
            $firstname = $column[2];
            $lastname = $column[3];
            $employer = $column[5];
            $startdate = $column[6];
            $functioncode = $column[7];
            $functiondescription = $column[8];
            //$manager = $column[9];
            //$advisors[] = new Advisor($mwd_id, $firstname, $lastname, $manager);

            $stmt = $conn->prepare("INSERT INTO advisors (ID, firstname, lastname, Employer, Startdate, functioncode, functiondescription, manager) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            if (!$stmt) {
                die("Statement failed (" . $stmt->errno . "): " . $conn->error);
            }
            $stmt->bind_param("issssisi", $ID_, $firstname_, $lastname_, $employer_, $startdate_, $functioncode_, $functiondescription_, $manager_);
            $ID_ = $mwd_id;
            $firstname_ = $firstname;
            $lastname_ = $lastname;
            $employer_ = $employer;
            $startdate_ = $startdate;
            $functioncode_ = $functioncode;
            $functiondescription_ = $functiondescription;
            $manager_ = $manager;
            if(!$stmt->execute()) {
                echo $stmt->errno . ": " . $conn->error . "<br>";
            }
            $stmt->close();
        }

        // Linking relations
        $file = fopen($file_name, 'r');
        while ( ($column = fgetcsv($file, 10000, ";")) != false) {




            // Advisor to division
            $advisor_id = $column[1];



            $stmt = $conn->prepare("UPDATE advisors set EmployerDivision=(SELECT ID from divisions WHERE name=?) where ID=?");
            if (!$stmt) {
                die("Statement failed (" . $stmt->errno . "): " . $conn->error);
            }
            $stmt->bind_param("si", $division_, $id_);
            $division_ = $column[21];
            $id_ = $advisor_id;
            $stmt->execute();
            $stmt->close();


        }
    }




    // Upload managers
    if (isset($_POST['btnUploadManagers'])) {
        ini_set('auto_detect_line_endings', true);
        $file_name = $_FILES['file_managers']['tmp_name'];
        $file = fopen($file_name, 'r');

        if ($_FILES['file_managers']['size'] <= 0) {
            echo "Error";
            return;
        }

        //mysqli_query($conn, "DELETE FROM Advisors");
        while ( ($column = fgetcsv($file, 10000, ";")) != false) {
            $adv = $column[0];
            $mgr = $column[1];
            $period = $column[3];

            $stmt = $conn->prepare("INSERT INTO advisor_manager (advisor_id, manager_id, period) VALUES (?, ?, ?)");
            if (!$stmt) {
                die("Statement failed (" . $stmt->errno . "): " . $conn->error);
            }
            $stmt->bind_param("iis", $advisor_, $manager_, $period_);
            $advisor_ = $adv;
            $manager_ = $mgr;
            $period_ = $period;
            $stmt->execute();
            $stmt->close();


        }

    }






    // Upload customer_advisor
    if (isset($_POST['btnUploadCustomerAdvisor'])) {
        ini_set('auto_detect_line_endings', true);
        $file_name = $_FILES['file_customer_advisor']['tmp_name'];
        $file = fopen($file_name, 'r');

        if ($_FILES['file_customer_advisor']['size'] <= 0) {
            echo "Error";
            return;
        }

        //mysqli_query($conn, "DELETE FROM Advisors");
        while ( ($column = fgetcsv($file, 10000, ";")) != false) {
            $adv = $column[0];
            $customer = $column[6];
            $period = $column[5];

            $stmt = $conn->prepare("INSERT INTO advisors_customers (advisor_id, customer_id, period) VALUES (?, ?, ?)");
            if (!$stmt) {
                die("Statement failed (" . $stmt->errno . "): " . $conn->error);
            }
            $stmt->bind_param("iis", $advisor_, $customer_, $period_);
            $advisor_ = $adv;
            $customer_ = $customer;
            $period_ = $period;
            $stmt->execute();
            $stmt->close();
        }

    }




    // Upload CRMs
    if (isset($_POST['btnUploadCrms'])) {
        ini_set('auto_detect_line_endings', true);
        $file_name = $_FILES['file_crm']['tmp_name'];
        $file = fopen($file_name, 'r');

        if ($_FILES['file_crm']['size'] <= 0) {
            echo "Error";
            return;
        }

        //mysqli_query($conn, "DELETE FROM Advisors");
        while ( ($column = fgetcsv($file, 10000, ";")) != false) {
            $crm_names = array(
                $column[6],
                $column[7],
                $column[8],
                $column[9],
                $column[10],
                $column[11],
                $column[12],
                $column[13]
            );


            foreach ($crm_names as $crm) {
                if ($crm != "") {
                    $stmt = $conn->prepare("IF NOT EXISTS (SELECT * FROM crms WHERE name=?) INSERT INTO crms (name) VALUES (?)");
                    $stmt->bind_param("ss", $name1_, $name2_);
                    $name1_ = $crm;
                    $name2_ = $crm;
                    $stmt->execute();
                    $stmt->close();
                }
            }

        }

        // Relations
        $file = fopen($file_name, 'r');
        while ( ($column = fgetcsv($file, 10000, ";")) != false) {
            // CRM to opunit
            $crm_names = array(
                $column[6],
                $column[7],
                $column[8],
                $column[9],
                $column[10],
                $column[11],
                $column[12],
                $column[13]
            );

            $opunit_id = strToId($column[3], "operationalunits", "name");

            foreach ($crm_names as $crm) {
                $stmt = $conn->prepare("REPLACE INTO operationalunits_crms (opunit_id, crm_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $opunit_, $crm_);
                $opunit_ = $opunit_id;
                $crm_id = strToId($crm, "crms", "name");
                $stmt->execute();
                $stmt->close();

                $stmt = $conn->prepare("REPLACE INTO advisor_crm (advisor_id, crm_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $advisor_, $crm_);
                $advisor_ = $column[0];
                $crm_id = strToId($crm, "crms", "name");
                $stmt->execute();
                $stmt->close();
            }
        }
    }



    // Upload competences, definitions,...
    if (isset($_POST['btnUploadData'])) {
        ini_set('auto_detect_line_endings', true);
        $file_name = $_FILES['file2']['tmp_name'];
        $file = fopen($file_name, 'r');


        if ($_FILES['file2']['size'] <= 0) {
            echo "Error";
            return;
        }

        while ( ($column = fgetcsv($file, 10000, ";")) != false) {

            $operational_unit = $column[0];
            if (!in_array($operational_unit, $operational_units)) {
                $operational_units[] = $operational_unit;
            }

            $division = $column[2];
            if (!in_array($division, $divisions)) {
                $divisions[] = $division;
            }


            $definition = $column[7];
            if (!in_array($definition, $definitions)) {
                $definitions[] = $definition;
            }

            $competence = $column[4];
            if (!in_array($competence, $competences)) {
                $competences[] = $competence;
            }

            $customer = $column[3];
            if (!in_array($customer, $customers)) {
                $customers[] = $customer;
            }

            $competence_value = $column[5];
            if (!in_array($competence_value, $competence_values)) {
                $competence_values[] = $competence_value;
            }

            $hr_competence = $column[6];
            if (!in_array($hr_competence, $hr_competences)) {
                $hr_competences[] = $hr_competence;
            }

            $role = $column[1];
            if (!in_array($role, $roles)) {
                $roles[] = $role;
            }

        }

        // Filling database
        // Operational Units
        if (sizeof($operational_units) > 0) {
            mysqli_query($conn, "DELETE FROM operationalunits");
            mysqli_query($conn, "ALTER TABLE operationalunits AUTO_INCREMENT = 1");

            foreach ($operational_units as $opunit) {
                $stmt = $conn->prepare("INSERT INTO operationalunits (name) VALUES (?)");
                $stmt->bind_param("s", $opunit_);
                $opunit_ = $opunit;
                $stmt->execute();
                $stmt->close();
            }
        }

        // Divisions
        if (sizeof($divisions) > 0) {
            mysqli_query($conn, "DELETE FROM divisions");
            mysqli_query($conn, "ALTER TABLE divisions AUTO_INCREMENT = 1");

            foreach ($divisions as $division) {
                $stmt = $conn->prepare("INSERT INTO divisions (name) VALUES (?)");
                $stmt->bind_param("s", $division_);
                $division_ = $division;
                $stmt->execute();
                $stmt->close();
            }
        }

        // Customers
        if (sizeof($customers) > 0) {
            mysqli_query($conn, "DELETE FROM customers");
            mysqli_query($conn, "ALTER TABLE customers AUTO_INCREMENT = 1");

            foreach ($customers as $customer) {
                $stmt = $conn->prepare("INSERT INTO customers (name) VALUES (?)");
                $stmt->bind_param("s", $customer_);
                $customer_ = $customer;
                $stmt->execute();
                $stmt->close();
            }
        }

        // Definitions
        if (sizeof($definitions) > 0) {
            mysqli_query($conn, "DELETE FROM definitions");
            mysqli_query($conn, "ALTER TABLE definitions AUTO_INCREMENT = 1");

            foreach ($definitions as $definition) {
                $stmt = $conn->prepare("INSERT INTO definitions (definition) VALUES (?)");
                $stmt->bind_param("s", $definition_);
                $definition_ = $definition;
                $stmt->execute();
                $stmt->close();
            }
        }

        // Competences
        if (sizeof($competences) > 1) {
            mysqli_query($conn, "DELETE FROM competences");
            mysqli_query($conn, "ALTER TABLE competences AUTO_INCREMENT = 1");

            foreach ($competences as $competence) {
                $stmt = $conn->prepare("INSERT INTO competences (competence) VALUES (?)");
                $stmt->bind_param("s", $competence_);
                $competence_ = $competence;
                $stmt->execute();
                $stmt->close();
            }
        }

        // Competence values
        if (sizeof($competence_values) > 0) {
            mysqli_query($conn, "DELETE FROM competencevalues");
            mysqli_query($conn, "ALTER TABLE competencevalues AUTO_INCREMENT = 1");

            foreach ($competence_values as $competence) {
                $stmt = $conn->prepare("INSERT INTO competencevalues (value) VALUES (?)");
                $stmt->bind_param("s", $competence_);
                $competence_ = $competence;
                $stmt->execute();
                $stmt->close();
            }
        }

        // HR Competences
        if (sizeof($hr_competences) > 0) {
            mysqli_query($conn, "DELETE FROM hrcompetences");
            mysqli_query($conn, "ALTER TABLE hrcompetences AUTO_INCREMENT = 1");

            foreach ($hr_competences as $competence) {
                $stmt = $conn->prepare("INSERT INTO hrcompetences (value) VALUES (?)");
                $stmt->bind_param("s", $competence_);
                $competence_ = $competence;
                $stmt->execute();
                $stmt->close();
            }
        }

        // Roles
        if (sizeof($roles) > 0) {
            mysqli_query($conn, "DELETE FROM roles");
            mysqli_query($conn, "ALTER TABLE roles AUTO_INCREMENT = 1");

            foreach ($roles as $role) {
                $stmt = $conn->prepare("INSERT into roles (role_name) VALUES (?)");
                $stmt->bind_param("s", $role_);
                $role_ = $role;
                $stmt->execute();
                $stmt->close();
            }
        }


        // Setting relations
        $file = fopen($file_name, 'r');
        mysqli_query($conn, "DELETE FROM definitiondetails");
        mysqli_query($conn, "ALTER TABLE defintiondetails AUTO_INCREMENT = 1");
        while ( ($column = fgetcsv($file, 10000, ";")) != false) {

            //Opunit to divisions// Opunit to division
            $operational_unit = $column[0];
            $division = $column[2];

            $opunit_id = 0;
            $stmt = $conn->prepare("SELECT ID from operationalunits WHERE name=?");
            $stmt->bind_param("s", $operational_unit_);
            $operational_unit_ = $operational_unit;
            $stmt->execute();
            $stmt->bind_result($id);
            $stmt->fetch();
            $opunit_id = $id;
            $stmt->close();

            $division_id = 0;
            $stmt = $conn->prepare("SELECT ID from divisions WHERE name=?");
            $stmt->bind_param("s", $division_);
            $division_ = $division;
            $stmt->execute();
            $stmt->bind_result($id);
            $stmt->fetch();
            $division_id = $id;
            $stmt->close();

            $relation = array($opunit_id, $division_id);
            if (!in_array($relation, $opunits_divisions)) {
                $opunits_divisions[] = $relation;
            }

            // Division to customer
            $customer = $column[3];

            $division_id = 0;
            $stmt = $conn->prepare("SELECT ID from divisions WHERE name=?");
            $stmt->bind_param("s", $division_);
            $division_ = $division;
            $stmt->execute();
            $stmt->bind_result($id);
            $stmt->fetch();
            $division_id = $id;
            $stmt->close();

            $customer_id = 0;
            $stmt = $conn->prepare("SELECT ID from customers WHERE name=?");
            $stmt->bind_param("s", $customer_);
            $customer_ = $customer;
            $stmt->execute();
            $stmt->bind_result($id);
            $stmt->fetch();
            $customer_id = $id;
            $stmt->close();

            $relation = array($division_id, $customer_id);
            if (!in_array($relation, $divisions_customers)) {
                $divisions_customers[] = $relation;
            }


            // Setting definition details
            $opunit_id = strToId($column[0], "operationalunits", "name");
            $role_id = strToId($column[1], "roles", "role_name");
            $customer_id = strToId($column[3], "customers", "name");
            $competence_id = strToId($column[4], "competences", "competence");
            $competencevalue_id = strToId($column[5], "competencevalues", "value");
            $hrcompetence_id = strToId($column[6], "hrcompetences", "value");
            $definition_id = strToId($column[7], "definitions", "definition");

            $stmt = $conn->prepare("INSERT INTO definitiondetails (definition_id, competence_id, role_id, opsunit_id, customer_id, hrcompetence_id, competencevalues_id) VALUES (?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("iiiiiii", $definition_, $competence_, $role_, $opunit_, $customer_, $hrcompetence_, $competencevalue_);
            $definition_ = $definition_id;
            $competence_ = $competence_id;
            $role_ = $role_id;
            $opunit_ = $opunit_id;
            $customer_ = $customer_id;
            $hrcompetence_ = $hrcompetence_id;
            $competencevalue_ = $competencevalue_id;

            if(!$stmt->execute()) {
                echo $conn->error . " -> " . $opunit_id . " for " . $column[0] . "<br>";
            }

            //echo $column[4] . "\t" . $column[7] . "<br>";

            $stmt->close();
        }
    }


    //Filling database with relations
    // Opunits_divisons
    if (sizeof($opunits_divisions) > 0) {
        mysqli_query($conn, "DELETE FROM operationalunits_divisions");
        mysqli_query($conn, "ALTER TABLE operationalunits_divisions AUTO_INCREMENT = 1");

        foreach ($opunits_divisions as $relation) {
            $stmt = $conn->prepare("INSERT INTO operationalunits_divisions (operationalunit_id, division_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $opunit_, $division_);
            $opunit_ = $relation[0];
            $division_ = $relation[1];
            $stmt->execute();
            $stmt->close();
        }
    }

    // divisions_customers
    if (sizeof($divisions_customers) > 0) {
        mysqli_query($conn, "DELETE FROM divisions_customers");
        mysqli_query($conn, "ALTER TABLE divisions_customers AUTO_INCREMENT = 1");

        foreach ($divisions_customers as $relation) {
            $stmt = $conn->prepare("INSERT INTO divisions_customers (division_id, customer_id) VALUES (?, ?)");
            if (!$stmt) {
                die("Statement failed (" . $stmt->errno . "): " . $conn->error);
            }
            $stmt->bind_param("ii", $division_, $customer_);
            $division_ = $relation[0];
            $customer_ = $relation[1];

            $stmt->execute();
            $stmt->close();
        }
    }

    // Advisors_customers
    // Tijdelijk: Iedereen in divisie werkt voor customers van divisie.
    mysqli_query($conn, "DELETE FROM advisors_customers");
    mysqli_query($conn, "ALTER TABLE advisors_customers AUTO_INCREMENT = 1");
    $stmt = $conn->prepare("SELECT ID FROM advisors");
    $conn2 = new mysqli(HOST, USER, PASS, DTBS);
    $stmt->execute();
    $stmt->bind_result($ID);
    while ($stmt->fetch()) {
        $stmt2 = $conn2->prepare("INSERT INTO advisors_customers (advisor_id, customer_id) VALUES (?, 1)");
        if (!$stmt2) {
            die("Statement failed (" . $stmt2->errno . "): " . $conn->error);
        }
        $stmt2->bind_param("i", $advisor_);
        $advisor_ = $ID;
        $stmt2->execute();
        $stmt2->close();
    }
    $conn2->close();
    $stmt->close();




    $conn->close();

    //header("Location: ../data.php?opunit=" . $_GET['opunit']);
