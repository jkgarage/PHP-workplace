<html>
    <body>
<?php
/* Algorithm:
 * - initial state : empty board
 * - move : 
 *        a) place 1st queen on board, starting from_position=0
 *        b) place next queen on board, starting from_position=0
 *               if cannot find a valid position, 
 *                    return false
 *                    remove previous queen from board
 *                    go back to step a), starting from_position=previous queen previous position + 1
 *               if can find a valid position
 *                    return true
 *                    place the queen on that position
 *                    if queen is the nth (n is size of the board) : return board
 *                    consider next queen, repeat step b)
 * - check valid move :
 *         for each pair of queen [i,j] vs. q[m,n]
 *             i <> m AND j <> n AND j-m <> j-n
 */
$N = 18; //work on 8x8 board first
  
//populate $chess_board here
//initialize the board to empty
$chess_board = array();
for ($i = 0; $i < $N; $i++) {
    $chess_board[$i] = -1;
}

//$filled_up_to : from 0 -> $N-1
function isCurrentChessBoardValid($board, $filled_up_to) {
    for ($i = 0; $i <= $filled_up_to; $i++)
        for ($j = $i+1; $j <= $filled_up_to; $j++)
        {
            if ( $board[$i] == $board[$j] || (abs($i-$j) == abs($board[$i] - $board[$j]) ) )
                return false;
        }
    return true;
}

// nth : from 0 -> $N-1
// pre-requisite : if I place queen nth, 
//                 then queen 1 -> (n-1)th must have been placed on the board
function placeQueen($nth, $from_position) {
    global $chess_board;
    global $N;
    for ($location = $from_position+1; $location < $N; $location++)
    {
        $chess_board[$nth] = $location;
        if ( isCurrentChessBoardValid($chess_board, $nth) ) {
            return true;
        }
        else {
            $chess_board[$nth] = -1;
        }
    }
    return false;
}

function printChessBoard()
{
    global $chess_board;
    global $N;
    for ($i = 0; $i < $N; $i++)
    {
       for ($j = 0; $j < $N; $j++)
           if ($j == $chess_board[$i])
               printf("X|");
           else
               printf("_|");
       print "<br>";
    }
}

$from_position = 0;
$n = 0;
$isSolved = false;
while (!$isSolved)
{
    if ( placeQueen($n, $from_position) )
    {
        if ($n == $N-1)
        {
            $isSolved = true;
            echo 'Solved!';
            var_dump($chess_board);
            printChessBoard();
            break;
        }
        else 
        {
            $from_position = -1;
            $n++;
        }
    }
    else
    {
        $n--;
        if ($n < 0)
        {
            echo 'There is no solution!!!';
            break;
        }
        $from_position = $chess_board[$n];
        $chess_board[$n] = -1;
    }
}

?>

</body>
</html>