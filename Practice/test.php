<h1>test</h1>

<?php
<!-- only php code can write here -->
$name = "Shihab";
$id = 47592;
$cgpa = 3.00;

//$std = array(1,"shihab",3.00);
$std = [1,"shihab",3.00];
$stds = [
    [1,"shihab",3.00],
    [2,"jamal",2.50],   //index array , 0,1,2
    [3,"karim",3.75],
];
$stds[2][2];

$std = ['id'=>1,'name'=>"shihab",'cgpa'=>3.00]; //associative array ,
//  new type of array, id,name,cgpa are called keys

$std['cgpa'];
$stds = [
's1'=>  ['id'=>1,'name'=>"shihab",'cgpa'=>3.00],
's2'=> ['id'=>2,'name'=>"jamal",'cgpa'=>2.50], // associative array inside associative array
's3'=>  ['id'=>3,'name'=>"karim",'cgpa'=>3.75],
];
$stds[2]['cgpa']; //combined array

function sum($a,$b){
    return $a+$b;
}
sum(10,20); //function with parameter

for($i=1;$i<=10;$i++){
    //loop  
}

if(){
    //condition
}else{
    //else part
}
for($a=1;$a<=10;$a++){
   echo "Joy Bangla"."<br>";
   echo "<h1>Joy Bhangabhondu<h1>"<br>";
   echo "<h1>Joy Bhangabhondu<h1>";
}
echo "Joy Bangla"."Joy Bhangabhondu"; // return nothing and add string
print "Joy Bangla",; // return 1 or true

$obj-> // property access method


?>

<h2 style="color:red" onclick="alert('test')"> <?php if($name !==""){echo $name;}else{echo "null";}?> </h2> 


<h1>abc</h1>