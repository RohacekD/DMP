<?php
require_once 'Settings.class.php';

class Database{
	/**
	 * 
	 * Odkaz na pripojeni k DB.
	 * @var PDO $connection
	 */
	public static $connection;
	
    
    public function __construct(){
    	self::pripoj();
    }

    private static function pripoj() {
        if (!isset(self::$spojeni)) {
        	try{
            	self::$connection = @new PDO("mysql:host=".settings::$DBconn['host'].";dbname=".settings::$DBconn['dbname'],settings::$DBconn['user'],settings::$DBconn['pass'],Settings::$DBsettings);
        	}
        	Catch(PDOException $Exception){
		   		if( (int)$Exception->getCode()==1049){

		   			header("Location: ".settings::$url.settings::$url_base."/error/Neexistujici-DB");
		   			exit();
		   		}
	   		}
        }
    }
    
    public static function OneRowQuery($query, $parameters = array()){
    	$result = self::$connection->prepare($query);
    	$result->execute($parameters);
    	return $result->fetch();
    }
    
    /**
     * 
     * Dotaze se na DB a vrati vsechny vybrane radky. Rozhodne jeslti parametr neni int aby fungovalo strankovani.
     * @bug PDO nepodporuje LIMITY z nejakeho duvodu tam da uvozovky pokud nebindnete parametr s parametrem PDO::PARAM_INT
     * @param string $query SQL dotaz
     * @param array $parameters Parametry pro dotaz
     */
	public static function Query($query, $parameters = array()) {
    	$result = self::$connection->prepare($query);
    	$i=1;
    	foreach ($parameters as $val) {
    		if(is_numeric($val)) {
    			$result->bindValue($i, $val, PDO::PARAM_INT);
    		}
    		else {
    			$result->bindValue($i, $val);
    		}
    			
    		$i++;
    	}
    	$result->execute();
    	return $result->fetchAll();
	}

	public static function Insert($query, $parameters = array()) {
    	$result = self::$connection->prepare($query);
    	$result->execute($parameters);
    	return self::$connection->lastInsertId();
	}
	
	public static function Update($query, $parameters = array()) {
    	$result = self::$connection->prepare($query);
    	$result->execute($parameters);
	}
	
	public static function DeleteOne($query, $parameters = array()){
    	$result = self::$connection->prepare($query." LIMIT 1;");
    	$result->execute($parameters);
	}
	
	/**
	 * 
	 * Returns date in our format
	 * @param string $date
	 * @return string Date
	 */
	public static function ToOurDate($date){
		$datetime = strtotime($date);
		return date("d. m. Y G:i", $datetime);
	}
}