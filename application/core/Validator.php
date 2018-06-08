<?php

namespace Core;

/*
|--------------------------------------------------------------------------
| Core : Validator
|--------------------------------------------------------------------------
|
| a class that can validate the request body 
| can be included into controller to be used
|
*/

use Core\Session;

class Validator
{
	
	/*
  |--------------------------------------------------------------------------
  | Setting Properties
  |--------------------------------------------------------------------------
  |
  | - $errorBag = an array of errors validation found
  | 
  */

  private $errorBag = [];
  public $messageBag = [];

	/*
  |--------------------------------------------------------------------------
  | make @params (object) $request | (array) $rules | (array) $messages
  |--------------------------------------------------------------------------
  |
  | - $_flash = an array of flash session on single request
  | 
  */

  public function make($request, $rules, $messages = null)
  {
  	$validator = new Validator();
  	$validator->messageBag = $messages;

  	foreach ($rules as $name => $rule) {
  		$explodedRules[$name] = explode('|', $rule);
  	}

  	foreach ($request as $key => $value) {
  		$value = trim($value);
  		if ( array_key_exists($key, $explodedRules)) {
  			foreach ($explodedRules[$key] as $index => $ruleSet) {
  				if ( strpos($ruleSet, ':') !== false) {
  					$explodedCond = explode(':', $ruleSet);
  					$condName = $explodedCond[0];
  					$condValue = $explodedCond[1];
  				} else {
  					$condName = $ruleSet;
  					$condValue = null;
  				}

  				$validator->$condName($key, $value, $condName, $condValue);
  			}
  		}
  	}

  	if ($validator->fails()){
  		$dummyArr = [];
  		foreach ($validator->errors() as $key => $value) {
  			$dummyArr[$key] = $value;
  		}

  		Session::flash('errors', $dummyArr);
  	}

  	return $validator;
  }

	/*
  |--------------------------------------------------------------------------
  | fails
  |--------------------------------------------------------------------------
  |
  | to check if there is any error in validations
  |	@return (boolean) 
  |
  */

  public function fails()
  {
  	return ( count($this->errorBag) > 0);
  }

  public function passes()
  {
  	return (! count($this->errorBag) > 0);
  }

	/*
  |--------------------------------------------------------------------------
  | errors
  |--------------------------------------------------------------------------
  |
  | retrieves the bag of errors messages
  | @return (array)
  | 
  */

  public function errors()
  {
  	return $this->errorBag;
  }

	/*
  |--------------------------------------------------------------------------
  | min @params (string) $key | (string) $value | (string) $condName | (string) $condValue
  |--------------------------------------------------------------------------
  |
  | a rule of MIN check error if value less than the rule
  | 
  */

  private function min($key, $value, $condName, $condValue)
  {
  	if ($condValue > mb_strlen($value)) {
  		if (! isset($this->errorBag[$key]))
  			$this->errorBag[$key] = [];

  		if (isset($this->messageBag[$key]) && ($this->messageBag[$key][$condName])) {
  			$this->errorBag[$key][$condName] = $this->messageBag[$key][$condName];
  		} else {
  			$this->errorBag[$key][$condName] = "$key must contains more than $condValue characters";
  		}
  	}
  }

	/*
  |--------------------------------------------------------------------------
  | min @params (string) $key | (string) $value | (string) $condName | (string) $condValue
  |--------------------------------------------------------------------------
  |
  | a rule of MAX check error if value more than the rule
  | 
  */

  private function max($key, $value, $condName, $condValue)
  {
  	if ($condValue < mb_strlen($value)) {
  		if (! isset($this->errorBag[$key]))
  			$this->errorBag[$key] = [];

  		if (isset($this->messageBag[$key]) && ($this->messageBag[$key][$condName])) {
  			$this->errorBag[$key][$condName] = $this->messageBag[$key][$condName];
  		} else {
  			$this->errorBag[$key][$condName] = "$key must contains less than $condValue characters";
  		}
  	}
  }

	/*
  |--------------------------------------------------------------------------
  | required @params (string) $key | (string) $value | (string) $condName | (string) $condValue
  |--------------------------------------------------------------------------
  |
  | a rule of REQUIRED check error if value is null or empty
  | 
  */

  private function required($key, $value, $condName, $condValue)
  {
  	if (mb_strlen($value) === 0) {
  		if (! isset($this->errorBag[$key]))
  			$this->errorBag[$key] = [];

  		if (isset($this->messageBag[$key]) && ($this->messageBag[$key][$condName])) {
  			$this->errorBag[$key][$condName] = $this->messageBag[$key][$condName];
  		} else {
  			$this->errorBag[$key][$condName] = "$key is required";
  		}
  	}
  }
}