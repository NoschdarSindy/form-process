<?php
class FormProcess {
    public $errors;
	private $items;
	private $criteria;
	
	function __construct(...$items) {
		mb_internal_encoding("UTF-8");
		$this->items = $items;
    }
	
	private function validate($item) {
        $err = array();
        $value = $_POST[$item] ?? $_GET[$item];
        $criteria = $this->criteria[$item];
        foreach($criteria as $critKey => $critVal) {
            if($critKey === 'required' && empty($value)) {
                $err[] = "is required";
            } else if(!empty($value)) {
                switch($critKey) {
                    case 'length':
                        if(is_array($critVal)) {
                            //min and max set
                            if(mb_strlen($value) < $critVal[0] || mb_strlen($value) > $critVal[1]) {
                                $err[] = "must contain between {$critVal[0]} and {$critVal[1]} characters"; 
                            }
                        } else {
                            //max set only
                            if(mb_strlen($value) > $critVal) {
                                $err[] = "must contain a maximum of $critVal characters";
                            }
                        }
                    break;
                    case 'pattern':
                        if(!preg_match($critVal[0], $value)) {
                            $err[] = "may consist of {$critVal[1]} only";
                        }
                    break;
                    case 'function':
                        $result = static::$critVal($value);
                        if($result) {
                            $err[] = $result;
                        }
                    break;
                }
            }
        }
        if(!empty($err)) {
            return "{$criteria['name']} " . self::additive($err) . "!";
        }
        return false;	
	}
	
    private static function email($email) {
        //checks if given string is a valid email address
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "is invalid";
        }
        return false;
    }
	
	//creates additive sentence parts to make error messages more readable. ['a', 'b', 'c'] becomes "a, b and c"
	function additive(array $limbs) {
		return implode(' and ', array_filter([implode(', ', array_slice($limbs, 0, -1)), end($limbs)], 'strlen'));
	}

	//runs the validate function on each item while storing occuring errors
	function start($c) {
		$this->criteria = $c;
		foreach($this->items as $item) {
            $error = self::validate($item);
            if($error) {
                $this->errors[] = $error;
            }
        }
	}
	
	function printErrors() {
		echo "There was something wrong with your request:<ul>";
		foreach($this->errors as $error) {
			echo "<li>".$error."</li>";
		}
		echo "</ul>";
	}
}
?>