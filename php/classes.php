<?php

    /*
     * File holding most classes used in this project
     */


    // Operational Unit
    class OperationalUnit {
        public $id = 0;
        public $name = "";

        public function __construct($id, $name) {
            $this->id = $id;
            $this->name = $name;
        }
    }

    // Division
    class Division {
        public $id = 0;
        public $name = "";

        public function __construct($id, $name) {
            $this->id = $id;
            $this->name = $name;
        }
    }

    // Customer
    class Customer {
        public $id = 0;
        public $name = "";

        public function __construct($id, $name) {
            $this->id = $id;
            $this->name = $name;
        }
    }

    // Advisor
    class Advisor {
        public $id = 0;
        public $firstname = "";
        public $lastname = "";
        public $EmployerDivision; // FK
        public $employer;
        public $startDate;
        public $functionCode;
        public $functionDescription;
        public $manager = 0;

        public function __construct($id, $firstname, $lastname, $employer, $startDate, $functionCode, $functionDescription, $manager) {
            $this->id = $id;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            $this->employer = $employer;
            $this->startDate = $startDate;
            $this->functionCode = $functionCode;
            $this->$functionDescription = $functionDescription;
            $this->manager = $manager;
        }
    }

    // Competence
    class Competence {
        public $id = 0;
        public $competence = "";

        public function __construct($id, $competence) {
            $this->id = $id;
            $this->competence = $competence;
        }
    }

    // Definition
    class Definition {
        public $id = 0;
        public $definition = "";
        public $target = "";

        public function __construct($id, $definition) {
            $this->id = $id;
            $this->definition = $definition;
        }
    }

    // role
    class Role {
        public $id = 0;
        public $name = "";

        public function __construct($id, $name) {
            $this->id = $id;
            $this->name = $name;
        }
    }
