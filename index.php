<?php
// By Akiko Koyama
require "lib.php";

display_html_head("Algebraic Expressions");
$degree = 2;  // the degree of an expression. Just change this degree and the whole program works based on the 
display_form($degree);
display_html_foot();

function display_form($default_degree){
// you must complete this function
// use HEREdoc to display the <label> and <input> and <form>
    $default_degree=$_POST['degree']??$default_degree;
    $form= <<<FORM
    <p>Select the degree of the expression:</p>
    <form method="post" action="$_SERVER[PHP_SELF]">
    <label for="degree">Degree:</label>
    <input type="number" name="degree" value="$default_degree" min=0>
    <input type="submit" value="Generate">
    </form>
    FORM;
    if('POST'==$_SERVER['REQUEST_METHOD']){
        if(trim($_POST['degree'])!=""){
            echo $form;
            process_form($_POST['degree']);
        }else{
            echo $form;
            echo "<p style='color:red'>&nbspPlease enter a number ! <p>";
        }
    }else{
        echo $form;
    }
}
function process_form($degree){
    $expr=new Expression($degree);
    $expr->generate();
    $expr_str=$expr->stringify();
    $expr_simpl=$expr->simplify($expr);
    $expr_str_simpl=$expr_simpl->stringify();
    display_expression("The Generated Expression:", $expr_str);
    display_expression("Its Simplified Version:",  $expr_str_simpl);
}
function display_expression($caption, $expr_str)
{
    print <<<_EXPR
    <div>
    <label>$caption</label><span>$expr_str</span><br />
    </div>
    _EXPR;
}

// stringifies only one term
function stringify_term($exponent)
{
    $term = "";
    if ($exponent == 1)
        $term .= "x";
    else if ($exponent > 1)
        $term .= "x" . "<sup>$exponent</sup>";
    return $term;
}

class Term{
    public $coefficient;
    public $exponent;
}
class Expression{
    const COEFFICIENT_MIN=-20;
    const COEFFICIENT_MAX=20;
    public $terms;  //single dimension array of Term objects 
    public $degree;  //int
    public $num_of_terms;  //int
    //constructor
    public function __construct($degree){
        $this->degree=$degree;
        $this->num_of_terms=2 * $degree + 3;  
        $this->terms=array();
    }
    //4 methods
    function generate(){
        $excluded = [0];
        do {
            $this->terms=[];
            for ($i = 0; $i < $this->num_of_terms; $i++) {
                 $aTerm=new Term();
                do {
                    $aTerm->coefficient = rand(self::COEFFICIENT_MIN, self::COEFFICIENT_MAX);  
                } while (in_array($aTerm->coefficient, $excluded));
                $aTerm->exponent = rand(0, $this->degree);
                $this->terms[$i]=$aTerm;
            }
        } while (!$this->meets_requirements());  // re-generate expression if it does not meet the requirement
        return $this->terms;    
    }
    function meets_requirements(){
        $isValid = false;
        for ($deg = $this->degree; $deg >= 0; $deg--) {
            $counter = 0;
            for ($i = 0; $i < $this->num_of_terms; $i++) {
                if ($this->terms[$i]->exponent == $deg) {
                    $counter++;
                    if ($counter == 2) {
                        $isValid = true;
                        break;
                    }
                }
            }
            if (!$isValid) {
                break;
            }
        }
        return $isValid;
    }
    function stringify(){
        $expression = "";
        // strigify the first term as it is different that the other terms- the sign (- or +) is connected to the number, also if there is only 
        // one term, no + or - sign is needed after 
        if ($this->terms[0]->coefficient != 0) {
            if ($this->terms[0]->exponent == 0) {
                $expression .= $this->terms[0]->coefficient;
            } else if ($this->terms[0]->coefficient == 1) {
                $expression .= stringify_term($this->terms[0]->exponent);;
            } else if ($this->terms[0]->coefficient == -1) {
                $expression .= "-" . stringify_term($this->terms[0]->exponent);;
            } else {
                $expression .= $this->terms[0]->coefficient . stringify_term($this->terms[0]->exponent);
            }
        }
    // strigify the rest of the terms
        for ($i = 1; $i < count($this->terms); $i++) {
            if ($this->terms[$i]->coefficient == 0) {
                continue;
            } else if ($this->terms[$i]->coefficient < 0) {
                $expression .= " - ";
            } else if ($this->terms[$i]->coefficient  > 0) {
                $expression .= " + ";
            }
            if ($this->terms[$i]->exponent == 0) {
                $expression .= abs($this->terms[$i]->coefficient);
            } else if (abs($this->terms[$i]->coefficient) == 1) {
                $expression .= stringify_term($this->terms[$i]->exponent);;
            } else {
                $expression .= abs($this->terms[$i]->coefficient) . stringify_term($this->terms[$i]->exponent);
            }
        }
        return $expression;  
    }
    public static function simplify(Expression $expr){
        $deg=$expr->degree;
        $simpl=new Expression($deg); 
        for($i=0; $i<=$deg; $i++){
            $sum=0;
            for($x=0; $x<sizeof($expr->terms); $x++){
                if($expr->terms[$x]->exponent==$i){
                    $sum +=$expr->terms[$x]->coefficient;
                }
            }
            if($sum!=0){
                $newTerm=new Term();
                $newTerm->exponent=$i;
                $newTerm->coefficient=$sum;
                array_unshift($simpl->terms,$newTerm);
            }
        }
    $simpl->num_of_terms=sizeof($simpl->terms);    
    return $simpl;        
    }
}




