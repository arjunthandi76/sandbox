<?php
        class person {

        var $name;              
                public $height;         
                protected $social_insurance;
                public $pinn_number;

                	function __construct($persons_name) {           
                         $this->name = $persons_name;     
$pinn_number=2;       
                	}  
                protected function set_name($new_name) {
                        if ($new_name != "Jimmy Two Guns") {
                                $this->name = strtoupper($new_name);
                        }
                }
                        function get_name() {
                                return $this->name;
                        }

        }




// 'extends' is the keyword that enables inheritance
class employee extends person {
        function __construct($employee_name) {
                $this->set_name($employee_name);

        }

                protected function set_name($new_name) {
                        if ($new_name == "Stefan Sucks") {
                                $this->name = $new_name;
                        }
                }
}


?>