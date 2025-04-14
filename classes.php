<?php

// 1. OOP - klasser - varför private och public
//                   - constructor + exceptions   - VALID STATE
// Javascript let a = { name: "Stefan", age: 50, car: "Renault" }
// 2. Seed:a många random produkter
//             why? paging (sida 1,2,3,4,5) - 10 produkter per sida
//             why? paging (sida 1,2,3,4,5) - 3 produkter per sida
 
// 3. Populära produkter : startsidan = 10 mest populära 


// Vi ska bygga ett system som ska hantera personer och deras lön
// Reglerna är: ska ha ett namn och en age
// kan ha bil
// om man är mer än 50 år så har man 300 kr i timmen
// annars 200 kr i timmen
// sen ska man kunna anropa calulcateSalary (hoursWorked) som ska ge en lön

// När vi har  ett fält som vi vill skydda/säkerställa att det är i VALID STATE så ska vi 
// använda oss av private variabler och getters/setters - 
// public, private, protected (specialvariant av private - om man har ARV)

class Employee{
    public $name; // public = alla kan se och ändra
    private $age; // private = endast synlig inifrån klassen
    private $car; 

    function getCar(){ // getter = hämta värde på en privat variabel
        return $this->car;
    }

    function setCar($car){ // setter = sätta värde på en privat variabel
        if(strlen($car) > 8){
            throw new Exception("Bilen kan inte vara längre än 8 tecken!");
        } else {
            $this->car = $car;
        }
    }

    function __construct($name, $age){ // constructor = initiera saker
        $this->name = $name; // $this = den aktuella instansen av klassen
        $this->setAge($age);
        //$this->age = $age;
    }

   function calulcateSalary($hoursWorked){
        if($this->age > 50){
            return $hoursWorked * 300;
        } else {
            return $hoursWorked * 200;
        }
    }

    function getAge(){ // getter = hämta värde på en privat variabel
        return $this->age;
    }
    function setAge($age){ // setter = sätta värde på en privat variabel
        if($age < 0){
            throw new Exception("Ålder kan inte vara negativ!");
        } else {
            $this->age = $age;
        }
    }

}
$employee = new Employee("Stefan", -50); // $employee är en instans av klassen Employee
echo $employee->getAge(); // -50

//$employee->car = "Renault";
$employee->setCar("Renault");
echo $employee->getCar(); 
$employee->setCar("");

try{
    $employee->setCar("LAMBORGHINI"); // max 8 tecken
} catch (Exception $e) {
    echo "Fel: " . $e->getMessage(); // Bilen kan inte vara längre än 8 tecken!
}


// nu är $employee i VALID STATE
// $employee->name = "Stefan";
// HELLRE ATT PROGRAMMET KRASCHAR ÄN ATT VI HAR EN INVALID STATE 
$employee->setAge(-50); 
// $employee->age = 50;
echo $employee->name; // Stefan
echo $employee->getAge(); // 50


//$employee->age = -20; // -20 är inte VALID STATE

// $employee är i INVALID STATE
$salary = $employee->calulcateSalary(40); 















// SKAPA SEPARATA KLASSER FÖR VARJE FEL SOM KAN INTRÄFFA

class NotEnoughBalanceException extends Exception { 
}
class TooLargeDepositException extends Exception { 
}



class BankAccount{
    public $saldo;

    function __construct(){
        $this->saldo = 0;
    }

    function deposit($amount){
        $this->saldo = $this->saldo + $amount;
    }
    function withdraw($amount){
        if($amount > $this->saldo){
            throw new NotEnoughBalanceException("Belopp större än saldo");
        } 
        if($amount > 3000){
            throw new TooLargeDepositException("Belopp större än 3 000 kr");
        }
        $this->saldo = $this->saldo - $amount;

    }
};

$bankAccount = new BankAccount();
$bankAccount->deposit(5000);
try{    
    $bankAccount->withdraw(6000);
} catch (NotEnoughBalanceException $e) {
    echo "Inte tillräckligt med pengar på kontot!";
} catch (TooLargeDepositException $e) {
    echo "För stort belopp!";
} catch (Exception $e) {
    echo "Något annat fel inträffade!";
} 

var_dump($bankAccount->saldo); 


// ni får se ::


// måndagsexemplar 

// Det finns X antal möjliga värden
// weekday, month, houseType (radhus,villa,lägenhet)
// playerType (forward,defence,goalie)
// Räkna upp alla möjliga värden  = ENUM (enumeration)

enum Color{
    case Red;
    case Green;
    case Blue;
    case Yellow;
    case Black;
    case White;
}

enum Weekday {
    case Monday;
    case Tuesday;
    case Wednesday;
    case Thursday;
    case Friday;
    case Saturday;
    case Sunday;
}

// enumnamn :: värde   


class House{
    public $color;
    public $year;

    public $createdWeekday; // vilken veckodag skapades huset? 

    public $totalSpent; // hur mycket har vi spenderat på renovering
    
    // OOP funktioner ligga inuti klass = metod

    public function isGoodHouse(){
        if($this->createdWeekday == Weekday::Monday){
            return false;      
        }   else {
            return true;
        }
    }


    // MENINGEN MED EN CONSTUCTOR ÄR
    // - initiera saker
    // - se till att VALID STATE gäller 
    //             ( mandatory variabeler som måste finnas)
    //                     REQUIRED
    public function __construct($color, $year, $createdWeekday) {   
        $this->color = $color;
        $this->year = $year;
        $this->createdWeekday = $createdWeekday;
        $this->totalSpent = 0;
    }

    function paint($color){
        $this->color = $color;
        $this->totalSpent = $this->totalSpent + 5000;
    }

}
// Alla metoder är funktioner, men alla funktioner är inte metoder? 
// Lite som att alla kvadrater är rektanglar, men alla rektanglar är inte kvadrater?

// PHP är -> istäälet för .   // MAGIC STRINGS
$stefansHus = new House(Color::Black,1978, Weekday::Monday); // Jag har egna variabler
$stefansHus->year = 1978;
$stefansHus->paint("red");
if($stefansHus->isGoodHouse() == false){
    echo "Detta är inte ett bra hus!";
} else {
    echo "Detta är ett bra hus!";
}

$annasHus = new House(Color::White, 1980, Weekday::Sunday); // Anna har egna variablera
if($annasHus->isGoodHouse() == false){
    echo "Detta är inte ett bra hus!";
} else {
    echo "Detta är ett bra hus!";
}



















class Garage{ // 
    public $color;

    public $totalSpent; // hur mycket har vi spenderat på renovering
    
    // OOP funktioner ligga inuti klass = metod


    // MENINGEN MED EN CONSTUCTOR ÄR
    // - initiera saker
    // - se till att VALID STATE gäller 
    //             ( mandatory variabeler som måste finnas)
    //                     REQUIRED
    public function __construct($color) {   
        $this->color = $color;
        $this->totalSpent = 0;
    }

    function paint($color){
        $this->color = $color;
        $this->totalSpent = $this->totalSpent + 3000;
    }

}



$stefansGarage = new Garage("Vitt");
$stefansGarage->paint("green");



//$annasHus->year = 1980;

// paintGarage($stefansGarage,"red");

// // FUNKTIONSORIENTERAD KOD
// paint($stefansHus, "blue");
// function paint($house, $color){
//     $house->color = $color;
//     $house->totalSpent = $house->totalSpent + 5000;
// }

// function paintGarage($house, $color){
//     $house->color = $color;
//     $house->totalSpent = $house->totalSpent + 3000;
// }



?>