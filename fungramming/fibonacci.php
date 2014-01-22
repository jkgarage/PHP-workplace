<?php

/* My algorithm:
   1. Requirements:
      T, number of test cases, 1 <= T <= 10^5
      The next T lines, each contains integer N, 1 <= N <= 10^10
   2. Analysis : 
      >> Since each N <= 10^10, it never exceed fibonacci element 50th 
         ( fibonacci(49) = 7,778,742,049; fibonacci(50) = 12,586,269,025 )
	  >> step 1 : generate an array of all fibonacci numbers from 1 -> 50, sorted ascending
	  >> step 2 : for each input line, search for the input N in fibonacci array. 
	              Found => "IsFibo", Notfound => "IsNotFibo"
			2a. program the binary search function  - or try PHP array_search()
			2b. for each input line, use 2a. search function
*/

$_fp = fopen("php://stdin", "r");

/* Enter your code here. Read input from STDIN. Print output to STDOUT */

//populate $fibo_array here
$fibo_array = array();
$fibo_array[0] = 0; $fibo_array[1] = 1;
//populate array up to 50 element
$i = 0;
for($i = 0; $i < 49; $i++ ) {
    $fibo_array[$i+2] = $fibo_array[$i] + $fibo_array[$i+1];
}

function isFibo( $N )
{
	global $fibo_array;
	if ( array_search($N, $fibo_array) <> FALSE)
		return "IsFibo";
	else return "IsNotFibo";
}

//echo isFibo( 233 );

fscanf($_fp, "%f\n", $T);
for ($i = 0; $i < $T; $i++ )
{
    fscanf($_fp, "%f\n", $N);
    printf ("%s\n", isFibo( $N ) );
}//for each input line



// //Helper class, support analysis only
// function fibonacci($n) {
// 	//0, 1, 1, 2, 3, 5, 8, 13, 21
// 
// 	/*this is an error condition
// 	   returning -1 is arbitrary - we could
// 	   return anything we want for this
// 	   error condition:
// 	*/
// 	if($n <0) return -1;
// 
// 	if ($n == 0) return 0;
// 
// 	if($n == 1 || $n == 2) return 1;
// 
// 	$int1 = 1;
// 	$int2 = 1;
// 
// 	$fib = 0;
// 
// 	//start from n==3
// 	for($i=1; $i<=$n-2; $i++ )
// 	{
// 		$fib = $int1 + $int2;
// 		//swap the values out:
// 		$int2 = $int1;
// 		$int1 = $fib;
// 	}
// 
// 	return $fib;
// }
// 
// echo number_format( fibonacci(49) );

?>