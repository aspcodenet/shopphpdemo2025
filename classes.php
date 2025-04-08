<?php

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