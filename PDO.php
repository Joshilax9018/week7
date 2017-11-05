<?php

//turn on debugging messages
ini_set('display_errors', 'On');
error_reporting(E_ALL);

class Manage {
    public static function autoload($class) {
        //you can put any file name or directory here
        include $class . '.php';
    }
}


//instantiate the program object
$obj = new main();

class main {

    public function __construct()
    {
        //print_r($_REQUEST);
        //set default page request when no parameters are in URL
        $pageRequest = 'homepage';
        //check if there are parameters
        if(isset($_REQUEST['page'])) {
            //load the type of page the request wants into page request
            $pageRequest = $_REQUEST['page'];

        }
        //instantiate the class that is being requested
        $page = new $pageRequest();


        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $page->get();
        } else {
            //When form Submits the request, Object of the uploadform will be dynamically created
            //this object will call post method inside the uploadform class.
            $page->post();
        }

    }

}


abstract class page {
    protected $html;

    public function __construct()
    {
        $this->html .= '<html>';
        $this->html .= '<link rel="stylesheet" href="styles.css">';
        $this->html .= '<body>';
    }
    public function __destruct()
    {
        $this->html .= '</body></html>';
        stringFunctions::printThis($this->html);
    }

    public function get() {
        echo 'default get message';
    }


    public function post() {
        //print_r($_POST);
    }
}

class homepage extends page {



    //Overwrite the constructor so that we can same the file name
    public function __construct()
    {
        parent::__construct();

    }

    //get function will return form through which user can upload a file on server
    //Post method will call post method from 'uploadform' class
    public function get() {

       $conn = pdoUtil::getDBConnection();
        $statement = $conn->prepare('SELECT * FROM accounts where id < 6');
        $statement->execute();
        /*while($result = $statement->fetch(PDO::FETCH_OBJ)) {
            $results[] = $result;
        }
        print_r($results);*/
	$results=$statement->fetchAll();
	$statement->closeCursor();
	$t = new htmlTable();
	$t->createTable($results);
        stringFunctions::printThis("Total number of Records: ".count($results));

    }

}



/*This class will be used to deliver csv file content
in table form*/
class htmlTable extends page
{

    private $table;

    public function __construct()
    {
        //call the parent constructor,so that html page will be intialize.
        parent::__construct();


    }




/*Note : This below code was refer from /*https://github.com/MilesZhao/live_code7/blob/master/show.php
    */

    public function createTable($result)
    {
       
       
       stringFunctions::printThis("<table border=\"1\"><tr><th>ID</th><th>Email</th><th>First Name</th><th>Last Name</th><th>Phone
       </th><th>Birthday</th><th>Gender</th><th>Password</th></tr>");
       foreach($result as $row)
       {
          //  echo "<tr><td>".$row["id"]."</td><td>".$row["email"]."</td><td>".$row["fname"]."</td><td>".$row["password"]."</td></tr>";   
          stringFunctions::printThis('<tr>');
	  stringFunctions::printThis("<td>".$row["id"]."</td>");
	  stringFunctions::printThis("<td>".$row["email"]."</td>");
	  stringFunctions::printThis("<td>".$row["fname"]."</td>");
	  stringFunctions::printThis("<td>".$row["lname"]."</td>");
	  stringFunctions::printThis("<td>".$row["phone"]."</td>");
	  stringFunctions::printThis("<td>".$row["birthday"]."</td>");
	  stringFunctions::printThis("<td>".$row["gender"]."</td>");
	  stringFunctions::printThis("<td>".$row["password"]."</td>");
	  stringFunctions::printThis('</tr>');



       }




    }


}

/*Class String Function
*/
class stringFunctions{

    //This fution will print HTML page
    public static function printThis($text){
        print($text);
    }

}

//Create one Singleton class
class pdoUtil{

    private $servername = "mysql:dbname=svj28;host=sql2.njit.edu";
    private $username = "svj28";
    private $password = "vlAtaFzRh";
    protected static  $conn;


   //Making constructor Private so that it can be instantiated only once

    private function __construct()
    {
        try {
            self::$conn = new PDO("mysql:host=$this->servername;dbname=svj28", $this->username, $this->password);
            // set the PDO error mode to exception
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected successfully <br>";
        }
        catch(PDOException $e)
        {
            echo "Connection failed: " . $e->getMessage();
        }
    }


    public static function getDBConnection(){
        if(!self::$conn){
            new pdoUtil();
        }
        return self::$conn;
    }


}


?>
